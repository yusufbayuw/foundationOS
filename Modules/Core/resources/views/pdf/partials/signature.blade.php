@if(! empty($showSignature))
    <table style="width: 100%; margin-top: 36px;">
        <tr>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                @if(! empty($context?->signatureDataUri))
                    <img src="{{ $context->signatureDataUri }}" alt="Signature" style="max-height: 60px; margin-bottom: 6px;">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div style="border-top: 1px solid #000; padding-top: 4px;">
                    {{ $signatureLabel ?? 'Penanggung Jawab' }}
                </div>
            </td>
            <td style="width: 50%; text-align: center; vertical-align: bottom;">
                @if(! empty($context?->stampDataUri))
                    <img src="{{ $context->stampDataUri }}" alt="Stamp" style="max-height: 60px; margin-bottom: 6px;">
                @else
                    <div style="height: 60px;"></div>
                @endif
                <div style="border-top: 1px solid #000; padding-top: 4px;">
                    {{ $stampLabel ?? 'Stempel' }}
                </div>
            </td>
        </tr>
    </table>
@endif
