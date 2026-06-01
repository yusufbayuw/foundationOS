<div style="border-bottom: 2px solid {{ $primaryColor ?? '#6366f1' }}; padding-bottom: 10px; margin-bottom: 16px;">
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            @if(! empty($context?->logoDataUri))
                <td style="width: 80px; vertical-align: top;">
                    <img src="{{ $context->logoDataUri }}" alt="Logo" style="max-width: 70px; max-height: 70px;">
                </td>
            @endif
            <td style="vertical-align: top; {{ empty($context?->logoDataUri) ? '' : 'padding-left: 10px;' }}">
                <h2 style="margin: 0; color: {{ $primaryColor ?? '#6366f1' }}; font-size: 15px;">
                    {{ strtoupper($institutionName ?? $tenant->name ?? 'Institution') }}
                </h2>
                @if(! empty($address))
                    <p class="mb-0 text-muted" style="font-size: 10px;">{{ $address }}</p>
                @endif
                @if(! empty($phone) || ! empty($email))
                    <p class="mb-0 text-muted" style="font-size: 10px;">
                        @if(! empty($phone)) Tel: {{ $phone }} @endif
                        @if(! empty($phone) && ! empty($email)) | @endif
                        @if(! empty($email)) {{ $email }} @endif
                    </p>
                @endif
            </td>
            <td style="width: 35%; vertical-align: top; text-align: right;">
                @hasSection('document_title')
                    <strong style="font-size: 12px;">@yield('document_title')</strong>
                @endif
            </td>
        </tr>
    </table>
</div>
