<?php

namespace Modules\Monitoring\Listeners;

use App\Support\TypedValue;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
use Modules\Monitoring\Services\SecurityAuditLogger;

class LogSecurityAuthEvents
{
    public function __construct(
        protected SecurityAuditLogger $logger,
    ) {}

    public function handleFailed(Failed $event): void
    {
        $this->logger->log(
            action: 'security.login_failed',
            description: 'Failed login attempt for '.TypedValue::string($event->credentials['email'] ?? 'unknown', 'unknown'),
            status: 'failed',
            userId: null,
            ipAddress: $this->request()->ip(),
            userAgent: $this->request()->userAgent(),
            newValues: [
                'email' => $event->credentials['email'] ?? null,
                'guard' => $event->guard,
            ],
        );
    }

    public function handleLockout(Lockout $event): void
    {
        $this->logger->log(
            action: 'security.lockout',
            description: 'Account lockout triggered',
            status: 'failed',
            userId: null,
            ipAddress: $this->request()->ip(),
            userAgent: $this->request()->userAgent(),
        );
    }

    public function handleLogin(Login $event): void
    {
        if ($event->guard !== 'web') {
            return;
        }

        $user = $event->user;

        $this->logger->log(
            action: 'security.login',
            description: 'User logged in',
            status: 'success',
            userId: TypedValue::int($user->getAuthIdentifier()),
            ipAddress: $this->request()->ip(),
            userAgent: $this->request()->userAgent(),
            newValues: [
                'user_id' => $user->getAuthIdentifier(),
                'email' => $user->email ?? null,
            ],
        );
    }

    protected function request(): Request
    {
        return RequestFacade::instance();
    }
}
