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
use App\Models\UnidadListado;
use App\Models\ISSListado;
use App\Models\PrivadoListado;
use App\Models\TipoTraslado;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Request;

class TrasladoSecundarioGestoresResource extends Resource
{
    protected static ?string $model = TrasladoSecundarioGestores::class;
    protected static ?string $navigationIcon = 'healthicons-o-ambulance';
    protected static ?string $navigationGroup = 'Casos';
    protected static ?string $label = 'Traslados sin recuso asignado';
    protected static ?string $navigationLabel = 'Gestión de Recursos';

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
                                Forms\Components\ToggleButtons::make('tipo_traslado')
                                    ->label('Tipo de Traslado')->required()
                                    ->required()
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Traslado de Paciente')
                                    ->options(TipoTraslado::all()->pluck('nombre', 'id'))
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
                                    ->columnSpan(2)
                                    ->maxLength(255),

                                Forms\Components\TextInput::make('telefono_medico_solicitante')
                                    ->prefixicon('healthicons-o-phone')
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
                                    ->placeholder('Opción')
                                    ->columnSpan(1)
                                    ->reactive()
                                    ->beforeStateDehydrated(function ($state, callable $set) {
                                        // Cambia el color del ColorPicker basado en la prioridad seleccionada
                                        $color = match ($state) {
                                            '1' => '#de2121', // Rojo para prioridad 1
                                            '2' => '#dea02c', // Naranja para prioridad 2
                                            '3' => '#24de1b', // Amarillo para prioridad 3
                                            '4' => '#29dcf0', // Verde para prioridad 4
                                            default => '#FFF1', // Blanco como valor por defecto
                                        };
                                        $set('color', $color);
                                    })
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        // Cambia el color del ColorPicker basado en la prioridad seleccionada
                                        $color = match ($state) {
                                            '1' => '#de2121', // Rojo para prioridad 1
                                            '2' => '#dea02c', // Naranja para prioridad 2
                                            '3' => '#24de1b', // Amarillo para prioridad 3
                                            '4' => '#29dcf0', // Verde para prioridad 4
                                            default => '#FFF1', // Blanco como valor por defecto
                                        };
                                        $set('color', $color);
                                    }),
                                Forms\Components\ColorPicker::make('color')
                                    ->label('.')
                                    ->live()
                                    ->columnSpan(1)->extraAttributes(['style' => 'pointer-events: none; width: 0px; height: 0px; border-radius: 0px;']),
                                Forms\Components\TextInput::make('jvpe_medico_entrega')
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
                                    ->columnspan(2)
                                    ->default('Hospital')
                                    ->reactive()
                                    ->label('Tipo Origen')
                                    ->options([
                                        'Hospital' => 'Hospital',
                                        'Unidad de Salud' => 'Unidad de Salud',
                                        'ISSS' => 'ISSS',
                                        'Privado' => 'Privado',
                                        'Domicilio' => 'Domicilio',
                                        'Otro' => 'Otro',
                                    ])
                                    ->icons([
                                        'Hospital' => 'healthicons-o-hospital',
                                        'Unidad de Salud' => 'healthicons-o-ambulatory-clinic',
                                        'ISSS' => 'healthicons-o-rural-post',
                                        'Privado' => 'healthicons-o-emergency-post',
                                        'Domicilio' => 'healthicons-o-home',
                                        'Otro' => 'healthicons-o-health-alt',
                                    ])
                                    ->inline(),
                                Forms\Components\Select::make('origen_traslado')
                                    ->prefixicon('healthicons-o-hospital')
                                    ->label('Origen')
                                    ->searchable()->columnspan(2)
                                    ->hidden(fn(callable $get) => in_array($get('origen_institucion'), ['Domicilio', 'Otro']))
                                    ->options(function (callable $get) {
                                        $destino = $get('origen_institucion');
                                        switch ($destino) {
                                            case 'Hospital':
                                                return HospitalListado::query()->pluck('nombre', 'nombre');
                                            case 'Unidad de Salud':
                                                return UnidadListado::all()->pluck('nombre', 'nombre');
                                            case 'ISSS':
                                                return ISSListado::all()->pluck('nombre', 'nombre');
                                            case 'Privado':
                                                return PrivadoListado::all()->pluck('nombre', 'nombre');
                                            case 'Domicilio':
                                            default:
                                                return [];
                                        }
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('origen_traslado')
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('origen_institucion'), ['Domicilio', 'Otro']))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\Select::make('servicio_origen')
                                    ->prefixicon('healthicons-o-health-worker-form')
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
                                Forms\Components\TextInput::make('numero_cama_origen')
                                    ->numeric()->columnspan(1)
                                    ->prefixicon('healthicons-o-hospitalized')
                                    ->placeholder('Número de Cama')
                                    ->label('Número de Cama'),
                                Forms\Components\TextInput::make('origen_institucion')
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('origen_traslado'), ['Domicilio', 'Otro']))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\ToggleButtons::make('destino_institucion')
                                    ->reactive()->columnspan(2)
                                    ->label('Tipo Destino')
                                    ->default('Hospital')
                                    ->options([
                                        'Hospital' => 'Hospital',
                                        'Unidad de Salud' => 'Unidad de Salud',
                                        'ISSS' => 'ISSS',
                                        'Privado' => 'Privado',
                                        'Domicilio' => 'Domicilio',
                                        'Otro' => 'Otro',
                                    ])
                                    ->icons([
                                        'Hospital' => 'healthicons-o-hospital',
                                        'Unidad de Salud' => 'healthicons-o-ambulatory-clinic',
                                        'ISSS' => 'healthicons-o-rural-post',
                                        'Privado' => 'healthicons-o-emergency-post',
                                        'Domicilio' => 'healthicons-o-home',
                                        'Otro' => 'healthicons-o-health-alt',
                                    ])
                                    ->inline(),
                                Forms\Components\Select::make('destino_traslado')
                                    ->prefixicon('healthicons-o-hospital')
                                    ->label('Destino')
                                    ->searchable()->columnspan(2)
                                    ->hidden(fn(callable $get) => in_array($get('destino_institucion'), ['Domicilio', 'Otro']))
                                    ->options(function (callable $get) {
                                        $destino = $get('destino_institucion');
                                        switch ($destino) {
                                            case 'Hospital':
                                                return HospitalListado::all()->pluck('nombre', 'nombre');
                                            case 'Unidad de Salud':
                                                return UnidadListado::all()->pluck('nombre', 'nombre');
                                            case 'ISSS':
                                                return ISSListado::all()->pluck('nombre', 'nombre');
                                            case 'Privado':
                                                return PrivadoListado::all()->pluck('nombre', 'nombre');
                                            case 'Domicilio':
                                            default:
                                                return [];
                                        }
                                    })
                                    ->reactive(),
                                Forms\Components\TextInput::make('destino_traslado')
                                    ->label('Otro Destino / Domicilio')->columnspan(2)
                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('destino_institucion'), ['Domicilio', 'Otro']))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\Select::make('servicio_destino')
                                    ->prefixicon('healthicons-o-health-worker-form')
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
                                    ->numeric()->columnspan(1)
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Traslado de Paciente')
                                    ->prefixicon('healthicons-o-hospitalized')
                                    ->placeholder('Número de Cama')
                                    ->label('Número de Cama')
                                    ->maxLength(3),

                                Forms\Components\TextInput::make('nombre_medico_recibe')
                                    ->placeholder('Nombre del Médico que Recibe')
                                    ->label('Nombre médico o persona receptora')->columnspan(2)

                                    ->prefixicon('healthicons-o-doctor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('jvpe_medico_recibe')
                                    ->tel()->columnspan(1)
                                    ->label('JVPE médico receptor')
                                    ->placeholder('JVPE')
                                    ->prefixicon('healthicons-o-stethoscope')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono_medico_recibe')
                                    ->tel()->columnspan(1)
                                    ->mask('99999999')
                                    ->label('Teléfono médico receptor')
                                    ->placeholder('0000-0000')
                                    ->prefixicon('healthicons-o-phone')
                                    ->maxLength(255),

                            ]),
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
                                    ->label(label: 'Fecha de Programación')
                                    ->disabled(fn(callable $get) => $get('programado') != 'SI')
                                    ->columnspan(1)
                                    ->hidden(fn(callable $get) => $get('programado') != 'SI')
                                    ->required()
                                    ->prefixicon('heroicon-o-calendar'),

                                Forms\Components\Select::make('tipo_unidad_sugerida')
                                    ->disabled(auth()->user()->cargo != 'Médico')
                                    ->prefixicon('healthicons-o-ambulance')
                                    ->label('Tipo Unidad Sugeridad')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'C' => 'C',
                                        'M' => 'M',
                                    ])->columnspan(1),

                                ///Meter imaginaria
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
                                        Forms\Components\Select::make('ambulancia')
                                            ->placeholder('Unidad')
                                            ->label('Unidad')
                                            ->options(Ambulancias::query()->pluck('unidad', 'unidad'))
                                            ->searchable()
                                            ->disabled(auth()->user()->cargo === 'Operador')
                                            ->columnspan(1)
                                            ->prefixicon('healthicons-o-ambulance'),
                                    ])->columnspan('full'),
                            ]),
                        Forms\Components\Fieldset::make('DATOS DE PACIENTE')
                            ->columns(8)
                            ->schema([
                                Forms\Components\TextInput::make('nombres_paciente')
                                    ->prefixicon('healthicons-o-person')
                                    ->columnspan(2)
                                    ->placeholder('Nombres del Paciente')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('apellidos_paciente')
                                    ->placeholder('Apellidos del Paciente')
                                    ->prefixicon('healthicons-o-person')
                                    ->columnspan(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('edad_paciente')
                                    ->label('Edad')
                                    ->mask('999')
                                    ->reactive()
                                    ->columnspan(1)
                                    ->placeholder('00')
                                    ->prefixicon('healthicons-o-insurance-card')
                                    ->numeric(),
                                Forms\Components\Select::make('componente_edad')//PONER EN MODEL
                                    ->label('Complemento de edad')->columnspan(1)
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
                                    ->inline()
                                    ->reactive()
                                    ->options([
                                        'M' => 'M',
                                        'F' => 'F',
                                    ]),
                                Forms\Components\TextArea::make('registro_expediente')
                                    ->placeholder('Registro de Expediente')
                                    ->columnspan(4)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('diagnostico_paciente')
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
                                        ->placeholder('0 cm')
                                        ->prefixicon('healthicons-o-blood-pressure')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('borramiento')
                                        ->placeholder('0%')
                                        ->prefixicon('healthicons-o-heart')
                                        ->maxLength(255),
                                    Forms\Components\TimePicker::make('hora_obstetrica')
                                        ->default(fn() => Carbon::now()->format('H:i'))
                                        ->prefixicon('heroicon-o-clock'),
                                    Forms\Components\TextInput::make('fcf')
                                        ->prefixicon('healthicons-o-lungs')
                                        ->placeholder('0 ppm')
                                        ->label('fcf')
                                        ->maxLength(255),
                                    Forms\Components\ToggleButtons::make('membranas_integras')
                                        ->label('¿Membranas Integras?')
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),
                                    Forms\Components\ToggleButtons::make('mov_fetales')
                                        ->label('¿Movimientos Fetales?')
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),
                                    Forms\Components\ToggleButtons::make('trabajo_parto')
                                        ->label('¿Trabajo de Parto?')
                                        ->reactive()
                                        ->options(['SI' => 'SI', 'NO' => 'NO'])
                                        ->inline(),

                                    Forms\Components\TextInput::make('contracciones')
                                        ->placeholder('0')
                                        ->prefixicon('healthicons-o-blood-pressure')
                                        ->hidden(fn(callable $get) => $get('trabajo_parto') != 'SI')
                                        ->maxLength(255),
                                    Forms\Components\TextInput::make('frecuencia')
                                        ->prefixicon('healthicons-o-heart')
                                        ->placeholder('0 ppm')
                                        ->hidden(fn(callable $get) => $get('trabajo_parto') != 'SI')
                                        ->maxLength(255),
                                    Forms\Components\Select::make('posicion')
                                        ->prefixicon('healthicons-o-baby-0203-alt')
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
                                    ->numeric()
                                    ->label('Fio2')
                                    ->columnSpan(1)
                                    ->prefixicon('healthicons-o-oxygen-tank')
                                    ->placeholder('Fio2'),
                                Forms\Components\ToggleButtons::make('requerimientos_oxigenoterapia')
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
                            ->columns(5)
                            ->schema([
                                Forms\Components\ToggleButtons::make('asistencia_ventilatoria')
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
                                    ->numeric()
                                    ->label('VT')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('VT'),
                                Forms\Components\TextInput::make(name: 'volmin')
                                    ->numeric()
                                    ->label('VOL MIN')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('VOL MIN'),
                                Forms\Components\Select::make(name: 'relacion_ie')
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
                                    ->label('fr')
                                    ->placeholder('fr'),
                                Forms\Components\TextInput::make(name: 'peep')
                                    ->label('peep')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('peep'),
                                Forms\Components\Select::make(name: 'trigger')
                                    ->options(['SI' => 'SI', 'NO' => 'NO'])
                                    ->label('trigger')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag'),
                                Forms\Components\TextInput::make('bombas_infusion')
                                    ->placeholder('#')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->numeric()
                                    ->prefixicon('healthicons-o-blood-bag'),
                            ]),

                        Forms\Components\Fieldset::make('NOTAS DE SEGUIMIENTO')
                            ->schema([
                                Forms\Components\TextArea::make('notas_seguimiento')
                                    ->label('Notas')
                                    ->maxLength(255)
                                    ->placeholder('Notas de Seguimiento')
                                    ->columnSpanFull(),
                                Forms\Components\Fieldset::make('Información de Usuario')
                                    ->columns(4)
                                    ->schema([

                                        Forms\Components\TextInput::make('operador_numero')
                                            ->placeholder('Número del Operador')
                                            ->columnspan(1)
                                            ->numeric()
                                            ->label('Puesto')
                                            ->default(function () {
                                                $ip = Request::ip();

                                                if (config('app.behind_cdn')) {
                                                    $ip = Request::server(config('app.behind_cdn_http_header_field', 'HTTP_X_FORWARDED_FOR')) ?? $ip;
                                                }


                                                $segments = explode('.', $ip);
                                                $lastDigits = array_slice($segments, -1);

                                                return implode('.', $lastDigits);
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
                Tables\Columns\TextColumn::make('origen_traslado')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Origen')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('destino_traslado')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Destino')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombres_paciente')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Nombres')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos_paciente')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Apellidos')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('edad_paciente')
                    ->numeric()
                    ->label('Edad')
                    ->default('---')
                    ->sortable()->alignment(Alignment::Center)

                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ambulancia')
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
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->default('---')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_ambulancia')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('operador_nombre')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('operador_numero')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_traslado')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('nombre_medico_solicitante')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono_medico_solicitante')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('origen_institucion')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),



                Tables\Columns\TextColumn::make('nombre_medico_recibe')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono_medico_recibe')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),


                Tables\Columns\TextColumn::make('sexo_paciente')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('registro_expediente')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_critico')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('edad_gestacional')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_probable_parto')
                    ->date()
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dilatacion')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('borramiento')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fcf')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('membranas_integras')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('mov_fetales')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('trabajo_parto')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('contracciones')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('frecuencia')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bombas_infusion')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('servicio')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_cama')
                    ->default('---')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('tipo_paciente')
                    ->options([
                        'Estable' => 'Estable',
                        'Critico' => 'Critico',
                    ])
                    ->label('Estado Paciente'),

            ])
            ->emptyStateHeading('Parece que no hay registros sin asignar')
            ->paginated([10, 25, 50])
            ->actions([
                Tables\Actions\ViewAction::make()->modalWidth(MaxWidth::SevenExtraLarge)->iconButton()->icon('heroicon-o-eye')->color('warning'),
                Tables\Actions\EditAction::make('Asignar Recurso')->modalWidth(MaxWidth::SevenExtraLarge)->iconButton()->color('primary')->icon('healthicons-o-mobile-clinic'),
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
            ->whereNull('ambulancia')
        ;
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrasladoSecundarioGestores::route('/'),
            //'create' => Pages\CreateTrasladoSecundarioGestores::route('/create'),
            //   'edit' => Pages\EditTrasladoSecundarioGestores::route('/{record}/edit'),
        ];
    }
}
