@php
    $empresa = \App\Models\Empresa::first();
    $logoPath = $empresa && $empresa->image_m ? public_path('storage/' . $empresa->image_m->url) : null;
@endphp

<style>
    .header-table {
        width: 100%;
        margin-bottom: 20px;
        border-bottom: 1px solid #ddd;
        padding-bottom: 10px;
    }
    .header-table td {
        border: none;
        vertical-align: middle;
    }
    .logo {
        width: 80px;
        height: auto;
    }
    .company-details {
        text-align: right;
    }
    .company-details h2 {
        margin: 0;
        font-size: 16px;
        font-weight: bold;
    }
    .company-details p {
        margin: 0;
        font-size: 10px;
        color: #555;
    }
    .report-title {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .report-dates {
        text-align: center;
        font-size: 12px;
        color: #555;
        margin-bottom: 20px;
    }
</style>

<table class="header-table">
    <tr>
        <td>
            @if($logoPath && file_exists($logoPath))
                <img src="{{ $logoPath }}" alt="Logo" class="logo">
            @endif
        </td>
        <td class="company-details">
            @if($empresa)
                <h2>{{ $empresa->nombre }}</h2>
                <p>{{ $empresa->calle }}</p>
                <p>{{ $empresa->distrito }}, {{ $empresa->provincia }}</p>
                <p>Tel: {{ $empresa->telefono }}</p>
            @endif
        </td>
    </tr>
</table>

<div class="report-title">{{ $title }}</div>
<div class="report-dates">
    <span>Desde: {{ $fechaInicio }}</span> | <span>Hasta: {{ $fechaFin }}</span>
</div>
