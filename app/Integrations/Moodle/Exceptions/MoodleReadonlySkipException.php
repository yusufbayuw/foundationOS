<?php

namespace App\Integrations\Moodle\Exceptions;

class MoodleReadonlySkipException extends MoodleIntegrationException
{
    public function __construct()
    {
        parent::__construct('Moodle sync skipped because MOODLE_SYNC_READONLY=true.');
    }
}
