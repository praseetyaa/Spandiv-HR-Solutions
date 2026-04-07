<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\JobPostingResource;
use App\Models\JobPosting;
use App\Models\Scopes\TenantScope;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    /**
     * Display a listing of published job postings for the authenticated tenant.
     *
     * @queryParam search string  Filter by title keyword. Example: designer
     * @queryParam type   string  Filter by employment type. Example: freelance
     * @queryParam page   integer Page number for pagination. Example: 1
     */
    public function index(Request $request)
    {
        $tenant = $request->attributes->get('api_tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $query = JobPosting::with(['department', 'position'])
            ->withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->where('status', 'published');

        // Optional: search by title
        if ($search = $request->query('search')) {
            $query->where('title', 'like', '%' . $search . '%');
        }

        // Optional: filter by employment type
        if ($type = $request->query('type')) {
            $query->where('employment_type', $type);
        }

        $jobs = $query->orderBy('created_at', 'desc')->paginate(
            $request->query('per_page', 15)
        );

        return JobPostingResource::collection($jobs)->additional([
            'success' => true,
        ]);
    }

    /**
     * Display a single job posting by its slug.
     *
     * @urlParam slug string required The URL-friendly slug of the job title. Example: freelance-graphic-designer
     */
    public function show(Request $request, string $slug)
    {
        $tenant = $request->attributes->get('api_tenant');

        if (!$tenant) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized.',
            ], 401);
        }

        $job = JobPosting::with(['department', 'position'])
            ->withoutGlobalScope(TenantScope::class)
            ->where('tenant_id', $tenant->id)
            ->where('status', 'published')
            ->get()
            ->first(function ($job) use ($slug) {
                return \Illuminate\Support\Str::slug($job->title) === $slug;
            });

        if (!$job) {
            return response()->json([
                'success' => false,
                'message' => 'Job posting not found.',
            ], 404);
        }

        return (new JobPostingResource($job))->additional([
            'success' => true,
        ]);
    }
}
