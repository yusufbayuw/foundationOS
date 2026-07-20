<?php

namespace App\Http\Controllers\Api\v1\App;

use App\Http\Controllers\Api\v1\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Modules\Donation\Models\Donation;

class EndowmentLeaderboardController extends ApiController
{
    public function __invoke(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'period' => ['sometimes', 'string', Rule::in(['monthly', 'yearly', 'all-time'])],
        ]);

        $period = $validated['period'] ?? 'all-time';

        $query = Donation::query()
            ->join('campaigns', 'campaigns.id', '=', 'donations.campaign_id')
            ->join('donors', 'donors.id', '=', 'donations.donor_id')
            ->where('campaigns.category', 'endowment')
            ->whereNull('campaigns.deleted_at')
            ->where('donations.payment_status', 'paid')
            ->whereNotNull('donations.paid_at')
            ->select([
                'donations.donor_id',
                DB::raw("case when donors.is_anonymous = 1 then 'Anonymous' else donors.name end as donor_name"),
                'donors.is_anonymous',
                DB::raw('sum(donations.amount) as total_amount'),
                DB::raw('count(donations.id) as donation_count'),
            ])
            ->groupBy('donations.donor_id', 'donors.name', 'donors.is_anonymous')
            ->orderByDesc('total_amount')
            ->orderBy('donor_name');

        if ($period === 'monthly') {
            $query->whereBetween('donations.paid_at', [now()->startOfMonth(), now()->endOfMonth()]);
        }

        if ($period === 'yearly') {
            $query->whereBetween('donations.paid_at', [now()->startOfYear(), now()->endOfYear()]);
        }

        $leaderboard = $query->get()->map(fn (object $row): array => [
            'donor_id' => (int) $row->donor_id,
            'donor_name' => (string) $row->donor_name,
            'is_anonymous' => (bool) $row->is_anonymous,
            'total_amount' => number_format((float) $row->total_amount, 2, '.', ''),
            'donation_count' => (int) $row->donation_count,
        ]);

        return $this->success($leaderboard, meta: [
            'period' => $period,
        ]);
    }
}
