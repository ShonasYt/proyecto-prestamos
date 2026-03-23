@extends("components.layout")

@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    {{-- Encabezado --}}
    <div class="row my-4">
        <div class="col">
            <h1>Detalle del Préstamo #{{ $prestamo->id_prestamo }}</h1>
        </div>
        <div class="col-auto titlebar-commands">
            @if($prestamo->estado == 'ACTIVO')
                <a class="btn btn-primary"
                href="{{ route('movimientos.abonosAgregarGet', $prestamo->id_prestamo) }}">
                + Agregar Abono
                </a>
            @endif
            <a class="btn btn-secondary" href="{{ url('/movimientos/prestamos') }}">← Volver</a>
        </div>
    </div>

    {{-- Tarjetas de resumen --}}
    <div class="prestamo-resumen">
        <div class="resumen-card">
            <span class="resumen-label">Empleado</span>
            <span class="resumen-value">{{ $prestamo->nombre_empleado }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Monto Original</span>
            <span class="resumen-value">${{ number_format($prestamo->monto, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Saldo Actual</span>
            <span class="resumen-value text-accent">${{ number_format($prestamo->saldo_actual, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Plazo</span>
            <span class="resumen-value">{{ $prestamo->plazo }} quincenas</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Tasa Mensual</span>
            <span class="resumen-value">{{ $prestamo->tasa_mensual }}%</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Estado</span>
            <span class="resumen-value">
                <span class="estado-badge estado-{{ strtolower($prestamo->estado) }}">
                    {{ $prestamo->estado }}
                </span>
            </span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Fecha Solicitud</span>
            <span class="resumen-value">{{ $prestamo->fecha_solicitud }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Inicio Descuento</span>
            <span class="resumen-value">{{ $prestamo->fecha_ini_desc }}</span>
        </div>
    </div>

    {{-- Tabla de abonos --}}
    <div class="row my-4">
        <div class="col">
            <h5 class="abonos-title">Tabla de Amortización</h5>
        </div>
    </div>

    <table class="table" id="tablaAbonos">
        <thead>
            <tr>
                <th scope="col">No. Abono</th>
                <th scope="col">Fecha</th>
                <th scope="col">Capital</th>
                <th scope="col">Interés</th>
                <th scope="col">Monto Cobrado</th>
                <th scope="col">Saldo Pendiente</th>
            </tr>
        </thead>
        <tbody>
            @foreach($abonos as $abono)
                <tr class="{{ $abono->saldo_pendiente == 0 ? 'abono-pagado' : '' }}">
                    <td class="text-center">{{ $abono->num_abono }}</td>
                    <td class="text-center">{{ $abono->fecha }}</td>
                    <td class="text-center">${{ number_format($abono->monto_capital, 2) }}</td>
                    <td class="text-center">${{ number_format($abono->monto_interes, 2) }}</td>
                    <td class="text-center">${{ number_format($abono->monto_cobrado, 2) }}</td>
                    <td class="text-center">${{ number_format($abono->saldo_pendiente, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        let table = new DataTable("#tablaAbonos", {
            paging: true,
            searching: false,
            order: [[0, 'asc']]
        });
    </script>
@endsection