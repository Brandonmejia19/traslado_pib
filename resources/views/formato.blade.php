<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formato de Traslado</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9fafb;
            color: #374151;
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
    <img src="{{ public_path('images/logo222.svg') }}" alt="Logo" align="left" >
    <span>Dirección del Sistema de Emergencias Médicas 132</span>
</header>

<body>
    <div class="container">
        <div class="header">Detalles del Traslado - {{ $traslado->correlativo }}</div>

        <div class="section">
            <div class="section-title">Información General</div>
            <table class="table">
                <tr>
                    <th>Asunto</th>
                    <td> <span
                            class="badge {{ $traslado->asunto_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->asunto_traslado }}
                        </span>
                        <span
                            class="badge {{ $traslado->tipo_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->tipo_traslado }}
                        </span>
                    </td>
                    <th>Fecha Creación</th>
                    <td>{{ $traslado->created_at }}</td>
                </tr>
                <tr>
                    <th>Origen</th>
                    <td>
                        <span
                            class="badge {{ $traslado->tipo_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->origen_institucion }}
                        </span> - {{ $traslado->origen_traslado }}
                    </td>
                    <th>Destino</th>
                    <td>
                        <span
                            class="badge {{ $traslado->tipo_traslado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->destino_institucion }}
                        </span> - {{ $traslado->destino_traslado }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="section">
            <div class="section-title">Datos del Paciente</div>
            <table class="table">
                <tr>
                    <th>Nombre</th>
                    <td>{{ $traslado->nombres_paciente }} {{ $traslado->apellidos_paciente }}</td>
                    <th>Edad</th>
                    <td>{{ $traslado->edad_paciente ?? '-' }} - {{ $traslado->componente_edad ?? '-' }}</td>
                    <th>Sexo</th>
                    <td>{{ $traslado->sexo_paciente ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Diagnóstico</th>
                    <td colspan="5">{{ $traslado->diagnostico_paciente ?? '-' }}</td>
                    <!-- Se expande a 5 columnas -->
                </tr>
            </table>

        </div>

        <div class="section">
            <div class="section-title">Estado del Traslado</div>
            <table class="table">
                <tr>
                    <th>Estado</th>
                    <td>
                        <span class="badge {{ $traslado->estado == 'Finalizado' ? 'badge-success' : 'badge-danger' }}">
                            {{ $traslado->estado }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
<footer class="footer">
    &copy; 2025 - Traslados Secundarios || Dirección del Sistema de Emergencias Médicas || Unidad de Informática
</footer>

</html>
