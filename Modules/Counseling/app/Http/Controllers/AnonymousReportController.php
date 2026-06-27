<?php

namespace Modules\Counseling\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Core\Models\Tenant;
use Modules\Counseling\Models\AnonymousReport;

class AnonymousReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        /** @var array{tenant_id: int, body: string} $validated */
        $validated = $request->validate([
            'tenant_id' => ['required', 'integer', 'exists:tenants,id'],
            'body' => ['required', 'string', 'max:5000'],
        ]);

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
