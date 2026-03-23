@extends("components.layout")

@section("content")
    @component("components.breadcrumbs", ["breadcrumbs" => $breadcrumbs])
    @endcomponent

    <div class="row">
        <div class="form-group my-3">
            <h1>Agregar Préstamo</h1>
        </div>
    </div>

    {{-- Errores del servidor --}}
    @if ($errors->any())
        <div class="alert alert-danger mb-3" style="border-radius:12px;">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Datos de empleados para validación JS --}}
    <script>
        const empleadosData = {
            @foreach($empleados as $emp)
                "{{ $emp->id_empleado }}": {
                    fechaIngreso: "{{ $emp->fecha_ingreso_f }}",
                    sueldo: {{ $emp->sueldo_puesto }}
                },
            @endforeach
        };
    </script>

    <form method="post" action="{{ url('/movimientos/prestamos/agregar') }}" id="form-prestamo">
        @csrf

        {{-- Fila 1: Empleado y Fecha Solicitud --}}
        <div class="row my-4">
            <div class="form-group mb-3 col-6">
                <label for="fk_id_empleado" class="form-label">Empleado:</label>
                <select name="fk_id_empleado" id="fk_id_empleado" class="form-select" required>
                    <option value="" disabled selected>Seleccione un empleado</option>
                    @foreach($empleados as $empleado)
                        <option value="{{ $empleado->id_empleado }}"
                            {{ old('fk_id_empleado') == $empleado->id_empleado ? 'selected' : '' }}>
                            {{ $empleado->nombre }}
                        </option>
                    @endforeach
                </select>
                {{-- Alertas JS para el empleado --}}
                <div id="alerta-antiguedad" class="mt-2" style="display:none;color:#dc2626;font-size:.82rem;font-weight:600;">
                    ⚠ El empleado no cumple con 1 año de antigüedad.
                </div>
                <div id="alerta-activo" class="mt-1" style="display:none;color:#dc2626;font-size:.82rem;font-weight:600;">
                    ⚠ Este empleado ya tiene un préstamo activo.
                </div>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="fecha_solicitud" class="form-label">Fecha de Solicitud:</label>
                <input type="date" name="fecha_solicitud" id="fecha_solicitud" class="form-control"
                       value="{{ old('fecha_solicitud') }}" required>
            </div>
        </div>

        {{-- Fila 2: Monto y Plazo --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="monto" class="form-label">Monto ($):</label>
                <input type="number" step="0.01" min="1" name="monto" id="monto"
                       class="form-control" placeholder="Ej: 10000.00"
                       value="{{ old('monto') }}" required>
                <div id="alerta-monto" class="mt-2" style="display:none;color:#dc2626;font-size:.82rem;font-weight:600;"></div>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="plazo" class="form-label">Plazo (quincenas):</label>
                <input type="number" min="1" name="plazo" id="plazo"
                       class="form-control" placeholder="Ej: 12"
                       value="{{ old('plazo') }}" required>
            </div>
        </div>

        {{-- Fila 3: Tasa mensual y Fecha aprobación --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="tasa_mensual" class="form-label">Tasa Mensual (%):</label>
                <input type="number" step="0.01" min="0" name="tasa_mensual" id="tasa_mensual"
                       class="form-control" placeholder="Ej: 2.5"
                       value="{{ old('tasa_mensual') }}" required>
            </div>
            <div class="form-group mb-3 col-6">
                <label for="fecha_aprob" class="form-label">Fecha de Aprobación:</label>
                <input type="date" name="fecha_aprob" id="fecha_aprob" class="form-control"
                       value="{{ old('fecha_aprob') }}" required>
            </div>
        </div>

        {{-- Fila 4: Fecha inicio descuento + Preview --}}
        <div class="row my-3">
            <div class="form-group mb-3 col-6">
                <label for="fecha_ini_desc" class="form-label">Fecha Inicio de Descuento:</label>
                <input type="date" name="fecha_ini_desc" id="fecha_ini_desc" class="form-control"
                       value="{{ old('fecha_ini_desc') }}" required>
            </div>
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
                <button type="submit" class="btn btn-primary" id="btn-guardar">
                    Guardar y Generar Abonos
                </button>
            </div>
        </div>
    </form>

    <script>
        // ── Datos de empleados con préstamos activos (viene del servidor) ──
        // La validación de préstamo activo solo es 100% confiable en el servidor.
        // Aquí bloqueamos el submit si las otras dos condiciones fallan.

        let empleadoSeleccionado = null;

        function validarEmpleado() {
            const id = document.getElementById('fk_id_empleado').value;
            const alertaAnt    = document.getElementById('alerta-antiguedad');
            const alertaActivo = document.getElementById('alerta-activo');

            alertaAnt.style.display    = 'none';
            alertaActivo.style.display = 'none';

            if (!id || !empleadosData[id]) {
                empleadoSeleccionado = null;
                return;
            }

            empleadoSeleccionado = empleadosData[id];

            // Verificar antigüedad
            const hoy         = new Date();
            const ingreso     = new Date(empleadoSeleccionado.fechaIngreso);
            const diffAnios   = (hoy - ingreso) / (1000 * 60 * 60 * 24 * 365.25);
            if (diffAnios < 1) {
                alertaAnt.style.display = 'block';
            }

            // Actualizar alerta de monto si ya hay monto ingresado
            validarMonto();
        }

        function validarMonto() {
            const alertaMonto = document.getElementById('alerta-monto');
            alertaMonto.style.display = 'none';

            if (!empleadoSeleccionado) return;

            const monto      = parseFloat(document.getElementById('monto').value) || 0;
            const montoMax   = empleadoSeleccionado.sueldo * 6;

            if (monto > 0 && monto > montoMax) {
                alertaMonto.textContent = `⚠ El monto supera el máximo permitido ($${montoMax.toLocaleString('es-MX', {minimumFractionDigits:2})}).`;
                alertaMonto.style.display = 'block';
            }
        }

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
                    pagoFijo = monto * (tasaQuincena * Math.pow(1 + tasaQuincena, plazo))
                                     / (Math.pow(1 + tasaQuincena, plazo) - 1);
                }
                document.getElementById('val-pago').textContent =
                    '$' + pagoFijo.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            } else {
                document.getElementById('val-pago').textContent = '—';
            }
        }

        // Bloquear submit si hay alertas visibles
        document.getElementById('form-prestamo').addEventListener('submit', function(e) {
            const alertas = [
                document.getElementById('alerta-antiguedad'),
                document.getElementById('alerta-monto')
            ];
            for (const alerta of alertas) {
                if (alerta.style.display === 'block') {
                    e.preventDefault();
                    alerta.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    return;
                }
            }
        });

        document.getElementById('fk_id_empleado').addEventListener('change', validarEmpleado);
        document.getElementById('monto').addEventListener('input', () => { validarMonto(); calcularPago(); });
        document.getElementById('plazo').addEventListener('input', calcularPago);
        document.getElementById('tasa_mensual').addEventListener('input', calcularPago);
    </script>
@endsection