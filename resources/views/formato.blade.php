<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato de Traslado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafa;
            color: #0d0e11;
            padding: 10px;
            font-size: 12px;
        }

        header {
            text-align: left;
            margin-bottom: 25px;
        }

        .container {
            max-width: 720px;
            margin: 0 auto;
            background: white;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .header {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            color: #1B3C71;
        }

        .section {
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e5e7eb;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 5px;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .header img {
            width: 25px;
            height: 25px;
        }

        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1B3C71;
            margin-bottom: 5px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 5px;
            text-align: left;
            font-size: 10px;
        }

        .table th {
            background-color: #1B3C71;
            color: white;
        }

        .badge {
            display: inline-block;
            padding: 3px 6px;
            border-radius: 4px;
            font-size: 10px;
            color: white;
        }

        .badge-success {
            background-color: #10b981;
        }

        .footer {
            bottom: 10px;
            left: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
        }

        .badge-danger {
            background-color: #ef4444;
        }
    </style>
</head>
<header class="header">
    <img src="{{ public_path('images/logo222.svg') }}" alt="Logo" align="left">
    <span>Dirección del Sistema de Emergencias Médicas 132</span>
    <img src="{{ public_path('images/GTS2-removebg-preview.png') }}" alt="Logo2" align="right">

</header>

<body>
    <div class="container">
        <div class="header">Detalles del Traslado - {{ $traslado->correlativo ?: 'N/A' }}</div>

        <!-- Información General -->
        <div class="section">
            <div class="section-title">Información General</div>
            <table class="table">
                <!-- Row 1: Asunto, Tipo y Fecha -->
                <tr>
                    <td>
                        <strong>Asunto de Traslado</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->asunto_traslado ?: 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <strong>Tipo de Traslado</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->tipo_traslado ?: 'N/A' }}
                        </span>

                    </td>
                    <td>
                        <strong>Fecha de creación</strong><br>
                        {{ $traslado->created_at ?: 'N/A' }}
                    </td>
                </tr>
                <!-- Row 2: Hora, Ambulancia y Tipo Ambulancia -->
                <tr>

                    <td>
                        <strong>Unidad</strong><br>
                        {{ $traslado->ambulancia ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Tipo Unidad</strong><br>
                        {{ $traslado->tipo_ambulancia ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Tipo Unidad Sugerida</strong><br>
                        {{ $traslado->tipo_ambulancia_sugerida ?: 'N/A' }}
                    </td>
                </tr>
                <!-- Row 3: Operador, Número de Llamada y Fecha Traslado -->
                <tr>
                    <td>
                        <strong>Operador Asignado</strong><br>
                        {{ $traslado->operador_nombre ?: 'N/A' }} (PP: {{ $traslado->operador_numero ?: 'N/A' }})
                    </td>
                    <td colspan="1">
                        <strong>Número de Llamada</strong><br>
                        {{ $traslado->numero_llamada ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Gestor Asignado</strong><br>
                        {{ $traslado->gestor_nombre ?: 'N/A' }} (PP: {{ $traslado->gestor_numero ?: 'N/A' }})
                    </td>
                </tr>
                <!-- Row 4: Programado (fila completa) -->
                <tr>
                    <td colspan="2">
                        <strong>Programado</strong><br>
                        {{ $traslado->programado ? 'Sí' : 'No' }}
                    </td>
                    <td>
                        <strong>Fecha de Programación</strong><br>
                        {{ $traslado->fecha_traslado ?: 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Origen y Destino -->
        <div class="section">
            <div class="section-title">Origen</div>
            <table class="table">
                <!-- Row 1: Institución Origen, Traslado Origen y Servicio Origen -->
                <tr>
                    <td>
                        <strong>Origen</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->origen_institucion == 1
                                ? 'Hospital'
                                : ($traslado->origen_institucion == 2
                                    ? 'Unidad de Salud'
                                    : ($traslado->origen_institucion == 3
                                        ? 'ISSS'
                                        : ($traslado->origen_institucion == 4
                                            ? 'Privado'
                                            : 'N/A'))) }}
                        </span>
                    </td>
                    <td colspan="2">
                        <strong>Traslado Origen</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->origen_traslado_nombre ?: 'N/A' }}
                        </span>
                    </td>
                </tr>
                <!-- Row 2: Número de Cama Origen, Institución Destino y Traslado Destino -->
                <tr>
                    <td>
                        <strong>Nombre de médico solicitante</strong><br>
                        {{ $traslado->nombre_medico_solicitante ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Teléfono médico solicitante</strong><br>
                        {{ $traslado->telefono_medico_solicitante ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>JVPE médico solicitante</strong><br>
                        {{ $traslado->jvpe_medico_entrega ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Servicio de Origen</strong><br>
                        {{ $traslado->servicio_origen ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Número de Cama</strong><br>
                        {{ $traslado->numero_cama_origen ?: 'N/A' }}
                    </td>
                </tr>

            </table>
        </div>
        <div class="section">
            <div class="section-title">Destino</div>
            <table class="table">
                <!-- Row 1: Institución Origen, Traslado Origen y Servicio Origen -->
                <tr>
                    <td>
                        <strong>Institución Destino</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->destino_institucion == 1
                                ? 'Hospital'
                                : ($traslado->destino_institucion == 2
                                    ? 'Unidad de Salud'
                                    : ($traslado->destino_institucion == 3
                                        ? 'ISSS'
                                        : ($traslado->destino_institucion == 4
                                            ? 'Privado'
                                            : 'N/A'))) }}
                        </span>
                    </td>
                    <td colspan="2">
                        <strong>Traslado Destino</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->destino_traslado_nombre ?: 'N/A' }}
                        </span>
                    </td>

                </tr>
                <!-- Row 3: Servicio Destino, Número de Cama Destino y Posición -->
                <tr>
                    <td>
                        <strong>Nombre de médico que recibe</strong><br>
                        {{ $traslado->nombre_medico_recibe ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Teléfono médico receptor</strong><br>
                        {{ $traslado->telefono_medico_recibe ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>JVPE médico receptor</strong><br>
                        {{ $traslado->jvpe_medico_recibe ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <strong>Servicio de Destino</strong><br>
                        {{ $traslado->servicio_destino ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Número de Cama</strong><br>
                        {{ $traslado->numero_cama_destino ?: 'N/A' }}
                    </td>
                </tr>
                <!-- Row 4: Color (fila completa) -->
            </table>
        </div>

        <!-- Datos del Paciente -->
        <div class="section">
            <div class="section-title">Datos del Paciente</div>
            <table class="table">
                <!-- Nombre completo en fila completa -->
                <tr>
                    <td colspan="1">
                        <strong>Nombre del Paciente</strong><br>
                        {{ $traslado->nombres_paciente ?: 'N/A' }} {{ $traslado->apellidos_paciente ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Edad</strong><br>
                        {{ $traslado->edad_paciente ?: 'N/A' }} - {{ $traslado->componente_edad ?: 'N/A' }}
                    </td>
                    <td colspan="1">
                        <strong>Sexo</strong><br>
                        <span
                            class="badge {{ $traslado->sexo_paciente == 'Masculino' ? 'badge-primary' : 'badge-danger' }}">
                            {{ $traslado->sexo_paciente ?: 'N/A' }}
                        </span>
                    </td>
                </tr>
                <!-- Row 1: Edad, Sexo y Registro Expediente -->
                <tr>
                    <td colspan="1">
                        <strong>Registro Expediente</strong><br>
                        {{ $traslado->registro_expediente ?: 'N/A' }}
                    </td>
                    <td colspan="2">
                        <strong>Diagnóstico</strong><br>
                        {{ $traslado->diagnostico_paciente ?: 'N/A' }}
                    </td>
                </tr>
                <!-- Diagnóstico en fila completa -->
                <!-- Row 2: Tipo de Paciente y Tipo Crítico -->
                <tr>
                    <td>
                        <strong>Tipo de Paciente</strong><br>
                        <span
                            class="badge {{ $traslado->tipo_paciente == 'Critico' ? 'badge-danger' : 'badge-success' }}">
                            {{ $traslado->tipo_paciente ?: 'N/A' }}
                        </span>
                    </td>
                    <td colspan="2">
                        <strong>Tipo Crítico</strong><br>
                        <span
                            class="badge {{ $traslado->tipo_paciente == 'Critico' ? 'badge-danger' : 'badge-success' }}">
                            {{ $traslado->tipo_critico ?: 'N/A' }}
                        </span>
                    </td>
                </tr>
                <!-- Antecedentes en fila completa -->
                <tr>
                    <td colspan="3">
                        <strong>Antecedentes Clínicos</strong><br>
                        {{ $traslado->antecendetes_clinicos ?: 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>


        <div class="section">
            <div class="section-title">Signos Vitales</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Presión Arterial</th>
                        <th>FC</th>
                        <th>FR</th>
                        <th>Temperatura</th>
                        <th>SAT. Oxígeno</th>
                        <th>HGT</th>
                        <th>Glasgow</th>
                        <th>Hora de toma</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $signosVitales = is_string($traslado->signos_vitales)
                            ? json_decode($traslado->signos_vitales, true)
                            : $traslado->signos_vitales;
                    @endphp

                    @if ($signosVitales && count($signosVitales) > 0)
                        @foreach ($signosVitales as $signo)
                            <tr>
                                <td>{{ $signo['presion_arterial'] ?? 'N/A' }}</td>
                                <td>{{ $signo['FC'] ?? 'N/A' }} ppm</td>
                                <td>{{ $signo['fr'] ?? 'N/A' }} ppm</td>
                                <td>
                                    {{-- En el caso de "Temp" se guarda como un objeto, extraemos su valor --}}
                                    @if (is_array($signo['Temp']))
                                        {{ $signo['Temp'][''] ?? 'N/A' }}
                                    @else
                                        {{ $signo['Temp'] ?? 'N/A' }}
                                    @endif
                                    °C
                                </td>
                                <td>{{ $signo['satoxi'] ?? 'N/A' }} %</td>
                                <td>{{ $signo['HGT'] ?? 'N/A' }} mg/dl</td>
                                <td>{{ $signo['glasgow'] ?? 'N/A' }} pts</td>
                                <td>{{ $signo['Hora'] ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">N/A</td>
                        </tr>
                    @endif
                </tbody>
            </table>

        </div>



        <!-- Datos Obstétricos y Neonatales -->
        <div class="section">
            <div class="section-title">Datos Obstétricos</div>
            <table class="table">
                <thead>
                    <tr>
                        <th>G</th>
                        <th>P</th>
                        <th>PR</th>
                        <th>A</th>
                        <th>V</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $formula_obstetrica = is_string($traslado->formula_obstetrica)
                            ? json_decode($traslado->formula_obstetrica, true)
                            : $traslado->formula_obstetrica;
                    @endphp

                    @if ($formula_obstetrica && count($formula_obstetrica) > 0)
                        @foreach ($formula_obstetrica as $signo)
                            <tr>
                                <td>{{ $signo['G'] ?? 'N/A' }}</td>
                                <td>{{ $signo['P'] ?? 'N/A' }}</td>
                                <td>{{ $signo['PR'] ?? 'N/A' }}</td>
                                <td>{{ $signo['A'] ?? 'N/A' }}</td>
                                <td>{{ $signo['V'] ?? 'N/A' }}</td>

                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="8">N/A</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <table class="table">
                <!-- Row 1: Fórmula Obstétrica, Edad Gestacional y Fecha Probable de Parto -->
                <tr>
                    <td>
                        <strong>Edad Gestacional</strong><br>
                        {{ $traslado->edad_gestacional ?: 'N/A' }} Semanas
                    </td>
                    <td>
                        <strong>Fecha Probable de Parto</strong><br>
                        {{ $traslado->fecha_probable_parto ?: 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="section">
            <div class="section-title">Datos de Evaluación Obstétricas</div>
            <table class="table">
                <tr>
                    <td>
                        <strong>Dilatación</strong><br>
                        {{ $traslado->dilatacion ?: 'N/A' }} cm
                    </td>
                    <td>
                        <strong>Borramiento</strong><br>
                        {{ $traslado->borramiento ?: 'N/A' }} %
                    </td>
                    <td>
                        <strong>Hora Obstétrica</strong><br>
                        {{ $traslado->hora_obstetrica ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>FCF</strong><br>
                        {{ $traslado->fcf ?: 'N/A' }} ppm
                    </td>
                </tr>
                <tr>

                    <td>
                        <strong>Membranas Íntegras</strong><br>
                        <span
                            class="badge {{ $traslado->membranas_integras == 'Sí' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->membranas_integras ?: 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <strong>Movimientos Fetales</strong><br>
                        <span class="badge {{ $traslado->mov_fetales == 'Sí' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->mov_fetales ?: 'N/A' }}
                        </span>
                    </td>
                    <td colspan="2">
                        <strong>Trabajo de Parto</strong><br>
                        <span class="badge {{ $traslado->mov_fetales == 'Sí' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->trabajo_parto ?: 'N/A' }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Contracciones</strong><br>
                        {{ $traslado->contracciones ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Frecuencia de Contracciones</strong><br>
                        {{ $traslado->frecuencia ?: 'N/A' }} ppm
                    </td>
                    <td colspan="2">
                        <strong>Posición</strong><br>
                        {{ $traslado->posicion ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <strong>Datos RN / Neonato</strong><br>
                        {{ $traslado->datos_rn_neonato ?: 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>
        <div class="section">
            <div class="section-title">Asistencia y Requerimientos</div>
            <table class="table">
                <tr>
                    <td colspan="2">
                        <strong>Oxigenoterapia</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->requerimientos_oxigenoterapia ?: 'N/A' }}
                        </span>
                    </td>
                    <td>
                        <strong>FIO<sub>2</sub></strong><br>
                        {{ $traslado->fio2 ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Asistencia Ventilatoria</strong><br>
                        <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->asistencia_ventilatoria ?: 'N/A' }}
                        </span>
                    </td>
                    <td colspan="1">
                        <strong>Modo de Ventilación</strong><br>
                        {{ $traslado->modo_ventilacion ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>VT</strong><br>
                        {{ $traslado->vt ?: 'N/A' }}
                    </td>

                </tr>
                <!-- Row 2: FIO<sub>2</sub>, VM y Modo de Ventilación -->
                <tr>
                    <td>
                        <strong>VM</strong><br>
                        {{ $traslado->VM ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Volumen Minuto</strong><br>
                        {{ $traslado->volmin ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Relación I/E</strong><br>
                        {{ $traslado->relacion_ie ?: 'N/A' }}
                    </td>
                </tr>
                <tr>
                    <td>
                        <strong>Frecuencia Respiratoria</strong><br>
                        {{ $traslado->fr ?: 'N/A' }} ppm
                    </td>
                    <td>
                        <strong>PEEP</strong><br>
                        {{ $traslado->peep ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Trigger</strong><br>
                        {{ $traslado->trigger ?: 'N/A' }}
                    </td>
                </tr>
                <!-- Row 5: Prioridad y Programado -->
                <tr>
                    <td colspan="3">
                        <strong>Bombas de Infusión</strong><br>
                        {{ $traslado->bombas_infusion ?: 'N/A' }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- Cierre y Seguimiento -->
        <div class="section">
            <div class="section-title">Cierre</div>
            <table class="table">
                <!-- Row 1: Justificación, Razón y Usuario de Cierre -->
                <tr>
                    <td>
                        <strong>Justificación de Cierre</strong><br>
                        {{ $traslado->justificacion_cierre ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Razón de Cierre</strong><br>
                        {{ $traslado->razon_cierre ?: 'N/A' }}
                    </td>
                    <td>
                        <strong>Usuario de Cierre</strong><br>
                        {{ $traslado->usuario_cierre ?: 'N/A' }} ({{ $traslado->doctor_numero ?: 'N/A' }})
                    </td>
                </tr>
                <!-- Row 2: Estado y User ID -->
                <tr>
                    <td colspan="3">
                        <strong>Estado</strong><br>
                        <span class="badge {{ $traslado->estado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->estado ?: 'N/A' }}
                        </span>
                    </td>

                </tr>
            </table>
        </div>
        <div class="section-title">Seguimiento de Traslado</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nota</th>
                    <th>Usuario</th>
                    <th>Fecha</th>

                </tr>
            </thead>
            <tbody>
                @php
                    $notas_seguimiento = is_string($traslado->notas_seguimiento)
                        ? json_decode($traslado->notas_seguimiento, true)
                        : $traslado->notas_seguimiento;
                @endphp

                @if ($notas_seguimiento && count($notas_seguimiento) > 0)
                    @foreach ($notas_seguimiento as $signo)
                        <tr>
                            <td>{{ $signo['nota'] ?? 'N/A' }}</td>
                            <td>{{ $signo['usuario'] ?? 'N/A' }} ({{ $signo['operador_numero'] ?? 'N/A' }})</td>
                            <td>{{ $signo['fecha'] ?? 'N/A' }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="8">N/A</td>
                    </tr>
                @endif
            </tbody>
        </table>

    </div>
    </div>
</body>


<footer class="footer">
    &copy; 2025 - Traslados Secundarios || Dirección del Sistema de Emergencias Médicas || Unidad de Informática
</footer>

</html>
