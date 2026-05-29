<?php

namespace Modules\Exam\Enums;

enum ExamRuntimeSyncAction: string
{
    case Publish = 'publish';
    case Republish = 'republish';
    case SyncParticipants = 'sync_participants';
    case SyncAdminAccess = 'sync_admin_access';
    case SyncResults = 'sync_results';

    public function label(): string
    {
        return match ($this) {
            self::Publish => 'Publish',
            self::Republish => 'Republish',
            self::SyncParticipants => 'Sync participants',
            self::SyncAdminAccess => 'Sync admin access',
            self::SyncResults => 'Sync results',
        };
    }
}
