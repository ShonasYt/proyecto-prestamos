@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col">
            <h1>Puestos de {{ $empleado->nombre }}</h1>
        </div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-secondary" href="{{ url('/empleados') }}">← Volver</a>
        </div>
    </div>

    <table class="table" id="maintable">
        <thead>
            <tr>
                <th>Puesto</th>
                <th>Fecha Inicio</th>
            </tr>
        </thead>
        <tbody>
            @foreach($puestos as $p)
                <tr>
                    <td class="text-center">{{ $p->nombre }}</td>
                    <td class="text-center">{{ $p->fecha_inicio }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <script>
        new DataTable("#maintable", { paging: true, searching: false });
    </script>
@endsection