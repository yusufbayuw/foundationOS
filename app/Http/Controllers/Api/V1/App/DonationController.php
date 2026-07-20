<?php

namespace App\Http\Controllers\Api\V1\App;

use App\Http\Controllers\Api\v1\ApiController;
use App\Http\Resources\Api\V1\App\CampaignResource;
use App\Http\Resources\Api\V1\App\DonationResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;

class DonationController extends ApiController
{
    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Campaign::class);

        return $this->collectionResponse(Campaign::query()->where('is_public', true)->latest('id'), $request, CampaignResource::class);
    }

    public function show(Campaign $campaign): JsonResponse
    {
        $this->authorize('view', $campaign);

        return $this->success(new CampaignResource($campaign));
    }

    public function checkout(Request $request, Campaign $campaign): JsonResponse
    {
        $this->authorize('create', Donation::class);

        $data = $request->validate(['amount' => ['required', 'numeric', 'min:1']]);
        $donation = Donation::create([
            'tenant_id' => $campaign->tenant_id,
            'campaign_id' => $campaign->id,
            'donation_number' => 'DON-'.Str::upper(Str::random(10)),
            'amount' => $data['amount'],
            'payment_status' => 'pending',
            'payment_reference' => 'APP-'.Str::uuid(),
        ]);

        return $this->success(new DonationResource($donation), 201);
    }

    public function mine(Request $request): JsonResponse
    {
        $this->authorize('viewAny', Donation::class);

        return $this->collectionResponse(Donation::query()->latest('id'), $request, DonationResource::class);
    }
}
