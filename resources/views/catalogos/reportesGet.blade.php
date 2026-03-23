@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col"><h1>Reporte de Préstamos</h1></div>
    </div>

    <table class="table" id="maintable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Empleado</th>
                <th>Monto</th>
                <th>Saldo Actual</th>
                <th>Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prestamos as $p)
                <tr>
                    <td class="text-center">{{ $p->id_prestamo }}</td>
                    <td class="text-center">{{ $p->nombre_empleado }}</td>
                    <td class="text-center">${{ number_format($p->monto, 2) }}</td>
                    <td class="text-center">${{ number_format($p->saldo_actual, 2) }}</td>
                    <td class="text-center">
                        <span class="estado-badge estado-{{ strtolower($p->estado) }}">
                            {{ $p->estado }}
                        </span>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        new DataTable("#maintable", {
            paging: true,
            searching: true,
            language: {
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                paginate: { next: "Siguiente", previous: "Anterior" }
            }
        });
    </script>
@endsection