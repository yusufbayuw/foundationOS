<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TenantIsolationConfigurationTest extends TestCase
{
    #[Test]
    public function tenant_scoped_member_donation_order_and_notification_models_keep_tenant_isolation_concerns(): void
    {
        foreach ([
            'member' => 'Modules/Library/app/Models/Member.php',
            'donation' => 'Modules/Donation/app/Models/Donation.php',
            'sales order' => 'Modules/Sales/app/Models/SalesOrder.php',
            'merch order' => 'Modules/MerchOrder/app/Models/MerchOrder.php',
            'marketplace order' => 'Modules/Marketplace/app/Models/MarketplaceOrder.php',
            'notification template' => 'Modules/Messaging/app/Models/NotificationTemplate.php',
        ] as $label => $path) {
            $contents = file_get_contents(__DIR__."/../../../{$path}");

            $this->assertStringContainsString('BelongsToTenant', $contents, "{$label} must remain tenant scoped.");
        }
    }

    #[Test]
    public function voucher_and_otp_security_events_have_central_activity_logger_hooks(): void
    {
        $contents = file_get_contents(__DIR__.'/../../../app/Support/Security/SecurityActivityLogger.php');

        $this->assertStringContainsString('voucherRedeemed', $contents);
        $this->assertStringContainsString('otpSuspiciousAttempt', $contents);
        $this->assertStringContainsString('activity(', $contents);
    }
}
