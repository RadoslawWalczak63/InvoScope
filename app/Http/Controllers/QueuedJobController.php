<?php

namespace App\Http\Controllers;

use App\Http\Resources\QueuedJobResource;
use App\Models\QueuedJob;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class QueuedJobController extends Controller
{
    public function index(Request $request): Response
    {
        $queuedJobs = QueryBuilder::for(QueuedJob::class)
            ->allowedFilters([
                'name',
                AllowedFilter::exact('status'),
                AllowedFilter::callback('job', function ($query, $value) {
                    $query
                        ->where('job', 'like', "%$value%")
                        ->orWhere('job', 'like', Str::camel("%$value%"));
                }),
            ])
            ->allowedSorts([
                'job',
                'status',
                'created_at',
                'started_at',
                'finished_at',
            ])
            ->defaultSort(['-created_at'])
            ->where('user_id', $request->user()->id)
            ->paginate($request->input('per_page', 20))
            ->withQueryString();

        return Inertia::render(
            component: 'QueuedJob/Index',
            props: [
                'queuedJobs' => QueuedJobResource::collection($queuedJobs),
                'state' => [
                    'filters' => $request->input('filter', []),
                    'sort' => $request->input('sort', '-created_at'),
                ],
            ]
        );
    }

    public function show(QueuedJob $queuedJob): Response
    {
        return Inertia::render(
            component: 'QueuedJob/Show',
            props: [
                'queuedJob' => new QueuedJobResource($queuedJob),
            ]
        );
    }

    public function destroy(QueuedJob $queuedJob): RedirectResponse
    {
        $queuedJob->delete();

        return redirect()
            ->route('queued-jobs.index')
            ->with('success', 'Queued Job Deleted Successfully');
    }
}
