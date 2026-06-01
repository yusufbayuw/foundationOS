<?php

namespace Modules\Donation\Services;

use Modules\Donation\Models\Donation;

class DonationDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Donation $donation): array
    {
        $donation->load(['campaign', 'donor']);

        return [
            'donation' => $donation,
            'showSignature' => true,
            'signatureLabel' => 'Fundraising',
        ];
    }

    public function filename(Donation $donation): string
    {
        return sprintf('DonationReceipt_%s.pdf', str_replace(' ', '_', $donation->donation_number ?? (string) $donation->getKey()));
    }
}
