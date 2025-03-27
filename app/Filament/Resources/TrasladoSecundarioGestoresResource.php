<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrasladoSecundarioGestoresResource\Pages;
use App\Filament\Resources\TrasladoSecundarioGestoresResource\RelationManagers;
use App\Models\TrasladoSecundario;
use App\Models\TrasladoSecundarioGestores;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\Alignment;
use Filament\Resources\TrasladoSecundarioResource\Widgets\TrasladosSecundarios;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;
use Filament\Support\View\Components\Modal;
use Filament\Support\Enums\MaxWidth;
use App\Models\HospitalListado;
use App\Models\Ambulancias;
use App\Models\Institucion;
use App\Models\UnidadListado;
use App\Models\ISSListado;
use App\Models\PrivadoListado;
use App\Models\TipoTraslado;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Request;
use OwenIt\Auditing\Models\Audit;
use Psy\VersionUpdater\Installer;

class TrasladoSecundarioGestoresResource extends Resource
{
    protected static ?string $model = TrasladoSecundarioGestores::class;
    protected static ?string $navigationIcon = 'healthicons-o-ambulance';
    protected static ?string $navigationGroup = 'Casos';
    protected static ?string $label = 'Traslados sin recuso asignado';
    protected static ?string $navigationLabel = 'Gestión Recursos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(fn(callable $get) => 'Información de llamada - ' . ($get('correlativo') ?? 'Sin correlativo') . ' - ' . ($get('estado') ?? 'En creación'))
                    ->icon('healthicons-o-call-centre')
                    ->compact()
                    ->description(fn(callable $get) => 'Usuario Asignado: ' . ' ' . ($get('operador_nombre') ?? 'Sin usuario') . ' - Puesto: ' . ($get('operador_numero') ?? 'Sin numero'))
                    ->schema(components: [
                        Forms\Components\TextInput::make('numero_llamada')
                            ->readOnly()
                            ->placeholder('Telefono Origen')
                            ->required()
                            ->columnspan(1)
                            ->mask('99999999')
                            ->numeric()
                            ->prefixicon('healthicons-o-call-centre')
                            ->maxLength(255),
                        Forms\Components\Fieldset::make('Cierre de Caso')
                            ->hidden(
                                fn(callable $get) =>
                                $get('estado') != 'Finalizado'
                            )->columns(3)
                            ->schema([
                                Forms\Components\Textarea::make('justificacion_cierre')
                                    ->label('Justificación de Cierre')
                                    ->readOnly()
                                    ->placeholder('Por favor, ingrese una justificación para cerrar este caso')
                                    ->required(),
                                Forms\Components\TextInput::make('razon_cierre')
                                    ->readOnly()
                                    ->prefixicon('heroicon-o-exclamation-circle')
                                    ->label('Razón de Cierre')
                                    ->required(),
                                Forms\Components\TextInput::make('usuario_cierre')
                                    ->label('Doctor de cierre')
                                    ->prefixicon('healthicons-o-doctor')
                                    ->default(Auth::user()->name)
                                    ->readOnly(),
                                Forms\Components\TextInput::make('doctor_numero')
                                    ->readOnly()
                                    ->prefixicon('heroicon-o-exclamation-circle')
                                    ->label('PP')
                                    ->required(),
                            ])
                    ])->columns(3),
                Forms\Components\Section::make('Información')
                    ->compact()
                    ->icon('healthicons-o-ambulance')
                    ->schema([
                        Forms\Components\Fieldset::make('Información de Solicitud')
                            ->columns(6)
                            ->schema(components: [
                                Forms\Components\ToggleButtons::make('asunto_traslado')
                                    ->disabled()
                                    ->label('Tipo')
                                    ->reactive()
                                    ->default('Traslado de Paciente')
                                    ->required()
                                    ->options([
                                        'Traslado de Paciente' => 'Traslado de Paciente',
                                        'Transporte de Paciente' => 'Transporte de Paciente'
                                    ])
                                    ->icons([
                                        'Traslado de Paciente' => 'healthicons-o-hospitalized',
                                        'Transporte de Paciente' => 'healthicons-o-ambulance',
                                    ])
                                    ->inline()->columnSpanFull(),
                                Forms\Components\ToggleButtons::make('tipo_traslado_id')
                                    ->label('Tipo de Traslado')
                                    ->required()
                                    ->reactive()
                                    ->disabled()
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Traslado de Paciente')
                                    ->options(TipoTraslado::all()->pluck('nombre', 'id')) // Se muestra el nombre, pero guarda el ID
                                    ->icons([
                                        1 => 'healthicons-o-ambulance',
                                        2 => 'healthicons-o-ambulance',
                                        3 => 'healthicons-o-ambulance',
                                        4 => 'healthicons-o-ambulance',
                                        5 => 'healthicons-o-ambulance',
                                        6 => 'healthicons-o-ambulance',
                                        7 => 'healthicons-o-ambulance',
                                        8 => 'healthicons-o-ambulance',
                                        9 => 'healthicons-o-ambulance',
                                    ])
                                    ->afterStateUpdated(
                                        fn($state, callable $set) =>
                                        $set('tipo_traslado', TipoTraslado::find($state)?->nombre)
                                    ) // Cuando el usuario selecciona, actualiza el nombre automáticamente
                                    ->inline()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('tipo_traslado')
                                    ->label('')
                                    ->readOnly()
                                    ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                    ->columnSpanFull()
                                    ->reactive(),
                                Forms\Components\ToggleButtons::make('tipo_traslado')
                                    ->label('Tipo de Transporte')->required()
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Transporte de Paciente')
                                    ->options(TipoTraslado::whereIn('id', [1, 4, 9])->pluck('nombre', 'id'))
                                    ->icons([
                                        1 => 'healthicons-o-ambulance',
                                        2 => 'healthicons-o-ambulance',
                                        3 => 'healthicons-o-ambulance',
                                        4 => 'healthicons-o-ambulance',
                                        5 => 'healthicons-o-ambulance',
                                        6 => 'healthicons-o-ambulance',
                                        7 => 'healthicons-o-ambulance',
                                        8 => 'healthicons-o-ambulance',
                                        9 => 'healthicons-o-ambulance',

                                    ])
                                    ->inline()
                                    ->columnSpanFull(),
                                /* Forms\Components\TextInput::make('tipo_traslado_id')
                                     ->numeric(),*/
                                Forms\Components\TextInput::make('nombre_medico_solicitante')
                                    ->placeholder('Nombre Médico Solicitante')
                                    ->label('Nombre médico solicitante')
                                    ->prefixicon('healthicons-o-doctor')
                                    ->readOnly()
                                    ->columnSpan(2)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('telefono_medico_solicitante')
                                    ->prefixicon('healthicons-o-phone')
                                    ->readOnly()
                                    ->mask('99999999')
                                    ->label('Teléfono médico solicitante')
                                    ->tel()
                                    ->columnSpan(2)
                                    ->placeholder('0000-0000')
                                    ->maxLength(255),
                                Forms\Components\Select::make('prioridad')
                                    ->prefixIcon('heroicon-o-exclamation-triangle')
                                    ->options([
                                        '1' => '1',
                                        '2' => '2',
                                        '3' => '3',
                                        '4' => '4',
                                    ])->required()
                                    ->disabled()
                                    ->placeholder('Opción')
                                    ->columnSpan(1)
                                    ->live(onBlur: true)
                                    ->reactive()
                                    ->beforeStateDehydrated(function ($state, callable $set) {
                                        // Cambia el color del ColorPicker basado en la prioridad seleccionada
                                        $color = match ($state) {
                                            '1' => '#f9473c', // Rojo para prioridad 1
                                            '2' => '#f9c62f', // Naranja para prioridad 2
                                            '3' => '#27fb1d', // Amarillo para prioridad 3
                                            '4' => '#00ffff', // Verde para prioridad 4
                                            default => '#FFF1', // Blanco como valor por defecto
                                        };
                                        $set('color', $color);
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Cambia el color del ColorPicker basado en la prioridad seleccionada
                                        $color = match ($state) {
                                            '1' => '#f9473c', // Rojo para prioridad 1
                                            '2' => '#f9c62f', // Naranja para prioridad 2
                                            '3' => '#27fb1d', // Amarillo para prioridad 3
                                            '4' => '#00ffff', // Verde para prioridad 4
                                            default => '#FFF1', // Blanco como valor por defecto
                                        };
                                        $set('color', $color);
                                    }),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('.')
                                    ->live(onBlur: true)
                                    ->columnSpan(1)
                                    ->extraAttributes(['style' => 'pointer-events: none; width: 0px; height: 0px; border-radius: 0px;']),
                                Forms\Components\TextInput::make('jvpe_medico_entrega')
                                    ->readOnly()
                                    ->tel()->columnspan(2)
                                    ->label('JVPE médico solicitante')
                                    ->placeholder('JVPE')
                                    ->prefixicon('healthicons-o-stethoscope')
                                    ->maxLength(255),
                            ]),


                        Forms\Components\Fieldset::make('ORIGEN / DESTINO')
                            ->columns(4)
                            ->schema(components: [
                                Forms\Components\ToggleButtons::make('origen_institucion')
                                    ->disabled()
                                    ->columnspan(2)
                                    ->default('Hospital')
                                    ->reactive()
                                    ->label('Tipo Origen')
                                    ->options(Institucion::all()->pluck('nombre', 'id'))
                                    ->icons([
                                        '1' => 'healthicons-o-hospital',
                                        '2' => 'healthicons-o-ambulatory-clinic',
                                        '3' => 'healthicons-o-rural-post',
                                        '4' => 'healthicons-o-emergency-post',
                                        '5' => 'healthicons-o-home',
                                        '6' => 'healthicons-o-health-alt',
                                    ])
                                    ->inline(),
                                Forms\Components\Select::make('origen_traslado')
                                    ->prefixicon('healthicons-o-hospital')
                                    ->searchable()
                                    ->reactive()
                                    ->label('Origen')
                                    ->columnspan(2)
                                    ->hidden(fn(callable $get) => in_array($get('origen_institucion'), [5, 6]))
                                    ->options(function (callable $get) {
                                        $destino = $get('origen_institucion');
                                        switch ($destino) {
                                            case '1':
                                                return HospitalListado::query()->pluck('nombre', 'id');
                                            case '2':
                                                return UnidadListado::query()->pluck('nombre', 'id');
                                            case '3':
                                                return ISSListado::query()->pluck('nombre', 'id');
                                            case '4':
                                                return PrivadoListado::query()->pluck('nombre', 'id');
                                            case '5':
                                            default:
                                                return [];
                                        }
                                    })
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $tipo = $get('origen_institucion');
                                        switch ($tipo) {
                                            case '1':
                                                $registro = HospitalListado::find($state);
                                                break;
                                            case '2':
                                                $registro = UnidadListado::find($state);
                                                break;
                                            case '3':
                                                $registro = ISSListado::find($state);
                                                break;
                                            case '4':
                                                $registro = PrivadoListado::find($state);
                                                break;
                                            default:
                                                $registro = null;
                                        }
                                        $set('origen_traslado_nombre', $registro?->nombre);
                                    }),
                                Forms\Components\TextInput::make('origen_traslado_nombre')
                                    ->readOnly()
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('origen_institucion'), [5, 6]))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\Select::make('servicio_origen')
                                    ->prefixicon('healthicons-o-health-worker-form')
                                    ->disabled()
                                    ->columnspan(2)
                                    ->options([
                                        'Hospitalización' => 'Hospitalización',
                                        'Emergencia' => 'Emergencia',
                                        'Rayos X' => 'Rayos X',
                                        'UCI' => 'UCI',
                                        'Pediatria' => 'Pediatria',
                                        'Ginecología' => 'Ginecología',
                                        'Medicina Interna' => 'Medicina Interna',
                                        'Cirugía' => 'Cirugía',
                                        'Traumatología' => 'Traumatología',
                                        'Oncología' => 'Oncología',
                                        'Otro' => 'Otro',
                                    ]),
                                Forms\Components\TextInput::make('numero_cama_origen')
                                    ->readOnly()
                                    ->numeric()->columnspan(1)
                                    ->prefixicon('healthicons-o-hospitalized')
                                    ->placeholder('Número de Cama')
                                    ->label('Número de Cama'),
                                Forms\Components\Textarea::make('observaciones_origen')
                                    ->readOnly()
                                    ->autosize()
                                    ->columnspan(1)
                                    ->placeholder('Observaciones')
                                    ->label('Observaciones'),
                                Forms\Components\TextInput::make('origen_institucion')
                                    ->readOnly()
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('origen_traslado'), [5, 6]))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\ToggleButtons::make('destino_institucion')
                                    ->disabled()
                                    ->reactive()->columnspan(2)
                                    ->label('Tipo Destino')
                                    ->default('Hospital')
                                    ->options(Institucion::all()->pluck('nombre', 'id'))
                                    ->icons([
                                        '1' => 'healthicons-o-hospital',
                                        '2' => 'healthicons-o-ambulatory-clinic',
                                        '3' => 'healthicons-o-rural-post',
                                        '4' => 'healthicons-o-emergency-post',
                                        '5' => 'healthicons-o-home',
                                        '6' => 'healthicons-o-health-alt',
                                    ])
                                    ->inline(),
                                Forms\Components\Select::make('destino_traslado')
                                    ->prefixicon('healthicons-o-hospital')
                                    ->searchable()
                                    ->label('Destino')
                                    ->columnspan(2)
                                    ->hidden(fn(callable $get) => in_array($get('destino_institucion'), [5, 6]))
                                    ->options(function (callable $get) {
                                        $destino = $get('destino_institucion');
                                        switch ($destino) {
                                            case '1':
                                                return HospitalListado::query()->pluck('nombre', 'id');
                                            case '2':
                                                return UnidadListado::query()->pluck('nombre', 'id');
                                            case '3':
                                                return ISSListado::query()->pluck('nombre', 'id');
                                            case '4':
                                                return PrivadoListado::query()->pluck('nombre', 'id');
                                            case '5':
                                            default:
                                                return [];
                                        }
                                    })
                                    ->reactive()->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $tipo = $get('destino_institucion');
                                        switch ($tipo) {
                                            case '1':
                                                $registro = HospitalListado::find($state);
                                                break;
                                            case '2':
                                                $registro = UnidadListado::find($state);
                                                break;
                                            case '3':
                                                $registro = ISSListado::find($state);
                                                break;
                                            case '4':
                                                $registro = PrivadoListado::find($state);
                                                break;
                                            default:
                                                $registro = null;
                                        }
                                        $set('destino_traslado_nombre', $registro?->nombre);
                                    }),
                                Forms\Components\TextInput::make('destino_traslado_nombre')
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('destino_institucion'), [5, 6]))
                                    ->readOnly()
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\Select::make('servicio_destino')
                                    ->prefixicon('healthicons-o-health-worker-form')
                                    ->disabled()
                                    ->searchable()->columnspan(2)
                                    ->options([
                                        'Hospitalización' => 'Hospitalización',
                                        'Emergencia' => 'Emergencia',
                                        'Rayos X' => 'Rayos X',
                                        'UCI' => 'UCI',
                                        'Pediatria' => 'Pediatria',
                                        'Ginecología' => 'Ginecología',
                                        'Medicina Interna' => 'Medicina Interna',
                                        'Cirugía' => 'Cirugía',
                                        'Traumatología' => 'Traumatología',
                                        'Oncología' => 'Oncología',
                                        'Otro' => 'Otro',
                                    ]),
                                Forms\Components\TextInput::make('numero_cama_destino')
                                    ->readOnly()
                                    ->numeric()->columnspan(1)
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Traslado de Paciente')
                                    ->prefixicon('healthicons-o-hospitalized')
                                    ->placeholder('Número de Cama')
                                    ->label('Número de Cama')
                                    ->maxLength(3),
                                Forms\Components\Textarea::make('observaciones_destino')
                                    ->readOnly()
                                    ->autosize()
                                    ->columnspan(1)
                                    ->placeholder('Observaciones')
                                    ->label('Observaciones'),
                                Forms\Components\TextInput::make('nombre_medico_recibe')
                                    ->placeholder('Nombre del Médico que Recibe')
                                    ->label('Nombre médico o persona receptora')->columnspan(2)
                                    ->readOnly()
                                    ->prefixicon('healthicons-o-doctor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('jvpe_medico_recibe')
                                    ->readOnly()
                                    ->tel()->columnspan(1)
                                    ->label('JVPE médico receptor')
                                    ->placeholder('JVPE')
                                    ->prefixicon('healthicons-o-stethoscope')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono_medico_recibe')
                                    ->tel()->columnspan(1)
                                    ->readOnly()
                                    ->mask('99999999')
                                    ->label('Teléfono médico receptor')
                                    ->placeholder('0000-0000')
                                    ->prefixicon('healthicons-o-phone')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('origen_traslado_nombre')
                                    ->label('')->columnspan(2)
                                    ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                    ->reactive(),
                                Forms\Components\TextInput::make('destino_traslado_nombre')
                                    ->label('')->columnspan(2)
                                    ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                    ->reactive(),
                            ]),


                        /////////////////////GESTOR////////////////////////
                        Forms\Components\Fieldset::make('DATOS DE RECURSOS')
                            ->columns(4)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('Fecha del Traslado')
                                    ->default(fn() => Carbon::now())
                                    ->readonly()->columnspan(1)
                                    ->prefixicon('heroicon-o-calendar')
                                    ->required(),
                                Forms\Components\ToggleButtons::make('programado')
                                    ->label('Traslado Programado')
                                    ->disabled()
                                    ->default('NO')
                                    ->reactive()
                                    ->colors([
                                        'SI' => 'success',
                                        'NO' => 'danger',
                                    ])
                                    ->icons([
                                        'SI' => 'heroicon-o-check',
                                        'NO' => 'heroicon-o-x-circle',
                                    ])
                                    ->options([
                                        'SI' => 'SI',
                                        'NO' => 'NO',
                                    ])
                                    ->inline()
                                    ->columnspan(span: 1),
                                Forms\Components\DateTimePicker::make('fecha_traslado')
                                    ->readOnly()
                                    ->label(label: 'Fecha de Programación')
                                    ->disabled(fn(callable $get) => $get('programado') != 'SI')
                                    ->columnspan(1)
                                    ->hidden(fn(callable $get) => $get('programado') != 'SI')
                                    ->required()
                                    ->prefixicon('heroicon-o-calendar'),

                                Forms\Components\Select::make('tipo_unidad_sugerida')
                                    ->disabled()
                                    ->searchable()
                                    ->prefixicon('healthicons-o-ambulance')
                                    ->label('Tipo Unidad Sugeridad')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'C' => 'C',
                                        'M' => 'M',
                                    ])->columnspan(1),
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('tipo_ambulancia')
                                            ->label('Tipo Unidad')
                                            ->prefixicon('healthicons-o-ambulance')
                                            ->options([
                                                'A' => 'A',
                                                'B' => 'B',
                                                'C' => 'C',
                                                'M' => 'M',
                                            ])->columnspan(1),
                                        Forms\Components\Select::make('ambulancia_id')
                                            ->placeholder('Unidad')
                                            ->label('Unidad')
                                            ->options(Ambulancias::query()->pluck('unidad', 'id'))
                                            ->searchable(auth()->user()->cargo != 'Gestor' || auth()->user()->cargo != 'Administrador')
                                            /* ->disabled(
                                                 !in_array(auth()->user()->cargo, ['Gestor', 'Administrador'])
                                             )*/ ->columnspan(1)
                                            ->reactive()
                                            ->afterStateUpdated(function ($state, callable $set) {
                                                $ambulancia = Ambulancias::find($state);

                                                // Set gestor_numero a los últimos 2 dígitos de la IP
                                                $ip = Request::ip();
                                                if (config('app.behind_cdn')) {
                                                    $ip = Request::server(config('app.behind_cdn_http_header_field', 'HTTP_X_FORWARDED_FOR')) ?? $ip;
                                                }
                                                $segments = explode('.', $ip);
                                                $lastSegment = end($segments);
                                                $lastTwoDigits = substr($lastSegment, -2);

                                                // Setear los campos
                                                if ($ambulancia) {
                                                    $set('gestor_numero', $lastTwoDigits);
                                                    $set('gestor_nombre', auth()->user()->name);
                                                }
                                            })

                                            ->prefixicon('healthicons-o-ambulance'),
                                        Forms\Components\TextInput::make('gestor_numero')
                                            ->placeholder('Número del Operador')
                                            ->columnspan(1)
                                            ->label('Puesto Gestor')
                                            ->readOnly()
                                            ->prefixicon('healthicons-o-call-centre'),
                                        Forms\Components\TextInput::make('gestor_nombre')
                                            ->prefixicon('healthicons-o-call-centre')
                                            ->placeholder('Gestor Asignado')
                                            ->label('Gestor Asignado')
                                            ->readOnly()
                                            ->columnspan(1)
                                            ->maxLength(255),
                                        Forms\Components\TextInput::make(name: 'ambulancia')
                                            ->placeholder('Unidad')
                                            ->label('')
                                            ->readOnly()
                                            ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                            ->columnspan(1)
                                            ->prefixicon('healthicons-o-ambulance'),
                                    ])->columnspan('full'),
                            ]),
                        Forms\Components\Fieldset::make('DATOS DE PACIENTE')
                            ->columns(8)
                            ->schema([
                                Forms\Components\TextInput::make('nombres_paciente')
                                    ->prefixicon('healthicons-o-person')
                                    ->readOnly()
                                    ->columnspan(2)
                                    ->placeholder('Nombres del Paciente')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('apellidos_paciente')
                                    ->placeholder('Apellidos del Paciente')
                                    ->prefixicon('healthicons-o-person')
                                    ->readOnly()
                                    ->columnspan(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('edad_paciente')
                                    ->readOnly()
                                    ->label('Edad')
                                    ->mask('999')
                                    ->reactive()
                                    ->columnspan(1)
                                    ->placeholder('00')
                                    ->prefixicon('healthicons-o-insurance-card')
                                    ->numeric(),
                                Forms\Components\Select::make('componente_edad')//PONER EN MODEL
                                    ->label('Complemento de edad')->columnspan(1)
                                    ->disabled()
                                    ->columnspan(2)
                                    ->default('Años')
                                    ->options([
                                        'Horas' => 'Horas',
                                        'Días' => 'Días',
                                        'Semanas' => 'Semanas',
                                        'Meses' => 'Meses',
                                        'Años' => 'Años',
                                    ])
                                    ->prefixicon('healthicons-o-insurance-card'),
                                Forms\Components\ToggleButtons::make('sexo_paciente')
                                    ->disabled()
                                    ->inline()
                                    ->reactive()
                                    ->options([
                                        'M' => 'M',
                                        'F' => 'F',
                                    ]),
                                Forms\Components\TextArea::make('registro_expediente')
                                    ->placeholder('Registro de Expediente')
                                    ->readOnly()
                                    ->columnspan(4)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('diagnostico_paciente')
                                    ->readOnly()
                                    ->required()
                                    ->prefixicon('healthicons-o-clinical-f')
                                    ->placeholder('Diagnóstico del Paciente')
                                    ->maxLength(255)
                                    ->columnspan(4),
                            ]),
                        Forms\Components\Fieldset::make('ESTADO DE PACIENTE')
                            ->columns(4)
                            ->schema([
                                Forms\Components\ToggleButtons::make('tipo_paciente')
                                    ->disabled()
                                    ->reactive()
                                    ->default('Estable')
                                    ->options([
                                        'Estable' => 'Estable',
                                        'Critico' => 'Critico',
                                    ])
                                    ->icons([
                                        'Estable' => 'healthicons-o-happy',
                                        'Critico' => 'healthicons-o-bandaged',
                                    ])
                                    ->colors([
                                        'Estable' => 'success',
                                        'Critico' => 'danger',
                                    ])
                                    ->inline()
                                    ->required(),
                                Forms\Components\ToggleButtons::make('tipo_critico')
                                    ->hidden(fn(callable $get) => $get('tipo_paciente') != 'Critico')
                                    ->disabled()
                                    ->options([
                                        'Neonato' => 'Neonato',
                                        'Niño' => 'Niño',
                                        'Adolescente' => 'Adolescente',
                                        'Adulto' => 'Adulto',
                                        'Adulto Mayor' => 'Adulto Mayor',
                                        'Embarazada' => 'Embarazada',
                                        'Puerpera' => 'Puerpera',

                                    ])->inline()
                                    ->icons([
                                        'Neonato' => 'healthicons-o-baby-0203-alt',
                                        'Niño' => 'healthicons-o-boy-0105y',
                                        'Adolescente' => 'healthicons-o-boy-1015y',
                                        'Adulto' => 'healthicons-o-person',
                                        'Adulto Mayor' => 'healthicons-o-old-man',
                                        'Embarazada' => 'healthicons-o-pregnant',
                                        'Puerpera' => 'healthicons-o-pregnant',
                                    ])
                                    ->columnspan(3),
                                Forms\Components\Textarea::make('antecendetes_clinicos')
                                    ->readOnly()
                                    ->label('Antecedentes Clínicos')
                                    ->placeholder('Antecedentes Clínicos')
                                    ->columnSpanFull(),
                            ]),
                        Forms\Components\Fieldset::make('Datos obstétricos')
                            ->hidden(
                                fn(callable $get) =>
                                $get('sexo_paciente') !== 'F' ||  // Si no es femenino, ocultar
                                $get('edad_paciente') <= 12 ||     // Si es menor de 12 años, ocultar
                                $get('edad_paciente') >= 50        // Si es mayor de 50 años, ocultar
                            )->columns(4)
                            ->schema([
                                Forms\Components\Repeater::make('formula_obstetrica')
                                    ->disabled()
                                    ->columns(5)
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->addable(false)
                                    ->columnspan('full')
                                    ->schema([
                                        Forms\Components\TextInput::make('G')->prefixicon('healthicons-o-baby-0203m')->placeholder('G'),
                                        Forms\Components\TextInput::make('P')->prefixicon('healthicons-o-baby-0203m')->placeholder('P'),
                                        Forms\Components\TextInput::make('PR')->prefixicon('healthicons-o-baby-0203m')->placeholder('PR')->label('PR'),
                                        Forms\Components\TextInput::make(name: 'A')->prefixicon('healthicons-o-baby-0203m')->placeholder('A'),
                                        Forms\Components\TextInput::make(name: 'V')->prefixicon('healthicons-o-baby-0203m')->placeholder('V'),

                                    ]),
                                Forms\Components\TextInput::make('edad_gestacional')
                                    ->prefixicon('healthicons-o-baby-0203-alt')
                                    ->suffix('00')
                                    ->placeholder('Semanas')
                                    ->numeric(),
                                Forms\Components\DatePicker::make('fecha_probable_parto')
                                    ->label('Fecha Probable de Parto')
                                    ->prefixicon('heroicon-o-calendar'),
                            ]),
                        Forms\Components\Fieldset::make('Datos de Evaluación Obstétrica')
                            ->columns(5)
                            ->hidden(
                                fn(callable $get) =>
                                $get('sexo_paciente') !== 'F' ||  // Si no es femenino, ocultar
                                $get('edad_paciente') <= 12 ||     // Si es menor de 12 años, ocultar
                                $get('edad_paciente') >= 50        // Si es mayor de 50 años, ocultar
                            )->schema([
                                    Forms\Components\TextInput::make('dilatacion')
                                        ->readOnly()
                                        ->placeholder('0 cm')
                                        ->prefixicon('healthicons-o-blood-pressure')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('borramiento')
                                        ->readOnly()
                                        ->placeholder('0%')
                                        ->prefixicon('healthicons-o-heart')
                                        ->maxLength(255),
                                    Forms\Components\TimePicker::make('hora_obstetrica')
                                        ->default(fn() => Carbon::now()->format('H:i'))
                                        ->readOnly()
                                        ->prefixicon('heroicon-o-clock'),
                                    Forms\Components\TextInput::make('fcf')
                                        ->readOnly()
                                        ->prefixicon('healthicons-o-lungs')
                                        ->placeholder('0 ppm')
                                        ->label('fcf')
                                        ->maxLength(255),
                                    Forms\Components\ToggleButtons::make('membranas_integras')
                                        ->disabled()
                                        ->label('¿Membranas Integras?')
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),
                                    Forms\Components\ToggleButtons::make('mov_fetales')
                                        ->disabled()
                                        ->label('¿Movimientos Fetales?')
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),
                                    Forms\Components\ToggleButtons::make('trabajo_parto')
                                        ->label('¿Trabajo de Parto?')
                                        ->disabled()
                                        ->reactive()
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),

                                    Forms\Components\TextInput::make('contracciones')
                                        ->readOnly()
                                        ->placeholder('0')
                                        ->prefixicon('healthicons-o-blood-pressure')
                                        ->hidden(fn(callable $get) => $get('trabajo_parto') != 'SI')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('frecuencia')
                                        ->prefixicon('healthicons-o-heart')
                                        ->readOnly()
                                        ->placeholder('0 ppm')
                                        ->hidden(fn(callable $get) => $get('trabajo_parto') != 'SI')
                                        ->maxLength(255),
                                    Forms\Components\Select::make('posicion')
                                        ->prefixicon('healthicons-o-baby-0203-alt')
                                        ->disabled()
                                        ->placeholder(placeholder: 'Posición')
                                        ->hidden(condition: fn(callable $get) => $get('trabajo_parto') != 'SI')
                                        ->searchable()
                                        ->options([
                                            'Cefálica' => 'Cefálica',
                                            'Podálica' => 'Podálica',
                                            'Transversa' => 'Transversa',
                                            'Otro' => 'Otro',
                                        ]),
                                    Forms\Components\Textarea::make('datos_rn_neonato')
                                        ->disabled()
                                        ->label('Datos del RN / Neonato')
                                        ->placeholder('Datos del RN / Neonato')
                                        ->columnSpanFull(),
                                ]),
                        Forms\Components\Fieldset::make('SIGNOS VITALES')
                            ->schema([
                                Forms\Components\Repeater::make('signos_vitales')
                                    ->addActionLabel('Nuevo Signo Vital')
                                    ->extraAttributes(['class' => 'bg-yellow-500 text-white'])
                                    ->collapseAllAction(
                                        fn(Action $action) => $action->label('Esconder todo'),
                                    )
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->schema([
                                        Forms\Components\TextInput::make('presion_arterial')
                                            ->prefixicon('healthicons-o-blood-pressure')
                                            ->placeholder('120/80')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('FC')
                                            ->label('FC')
                                            ->prefixicon('healthicons-o-heart')
                                            ->placeholder('95 ppm')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('fr')
                                            ->label('FR')
                                            ->prefixicon('healthicons-o-lungs')
                                            ->placeholder('15')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('Temp.')
                                            ->label('Temperatura')
                                            ->prefixicon('healthicons-o-thermometer')
                                            ->placeholder('36.5 °C')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('satoxi')
                                            ->label('Sat. Oxígeno')
                                            ->prefixicon('healthicons-o-oxygen-tank')
                                            ->placeholder('95%')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('HGT')
                                            ->label('HGT')
                                            ->prefixicon('healthicons-o-blood-bag')
                                            ->placeholder('80 mg/dl')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\TextInput::make('glasgow')
                                            ->label('Escala Glasgow')
                                            ->prefixicon('healthicons-o-neurology')
                                            ->placeholder('15 pts')
                                            ->readOnly(fn($state) => !empty($state)),

                                        Forms\Components\DateTimePicker::make('Hora')
                                            ->label('Hora de Toma')
                                            ->prefixicon('heroicon-o-clock')
                                            ->readOnly(fn($state) => !empty($state)),
                                    ])
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(6)
                                    ->defaultItems(1) // Permite que el usuario agregue nuevos registros
                            ]),
                        Forms\Components\Fieldset::make('REQUERIMIENTOS')
                            ->columns(5)
                            ->schema([
                                Forms\Components\TextInput::make(name: 'fio2')
                                    ->readOnly()
                                    ->numeric()
                                    ->label('Fio2')
                                    ->columnSpan(1)
                                    ->prefixicon('healthicons-o-oxygen-tank')
                                    ->placeholder('Fio2'),
                                Forms\Components\ToggleButtons::make('requerimientos_oxigenoterapia')
                                    ->disabled()
                                    ->required()
                                    ->options([
                                        'NO' => 'NO',
                                        'Canula Nasal' => 'Canula Nasal',
                                        'Venturi' => 'Venturi',
                                        'Mascarilla Simple' => 'Mascarilla Simple',
                                        'Mascarilla Reservorio' => 'Mascarilla Reservorio',
                                    ])->default('NO')
                                    ->icons([
                                        'NO' => 'healthicons-o-oxygen-tank',
                                        'Fio2' => 'healthicons-o-oxygen-tank',
                                        'Canula Nasal' => 'healthicons-o-oxygen-tank',
                                        'Venturi' => 'healthicons-o-oxygen-tank',
                                        'Mascarilla Simple' => 'healthicons-o-oxygen-tank',
                                        'Mascarilla Reservorio' => 'healthicons-o-oxygen-tank',
                                    ])
                                    ->colors([
                                        'NO' => 'danger',
                                        'Fio2' => 'success',
                                        'Canula Nasal' => 'success',
                                        'Venturi' => 'success',
                                        'Mascarilla Simple' => 'success',
                                        'Mascarilla Reservorio' => 'success',
                                    ])
                                    ->inline()
                                    ->columnspan(4),

                            ]),
                        Forms\Components\Fieldset::make('PARAMETROS DE VENTILACIÓN')
                            ->hidden(fn(callable $get) => $get('tipo_paciente') != 'Critico')
                            ->columns(5)
                            ->schema([
                                Forms\Components\ToggleButtons::make('asistencia_ventilatoria')
                                    ->disabled()
                                    ->reactive()
                                    ->default('NO')
                                    ->label('Asistencia Ventilatoria (VM)')
                                    ->options([
                                        'NO' => 'NO',
                                        'SI' => 'SI',
                                    ])
                                    ->colors([
                                        'NO' => 'danger',
                                        'SI' => 'success',
                                    ])
                                    ->icons([
                                        'NO' => 'healthicons-o-ventilator',
                                        'SI' => 'healthicons-o-ventilator',
                                    ])
                                    ->inline(),
                                Forms\Components\Select::make('modo_ventilacion')
                                    ->prefixicon('healthicons-o-ventilator')
                                    ->placeholder('')
                                    ->disabled()
                                    ->searchable()
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->options([
                                        'Volumen Controlado' => 'Volumen Controlado',
                                        'Presión Controlada' => 'Presión Controlada',
                                        'Volumen Asistido' => 'Volumen Asistido',
                                        'Presión Asistida' => 'Presión Asistida',
                                        'Biphasic' => 'Biphasic',
                                        'Otros' => 'Otros',
                                    ]),
                                Forms\Components\TextInput::make(name: 'vt')
                                    ->disabled()
                                    ->numeric()
                                    ->label('VT')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('VT'),
                                Forms\Components\TextInput::make(name: 'volmin')
                                    ->disabled()
                                    ->numeric()
                                    ->label('VOL MIN')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('VOL MIN'),
                                Forms\Components\Select::make(name: 'relacion_ie')
                                    ->disabled()
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->options([
                                        'I' => 'I',
                                        'E' => 'E',
                                    ])
                                    ->placeholder('Relación I:E')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->label('Relación I:E'),
                                Forms\Components\TextInput::make(name: 'fr')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->disabled()
                                    ->label('fr')
                                    ->placeholder('fr'),
                                Forms\Components\TextInput::make(name: 'peep')
                                    ->disabled()
                                    ->label('peep')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('peep'),
                                Forms\Components\Select::make(name: 'trigger')
                                    ->options(['SI' => 'SI', 'NO' => 'NO'])
                                    ->disabled()
                                    ->label('trigger')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag'),
                                Forms\Components\TextInput::make('bombas_infusion')
                                    ->placeholder('#')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->disabled()
                                    ->numeric()
                                    ->prefixicon('healthicons-o-blood-bag'),
                            ]),

                        Forms\Components\Fieldset::make('Información de Usuario')
                            ->columns(4)
                            ->schema([

                                Forms\Components\TextInput::make('operador_numero')
                                    ->placeholder('Número del Operador')
                                    ->columnspan(1)
                                    ->label('Puesto')
                                    ->default(function () {
                                        $ip = Request::ip();

                                        if (config('app.behind_cdn')) {
                                            $ip = Request::server(config('app.behind_cdn_http_header_field', 'HTTP_X_FORWARDED_FOR')) ?? $ip;
                                        }


                                        $segments = explode('.', $ip); // Divide la IP en segmentos

                                        $lastSegment = end($segments); // Obtiene el último segmento

                                        // Obtiene los últimos 2 dígitos del segmento
                                        $lastTwoDigits = substr($lastSegment, -2);

                                        return $lastTwoDigits;
                                    })
                                    ->readOnly()
                                    ->prefixicon('healthicons-o-call-centre')
                                    ->maxLength(length: 255),
                                Forms\Components\TextInput::make('operador_nombre')
                                    ->prefixicon('healthicons-o-call-centre')
                                    ->default(Auth::user()->name)
                                    ->placeholder('Usuario Asignado')
                                    ->label('Usuario Asignado')
                                    ->readOnly()
                                    ->columnspan(2)
                                    ->maxLength(255),
                            ]),
                        Forms\Components\Section::make('Notas de Seguimiento')
                            ->collapsible()
                            ->schema([
                                Forms\Components\Repeater::make('notas_seguimiento')
                                    //   ->label('Notas de Seguimiento - '.fn(callable $get) => 'Información de llamada - ' . ($get('usuario') ?? 'Sin Usuario'))
                                    ->columns(4)
                                    ->label('')
                                    ->addActionLabel('Agregar nueva Nota')
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->schema([
                                        Forms\Components\TextArea::make('nota')
                                            ->label(fn(callable $get) => 'Nota por ' . auth()->user()->name . ' / PP: ' . ($get('operador_numero') ?? 'Sin Usuario') . ' - ' . ($get('fecha') ?? 'Sin Fecha'))
                                            ->readOnly(fn($state) => !empty($state))
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('usuario')
                                            ->label('')
                                            ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                            ->default(auth()->user()->name)
                                            ->readOnly() // No editable, solo informativo
                                            ->columnSpan(1),
                                        Forms\Components\TextInput::make('operador_numero')
                                            ->label('')
                                            ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                            ->default(function () {
                                                $ip = Request::ip();
                                                if (config('app.behind_cdn')) {
                                                    $ip = Request::server(config('app.behind_cdn_http_header_field', 'HTTP_X_FORWARDED_FOR')) ?? $ip;
                                                }
                                                $segments = explode('.', $ip); // Divide la IP en segmentos
                                                $lastSegment = end($segments); // Obtiene el último segmento
                                                // Obtiene los últimos 2 dígitos del segmento
                                                $lastTwoDigits = substr($lastSegment, -2);
                                                return $lastTwoDigits;
                                            })->readOnly() // No editable, solo informativo
                                            ->columnSpan(1),
                                        Forms\Components\DateTimePicker::make('fecha')
                                            ->label('')
                                            ->extraAttributes(['style' => 'display: none;'])///OCULTAR PERO AUN GUARDA
                                            ->default(now()) // Se asigna la fecha y hora actual
                                            ->readOnly() // No editable, solo informativo
                                            ->columnSpan(1),
                                    ])
                                    //->collapsible() // Para que las entradas no ocupen tanto espacio visualmente
                                    ->addActionLabel('Agregar Nota') // Personaliza el botón de agregar
                                    ->columnSpanFull(),
                            ]),

                    ])
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tipo_paciente')
                    ->label('Estado P.')
                    ->default('---')
                    ->sortable()
                    ->badge()
                    ->color(function ($record) {
                        $tipo_paciente = $record->tipo_paciente;
                        if ($tipo_paciente === "Critico") {
                            return 'danger';
                        }
                        if ($tipo_paciente === "Estable") {
                            return 'success';
                        }
                    })
                    ->icon(function ($record) {
                        $tipo_paciente = $record->tipo_paciente;
                        if ($tipo_paciente === "Critico") {
                            return 'healthicons-o-bandaged';
                        }
                        if ($tipo_paciente === "Estable") {
                            return 'healthicons-o-happy';
                        }
                    })
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('correlativo')
                    ->default('---')
                    ->sortable()
                    ->badge()->alignment(Alignment::Center)

                    ->label('Correlativo')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('diagnostico_paciente')
                    ->icon('healthicons-o-clinical-f')
                    ->default('---')
                    ->sortable()
                    ->limit(15)
                    ->alignment(Alignment::Center)
                    ->label('Diagnóstico')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Prioridad')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('origen_traslado_nombre')
                    ->limit(25)
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Origen')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('destino_traslado_nombre')
                    ->limit(25)
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Destino')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombres_paciente')
                    ->limit(15)
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Nombres')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos_paciente')
                    ->limit(15)
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Apellidos')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('edad_paciente')
                    ->numeric()
                    ->description(fn(TrasladoSecundarioGestores $record): string => $record->componente_edad)
                    ->label('Edad')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ambulancia.unidad')
                    ->default('---')
                    ->icon('healthicons-o-ambulance')
                    ->color('primary')
                    ->sortable()->alignment(Alignment::Center)
                    ->label('Ambulancia')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('programado')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)
                    ->badge()
                    ->color(function ($record) {
                        $programado = $record->programado;
                        if ($programado === "SI") {
                            return 'success';
                        }
                        if ($programado === "NO") {
                            return 'danger';
                        }
                    })
                    ->icon(function ($record) {
                        $programado = $record->programado;
                        if ($programado === "SI") {
                            return 'heroicon-o-check-circle';
                        }
                        if ($programado === "NO") {
                            return 'heroicon-o-x-circle';
                        }
                    })
                    ->label('¿Programado?')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()

                    ->sortable()->alignment(Alignment::Center)

                    ->label('Fecha de Creación')
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('estado')
                    ->default('----')
                    ->badge()
                    ->color(function ($record) {
                        $estado = $record->estado;
                        if ($estado === "Pendiente") {
                            return 'warning';
                        }
                        if ($estado === "En Curso") {
                            return 'info';
                        }
                        if ($estado === "Finalizado") {
                            return 'success';
                        }
                        if ($estado === "Cancelado") {
                            return 'danger';
                        }
                    })
                    ->icon(function ($record) {
                        $estado = $record->estado;
                        if ($estado === "Pendiente") {
                            return 'heroicon-o-clock';
                        }
                        if ($estado === "En Curso") {
                            return 'heroicon-o-play';
                        }
                        if ($estado === "Finalizado") {
                            return 'heroicon-o-check';
                        }
                        if ($estado === "Cancelado") {
                            return 'heroicon-o-x';
                        }
                    })
                    ->sortable()->alignment(Alignment::Center)
                    ->label('Estado')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                /////////////////////////////////////////////////////////////////////////////////////////////////
                Tables\Columns\TextColumn::make('numero_llamada')
                    ->default('---')
                    ->icon('heroicon-o-phone')
                    ->formatStateUsing(function ($state) {
                        // Si no hay valor, devuelve el valor por defecto.
                        if (empty($state)) {
                            return '---';
                        }
                        // Se asegura de tener 8 dígitos rellenando con ceros a la izquierda si es necesario.
                        $state = str_pad($state, 8, '0', STR_PAD_LEFT);
                        // Se separan los primeros 4 dígitos y los últimos 4, añadiendo el guion.
                        return substr($state, 0, 4) . '-' . substr($state, 4, 4);
                    })
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_ambulancia')
                    ->default('---')
                    ->sortable()
                    ->icon('healthicons-o-ambulance')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('operador_numero')
                    ->default('---')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_traslado')
                    ->default('---')
                    ->sortable()
                    ->icon('healthicons-o-ambulance')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('sexo_paciente')
                    ->default('---')
                    ->sortable()
                    ->icon('heroicon-o-user')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_critico')
                    ->default('---')
                    ->sortable()
                    ->icon('healthicons-o-ambulance')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Fecha de Actualización')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('asunto_traslado')
                    ->default('---')
                    ->sortable()
                    ->icon('heroicon-o-clipboard-document-list')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->label('Asunto del Traslado'),

                // 2. antecendetes_clinicos (asegúrate que en fillable esté escrito igual)

                Tables\Columns\TextColumn::make('doctor_numero')
                    ->default('---')
                    ->icon('heroicon-o-user')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->label('Doctor Cierre'),

                Tables\Columns\TextColumn::make(
                    'gestor_numero',
                )
                    ->default('---')
                    ->icon('heroicon-o-user')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->label('Gestor Asignado'),

                Tables\Columns\TextColumn::make('usuario_cierre')
                    ->icon('heroicon-o-user')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->prefix('Dr. ')
                    ->label('Usuario que Cierra'),

            ])
            ->filters([
                SelectFilter::make('tipo_paciente')
                    ->options([
                        'Estable' => 'Estable',
                        'Critico' => 'Critico',
                    ])
                    ->label('Estado Paciente'),

            ])->defaultSort('created_at', 'desc')
            ->emptyStateHeading('Parece que no hay registros sin asignar')
            ->paginated([10, 25, 50])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth(MaxWidth::SevenExtraLarge)->iconButton()->icon('heroicon-o-eye')->color('warning')
                    ->modalIcon('healthicons-o-mobile-clinic')
                    ->modalAlignment(Alignment::Center)
                    ->modalHeading('Traslados Secundarios - Vista'),
                Tables\Actions\EditAction::make('Asignar Recurso')->modalWidth(MaxWidth::SevenExtraLarge)->iconButton()->color('primary')->icon('healthicons-o-mobile-clinic')
                    ->modalIcon('healthicons-o-mobile-clinic')
                    ->modalAlignment(Alignment::Center)
                    ->modalHeading('Traslados Secundarios - Asignación de Recurso'),
                Tables\Actions\Action::make('Auditoria')
                    ->iconButton()
                    ->icon('heroicon-o-clock')
                    ->modalHeading('Historial de Auditoría')
                    ->modalWidth('4xl')
                    ->color('danger')
                    ->action(fn() => null) // no guarda nada
                    ->modalContent(fn($record) => view('auditoria-modal', [
                        'record' => $record,
                        'audits' => Audit::where('auditable_id', $record->id)
                            ->where('auditable_id', $record->id)
                            ->whereIn('auditable_type', [
                                'App\Models\TrasladoSecundarioPropios',
                                'App\Models\TrasladoSecundario',
                                'App\Models\TrasladoSecundarioGestores',
                            ])
                            ->latest()
                            ->get(),
                    ]))
            ], position: ActionsPosition::BeforeCells)
            ->defaultGroup('tipo_paciente')
            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('estado', '!=', 'Finalizado')
            ->whereNull('ambulancia_id')
        ;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrasladoSecundarioGestores::route('/'),
            //'create' => Pages\CreateTrasladoSecundarioGestores::route('/create'),
            //'edit' => Pages\EditTrasladoSecundarioGestores::route('/{record}/edit'),
        ];
    }
}
