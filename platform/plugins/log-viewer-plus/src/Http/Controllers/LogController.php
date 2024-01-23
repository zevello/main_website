<?php

namespace ArchiElite\LogViewer\Http\Controllers;

use ArchiElite\LogViewer\Exceptions\InvalidRegularExpression;
use ArchiElite\LogViewer\Facades\LogViewer;
use ArchiElite\LogViewer\Http\Resources\LevelCountResource;
use ArchiElite\LogViewer\Http\Resources\LogFileResource;
use ArchiElite\LogViewer\Http\Resources\LogResource;
use ArchiElite\LogViewer\Level;
use Botble\Base\Http\Controllers\BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LogController extends BaseController
{
    public const NEWEST_FIRST = 'desc';

    public function index(Request $request)
    {
        $fileIdentifier = $request->query('file', '');
        $query = $request->query('query', '');
        $direction = $request->query('direction', 'desc');
        $selectedLevels = $request->query('levels', Level::caseValues());
        $perPage = $request->integer('per_page', 25);
        session()->put('log-viewer:shorter-stack-traces', $request->boolean('shorter_stack_traces'));
        $hasMoreResults = false;
        $percentScanned = 0;

        if (! $request->integer('page', 1)) {
            $request->replace(['page' => 1]);
        }

        if ($file = LogViewer::getFile($fileIdentifier)) {
            $logQuery = $file->logs();
        } elseif (! empty($query)) {
            $logQuery = LogViewer::getFiles()->logs();
        }

        if (isset($logQuery)) {
            try {
                $logQuery->search($query);

                if (isset($file) && Str::startsWith($query, 'log-index:')) {
                    $logIndex = explode(':', $query)[1];
                    $expandAutomatically = intval($logIndex) || $logIndex === '0';
                }

                if ($direction === self::NEWEST_FIRST) {
                    $logQuery->reverse();
                }

                $logQuery->scan(LogViewer::lazyScanChunkSize());
                $logQuery->setLevels($selectedLevels);
                $logs = $logQuery->paginate($perPage);
                $levels = array_values($logQuery->getLevelCounts());

                if ($logs->lastPage() < $request->input('page', 1)) {
                    $request->replace(['page' => $logs->lastPage() ?? 1]);
                    // re-create the paginator instance to fix a bug
                    $logs = $logQuery->paginate($perPage);
                }

                $hasMoreResults = $logQuery->requiresScan();
                $percentScanned = $logQuery->percentScanned();
            } catch (InvalidRegularExpression) {
            }
        }

        return response()->json([
            'file' => isset($file) ? new LogFileResource($file) : null,
            'levelCounts' => LevelCountResource::collection($levels ?? []),
            'logs' => LogResource::collection($logs ?? []),
            'pagination' => isset($logs) ? [
                'current_page' => $logs->currentPage(),
                'first_page_url' => $logs->url(1),
                'from' => $logs->firstItem(),
                'last_page' => $logs->lastPage(),
                'last_page_url' => $logs->url($logs->lastPage()),
                'links' => $logs->linkCollection()->toArray(),
                'links_short' => $logs->onEachSide(0)->linkCollection()->toArray(),
                'next_page_url' => $logs->nextPageUrl(),
                'path' => $logs->path(),
                'per_page' => $logs->perPage(),
                'prev_page_url' => $logs->previousPageUrl(),
                'to' => $logs->lastItem(),
                'total' => $logs->total(),
            ] : null,
            'expandAutomatically' => $expandAutomatically ?? false,
            'cacheRecentlyCleared' => $this->cacheRecentlyCleared ?? false,
            'hasMoreResults' => $hasMoreResults,
            'percentScanned' => $percentScanned,
            'performance' => $this->getRequestPerformanceInfo(),
        ]);
    }

    protected function getRequestPerformanceInfo(): array
    {
        $startTime = defined('LARAVEL_START') ? LARAVEL_START : request()->server('REQUEST_TIME_FLOAT');
        $memoryUsage = number_format(memory_get_peak_usage(true) / 1024 / 1024, 2) . ' MB';
        $requestTime = number_format((microtime(true) - $startTime) * 1000) . 'ms';

        return [
            'memoryUsage' => $memoryUsage,
            'requestTime' => $requestTime,
            'version' => LogViewer::version(),
        ];
    }
}
