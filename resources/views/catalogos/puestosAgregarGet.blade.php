@extends("components.layout")
@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row my-4">
        <div class="col"><h1>Agregar Puesto</h1></div>
        <div class="col-auto titlebar-commands">
            <a class="btn btn-secondary" href="{{ url('/catalogos/puestos') }}">← Volver</a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mb-3" style="border-radius:12px;">
            <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ url('/catalogos/puestos/agregar') }}">
        @csrf
        <div class="row my-4">
            <div class="form-group mb-3 col-6">
                <label class="form-label">Nombre del Puesto:</label>
                <input type="text" name="nombre" class="form-control"
                       placeholder="Ej: ANALISTA DE SISTEMAS"
                       value="{{ old('nombre') }}" required>
            </div>
            <div class="form-group mb-3 col-6">
                <label class="form-label">Sueldo Mensual ($):</label>
                <input type="number" step="0.01" min="1" name="sueldo"
                       class="form-control" placeholder="Ej: 15000.00"
                       value="{{ old('sueldo') }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Guardar Puesto</button>
            </div>
        </div>
    </form>
@endsection