<?php

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Api\v1\ApiController as V1ApiController;

abstract class ApiController extends V1ApiController
{
    protected function apiVersion(): string
    {
        return 'v2';
    }
}
