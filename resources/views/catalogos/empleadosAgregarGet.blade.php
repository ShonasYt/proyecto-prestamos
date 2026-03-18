@extends("components.layout")

@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row">
        <div class="form-group my-3">
            <h1>Agregar empleado</h1>
        </div>
    </div>

    <form method="post" action="{{ url('/empleados/agregar') }}">
        @csrf 
        
        <div class="row my-4">
            <div class="form-group mb-3 col-6">
                <label for="nombre" class="form-label">Nombre:</label>
                <input type="text" maxlength="50" class="form-control" name="nombre" placeholder="Ingrese nombre completo" id="nombre" required autofocus>
            </div>
            
            <div class="form-group mb-3 col-6">
                <label for="fecha_ingreso" class="form-label">Fecha de ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
            </div>
        </div>

        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="puesto" class="form-label">Puesto:</label>
                <select name="puesto" id="puesto" class="form-select" required>
                    <option value="" disabled selected>Seleccione un puesto</option>
                    @foreach($puestos as $puesto)
                        <option value="{{ $puesto->id_puesto }}">{{ $puesto->nombre }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-group mb-3 col-6">
                <label for="activo" class="form-label">Activo:</label>
                <select name="activo" id="activo" class="form-select" required>
                    <option value="1">SÍ</option>
                    <option value="0">NO</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
        </div> </form>
@endsection