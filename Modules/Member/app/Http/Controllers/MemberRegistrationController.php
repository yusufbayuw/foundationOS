<?php

namespace Modules\Member\Http\Controllers;

use App\Support\CurrentTenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Member\Http\Requests\RegisterMemberRequest;
use Modules\Member\Models\Member;
use Modules\Member\Models\MemberType;

class MemberRegistrationController extends Controller
{
    public function __invoke(RegisterMemberRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $memberType = MemberType::where('code', $validated['member_type'])->firstOrFail();
        $tenantId = $request->user()?->currentAccessToken()?->tenant_id ?? app(CurrentTenant::class)->id();

        $member = DB::transaction(function () use ($request, $validated, $memberType, $tenantId): Member {
            $member = Member::create([
                'tenant_id' => $tenantId,
                'user_id' => $request->user()->id,
                'member_number' => sprintf('MBR-%s-%s-%s', now()->format('YmdHis'), $request->user()->id, Str::upper(Str::random(6))),
                'domain_member_type_id' => $memberType->id,
                'status' => 'pending',
            ]);

            $member->profile()->create([
                'profile_data' => $validated['profile'],
            ]);

            if ($request->hasFile('proof')) {
                $proof = $request->file('proof');
                $path = $proof->store('member-proofs');

                $member->proofs()->create([
                    'file_path' => $path,
                    'disk' => config('filesystems.default', 'local'),
                    'mime_type' => $proof->getMimeType() ?? 'application/octet-stream',
                    'status' => 'pending',
                ]);
            }

            return $member->load(['memberType', 'profile', 'proofs']);
        });

        return response()->json([
            'data' => [
                'id' => $member->id,
                'member_type' => $member->memberType->code,
                'status' => $member->status,
                'profile' => $member->profile?->profile_data,
                'proofs' => $member->proofs->map(fn ($proof): array => Arr::only($proof->toArray(), [
                    'id',
                    'file_path',
                    'disk',
                    'mime_type',
                    'status',
                ]))->values(),
                'created_at' => $member->created_at?->toIso8601String(),
            ],
        ], 201);
    }
}
