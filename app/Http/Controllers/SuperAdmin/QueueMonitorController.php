<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{DB, Artisan};

class QueueMonitorController extends Controller
{
    public function index()
    {
        $pendingJobs = DB::table('jobs')->orderBy('created_at', 'desc')->get()->map(function ($job) {
            $payload = json_decode($job->payload, true);
            $job->display_name = $payload['displayName'] ?? 'Unknown';
            $job->data = $payload;
            $job->created = \Carbon\Carbon::createFromTimestamp($job->created_at);
            return $job;
        });

        $failedJobs = DB::table('failed_jobs')->orderBy('failed_at', 'desc')->get()->map(function ($job) {
            $payload = json_decode($job->payload, true);
            $job->display_name = $payload['displayName'] ?? 'Unknown';
            $job->failed = \Carbon\Carbon::parse($job->failed_at);
            return $job;
        });

        $stats = [
            'pending'  => $pendingJobs->count(),
            'failed'   => $failedJobs->count(),
            'queues'   => $pendingJobs->groupBy('queue')->map->count(),
        ];

        return view('superadmin.queue-monitor.index', compact('pendingJobs', 'failedJobs', 'stats'));
    }

    public function retry(string $id)
    {
        Artisan::call('queue:retry', ['id' => [$id]]);
        return redirect()->route('superadmin.queue-monitor.index')
            ->with('success', 'Job queued for retry.');
    }

    public function retryAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);
        return redirect()->route('superadmin.queue-monitor.index')
            ->with('success', 'All failed jobs queued for retry.');
    }

    public function forget(string $id)
    {
        Artisan::call('queue:forget', ['id' => $id]);
        return redirect()->route('superadmin.queue-monitor.index')
            ->with('success', 'Failed job deleted.');
    }

    public function flush()
    {
        Artisan::call('queue:flush');
        return redirect()->route('superadmin.queue-monitor.index')
            ->with('success', 'All failed jobs cleared.');
    }

    public function processNow()
    {
        Artisan::call('queue:work', ['--stop-when-empty' => true, '--tries' => 3]);
        $output = trim(Artisan::output());
        return redirect()->route('superadmin.queue-monitor.index')
            ->with('success', 'Queue processed. ' . ($output ?: 'No pending jobs.'));
    }
}
