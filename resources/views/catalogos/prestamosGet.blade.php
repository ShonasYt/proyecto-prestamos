@extends("components.layout")

@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col">
            <h1>Préstamos</h1>
        </div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-primary" href="{{ url('/movimientos/prestamos/agregar') }}">Agregar</a>
        </div>
    </div>

    <table class="table" id="maintable">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Empleado</th>
                <th scope="col">Monto</th>
                <th scope="col">Plazo (quincenas)</th>
                <th scope="col">Tasa Mensual</th>
                <th scope="col">Saldo Actual</th>
                <th scope="col">Fecha Solicitud</th>
                <th scope="col">Estado</th>
                <th scope="col">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prestamos as $prestamo)
                <tr>
                    <td class="text-center">{{ $prestamo->id_prestamo }}</td>
                    <td class="text-center">{{ $prestamo->nombre_empleado }}</td>
                    <td class="text-center">${{ number_format($prestamo->monto, 2) }}</td>
                    <td class="text-center">{{ $prestamo->plazo }}</td>
                    <td class="text-center">{{ $prestamo->tasa_mensual }}%</td>
                    <td class="text-center">${{ number_format($prestamo->saldo_actual, 2) }}</td>
                    <td class="text-center">{{ $prestamo->fecha_solicitud }}</td>
                    <td class="text-center">
                        <span class="estado-badge estado-{{ strtolower($prestamo->estado) }}">
                            {{ $prestamo->estado }}
                        </span>
                    </td>
                    <td class="text-center">
                        <a href="{{ url('/movimientos/prestamos/ver/' . $prestamo->id_prestamo) }}" 
                           class="btn-accion btn-ver">Ver</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        let table = new DataTable("#maintable", {
            paging: true,
            searching: true
        });
    </script>
@endsection