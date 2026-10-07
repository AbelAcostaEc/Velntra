<div class="header">
    <table class="header-table">
        <tr>
            <td>
                <h1>{{ $title }}</h1>
                <div class="muted">{{ $settings->company_name }}</div>
                @if ($settings->ruc)<div class="muted">RUC: {{ $settings->ruc }}</div>@endif
            </td>
            <td class="right muted">
                {{ $subtitle }}<br>
                Generado: {{ now()->format('d/m/Y H:i') }}
            </td>
        </tr>
    </table>
</div>
