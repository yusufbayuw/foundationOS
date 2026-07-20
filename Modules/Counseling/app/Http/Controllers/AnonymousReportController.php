<?php

namespace Modules\Counseling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Tenant;
use Modules\Counseling\Http\Requests\AnonymousReportRequest;
use Modules\Counseling\Models\AnonymousReport;

class AnonymousReportController extends Controller
{
    public function store(AnonymousReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        Tenant::query()->findOrFail($validated['tenant_id']);

        AnonymousReport::query()->create([
            'tenant_id' => $validated['tenant_id'],
            'name' => 'Anonymous report',
            'description' => $validated['body'],
            'status' => 'new',
        ]);

        return response()->json(['accepted' => true]);
    }
}
