@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col">
            <h1>Agregar Abono — Préstamo #{{ $prestamo->id_prestamo }}</h1>
        </div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-secondary"
               href="{{ url('/movimientos/prestamos/ver/' . $prestamo->id_prestamo) }}">← Volver</a>
        </div>
    </div>

    {{-- Resumen del préstamo --}}
    <div class="prestamo-resumen" style="margin-bottom:1.5rem;">
        <div class="resumen-card">
            <span class="resumen-label">Empleado</span>
            <span class="resumen-value">{{ $prestamo->nombre_empleado }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Saldo Actual</span>
            <span class="resumen-value text-accent">${{ number_format($prestamo->saldo_actual, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">No. Abono</span>
            <span class="resumen-value">{{ $num_abono }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Fecha</span>
            <span class="resumen-value">{{ date('Y-m-d') }}</span>
        </div>
    </div>

    {{-- Detalle del abono calculado --}}
    <div class="prestamo-resumen" style="margin-bottom:2rem;">
        <div class="resumen-card">
            <span class="resumen-label">Capital a Amortizar</span>
            <span class="resumen-value">${{ number_format($pago_fijo_cap, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Interés</span>
            <span class="resumen-value">${{ number_format($monto_interes, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Total a Cobrar</span>
            <span class="resumen-value text-accent">${{ number_format($monto_cobrado, 2) }}</span>
        </div>
        <div class="resumen-card">
            <span class="resumen-label">Saldo Resultante</span>
            <span class="resumen-value">
                @if($saldo_pendiente == 0)
                    <span style="color:#16a34a;font-weight:700;">¡LIQUIDADO!</span>
                @else
                    ${{ number_format($saldo_pendiente, 2) }}
                @endif
            </span>
        </div>
    </div>

    {{-- Formulario de confirmación --}}
    <form method="POST"
          action="{{ route('movimientos.abonosAgregarPost', $prestamo->id_prestamo) }}">
        @csrf

        {{-- Campos ocultos con los valores precalculados --}}
        <input type="hidden" name="fk_id_prestamo"  value="{{ $prestamo->id_prestamo }}">
        <input type="hidden" name="num_abono"        value="{{ $num_abono }}">
        <input type="hidden" name="fecha"            value="{{ date('Y-m-d') }}">
        <input type="hidden" name="monto_capital"    value="{{ $pago_fijo_cap }}">
        <input type="hidden" name="monto_interes"    value="{{ $monto_interes }}">
        <input type="hidden" name="monto_cobrado"    value="{{ $monto_cobrado }}">
        <input type="hidden" name="saldo_pendiente"  value="{{ $saldo_pendiente }}">

        <div class="row">
            <div class="col"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Confirmar y Registrar Abono</button>
            </div>
        </div>
    </form>

@endsection