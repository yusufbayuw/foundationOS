<div style="margin-top: 24px; font-size: 10px; color: #777; border-top: 1px solid #e5e7eb; padding-top: 8px;">
    <table style="width: 100%;">
        <tr>
            <td>
                @if(! empty($generatedAt))
                    {{ $generatedAt }}
                @else
                    {{ now()->format('d/m/Y H:i') }}
                @endif
            </td>
            <td style="text-align: right;">
                @if(! empty($verificationUrl))
                    Verifikasi: {{ $verificationUrl }}
                @else
                    {{ $tenant->name ?? '' }}
                @endif
            </td>
        </tr>
    </table>
</div>
