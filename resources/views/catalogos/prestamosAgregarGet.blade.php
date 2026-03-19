@extends("components.layout")

@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row">
        <div class="form-group my-3">
            <h1>Agregar Préstamo</h1>
        </div>
    </div>

    <form method="post" action="{{ url('/movimientos/prestamos/agregar') }}">
        @csrf

        {{-- Fila 1: Empleado y Fecha Solicitud --}}
        <div class="row my-4">
            <div class="form-group mb-3 col-6">
                <label for="fk_id_empleado" class="form-label">Empleado:</label>
                <select name="fk_id_empleado" id="fk_id_empleado" class="form-select" required>
                    <option value="" disabled selected>Seleccione un empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id_empleado }}">{{ $empleado->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="fecha_solicitud" class="form-label">Fecha de Solicitud:</label>
                <input type="date" name="fecha_solicitud" id="fecha_solicitud" class="form-control" required>
            </div>
        </div>

        {{-- Fila 2: Monto y Plazo --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="monto" class="form-label">Monto ($):</label>
                <input type="number" step="0.01" min="1" name="monto" id="monto"
                       class="form-control" placeholder="Ej: 10000.00" required>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="plazo" class="form-label">Plazo (quincenas):</label>
                <input type="number" min="1" name="plazo" id="plazo"
                       class="form-control" placeholder="Ej: 12" required>
            </div>
        </div>

        {{-- Fila 3: Tasa mensual y Fecha aprobación --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="tasa_mensual" class="form-label">Tasa Mensual (%):</label>
                <input type="number" step="0.01" min="0" name="tasa_mensual" id="tasa_mensual"
                       class="form-control" placeholder="Ej: 2.5" required>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="fecha_aprob" class="form-label">Fecha de Aprobación:</label>
                <input type="date" name="fecha_aprob" id="fecha_aprob" class="form-control" required>
            </div>
        </div>

        {{-- Fila 4: Fecha inicio descuento --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="fecha_ini_desc" class="form-label">Fecha Inicio de Descuento:</label>
                <input type="date" name="fecha_ini_desc" id="fecha_ini_desc" class="form-control" required>
            </div>

            {{-- Preview calculado --}}
            <div class="form-group mb-3 col-6">
                <label class="form-label">Pago Fijo por Quincena (estimado):</label>
                <div class="preview-calc" id="preview-pago">
                    <span class="preview-value" id="val-pago">—</span>
                    <span class="preview-sub">Ingresa monto, plazo y tasa para calcular</span>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col"></div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Guardar y Generar Abonos</button>
            </div>
        </div>
    </form>

    <script>
        // Calcula el pago fijo estimado al llenar los campos
        function calcularPago() {
            const monto       = parseFloat(document.getElementById('monto').value) || 0;
            const plazo       = parseInt(document.getElementById('plazo').value) || 0;
            const tasaMensual = parseFloat(document.getElementById('tasa_mensual').value) || 0;

            if (monto > 0 && plazo > 0) {
                const tasaQuincena = tasaMensual / 2 / 100;
                let pagoFijo;

                if (tasaQuincena === 0) {
                    pagoFijo = monto / plazo;
                } else {
                    // Fórmula de amortización
                    pagoFijo = monto * (tasaQuincena * Math.pow(1 + tasaQuincena, plazo))
                                     / (Math.pow(1 + tasaQuincena, plazo) - 1);
                }

                document.getElementById('val-pago').textContent =
                    '$' + pagoFijo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                document.getElementById('val-pago').textContent = '—';
            }
        }

        document.getElementById('monto').addEventListener('input', calcularPago);
        document.getElementById('plazo').addEventListener('input', calcularPago);
        document.getElementById('tasa_mensual').addEventListener('input', calcularPago);
    </script>
@endsection