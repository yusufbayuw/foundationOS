<?php

namespace Modules\Core\Support;

use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeGenerator
{
    public function svg(string $payload, int $size = 200): string
    {
        return (string) QrCode::format('svg')->size($size)->generate($payload);
    }

    public function pngBase64(string $payload, int $size = 200): string
    {
        return base64_encode((string) QrCode::format('png')->size($size)->generate($payload));
    }
}
