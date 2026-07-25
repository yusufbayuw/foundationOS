<?php

namespace Modules\Donation\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Donation\Http\Requests\Api\V1\CreateDonationCheckoutRequest;
use Modules\Donation\Http\Resources\Api\V1\DonationResource;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;

class DonationController extends ApiController
{
    public function store(CreateDonationCheckoutRequest $request): JsonResponse
    {
        $campaign = Campaign::query()
            ->where('status', 'active')
            ->where('is_public', true)
            ->findOrFail($request->integer('campaign_id'));

        $donor = Donor::query()->firstOrCreate(
            [
                'tenant_id' => $campaign->tenant_id,
                'user_id' => $request->user()->getKey(),
            ],
            [
                'name' => $request->input('donor.name', $request->user()->name),
                'email' => $request->input('donor.email', $request->user()->email),
                'phone' => $request->input('donor.phone'),
                'is_anonymous' => $request->boolean('donor.is_anonymous'),
                'tags' => [],
            ],
        );

        $donation = Donation::query()->create([
            'tenant_id' => $campaign->tenant_id,
            'campaign_id' => $campaign->id,
            'donor_id' => $donor->id,
            'donation_number' => 'DON-'.now()->format('YmdHis').'-'.Str::upper(Str::random(6)),
            'amount' => $request->validated('amount'),
            'payment_status' => 'pending',
            'payment_reference' => 'CHK-'.Str::upper(Str::random(12)),
            'certificate_token' => Str::uuid()->toString(),
        ])->load(['campaign', 'donor']);

        return $this->success(new DonationResource($donation), 201);
    }

    public function mine(Request $request): JsonResponse
    {
        $query = Donation::query()
            ->with(['campaign', 'donor'])
            ->whereHas('donor', fn ($query) => $query->where('user_id', $request->user()->getKey()))
            ->orderByDesc('created_at')
            ->orderByDesc('id');

        return $this->collectionResponse($query, $request, DonationResource::class);
    }
}
