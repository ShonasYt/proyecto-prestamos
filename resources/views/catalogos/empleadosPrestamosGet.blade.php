@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col">
            <h1>Préstamos de {{ $empleado->nombre }}</h1>
        </div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-secondary" href="{{ url('/empleados') }}">← Volver</a>
        </div>
    </div>

    <table class="table" id="maintable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Monto</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Saldo Actual</th>
                <th>Estado</th>
                <th>Detalle</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prestamos as $p)
                <tr>
                    <td class="text-center">{{ $p->id_prestamo }}</td>
                    <td class="text-center">${{ number_format($p->monto, 2) }}</td>
                    <td class="text-center">{{ $p->fecha_ini_desc }}</td>
                    <td class="text-center">{{ $p->fecha_fin_desc }}</td>
                    <td class="text-center">${{ number_format($p->saldo_actual, 2) }}</td>
                    <td class="text-center">
                        <span class="estado-badge estado-{{ strtolower($p->estado) }}">
                            {{ $p->estado }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ url('/movimientos/prestamos/ver/' . $p->id_prestamo) }}"
                           class="btn-accion btn-ver">Ver</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        new DataTable("#maintable", { paging: true, searching: true });
    </script>
@endsection