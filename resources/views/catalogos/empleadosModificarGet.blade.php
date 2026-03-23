@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col"><h1>Modificar Empleado</h1></div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-secondary" href="{{ url('/empleados') }}">← Volver</a>
        </div>
    </div>

    <form method="POST" action="{{ url('/empleados/modificar/' . $empleado->id_empleado) }}">
        @csrf
        <div class="row my-4">
            <div class="form-group mb-3 col-6">
                <label class="form-label">Nombre:</label>
                <input type="text" name="nombre" class="form-control"
                       value="{{ $empleado->nombre }}" required>
            </div>
            <div class="form-group mb-3 col-6">
                <label class="form-label">Fecha de Ingreso:</label>
                <input type="date" name="fecha_ingreso" class="form-control"
                       value="{{ $empleado->fecha_ingreso }}" required>
            </div>
        </div>
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label class="form-label">Activo:</label>
                <select name="activo" class="form-select">
                    <option value="1" {{ $empleado->activo == 1 ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ $empleado->activo == 0 ? 'selected' : '' }}>No</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
            </div>
        </div>
    </form>
@endsection