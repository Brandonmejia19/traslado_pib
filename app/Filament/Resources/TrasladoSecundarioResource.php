<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TrasladoSecundarioResource\Pages;
use App\Filament\Resources\TrasladoSecundarioResource\RelationManagers;
use App\Models\HospitalListado;
use App\Models\Ambulancias;
use App\Models\UnidadListado;
use App\Models\ISSListado;
use App\Models\PrivadoListado;
use App\Models\TrasladoSecundario;
use App\Models\TipoTraslado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Carbon;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Filament\Forms\Components\Actions;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Support\Enums\Alignment;
use Filament\Resources\TrasladoSecundarioResource\Widgets\TrasladosSecundarios;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use Rmsramos\Activitylog\RelationManagers\ActivitylogRelationManager;

class TrasladoSecundarioResource extends Resource
{
    protected static ?string $model = TrasladoSecundario::class;

    protected static ?string $navigationIcon = 'healthicons-o-cardiogram';
    protected static ?string $navigationGroup = 'Casos';
    protected static ?string $label = 'Traslados Secundarios';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de llamada')
                    ->schema(components: [
                        Forms\Components\Fieldset::make('Información de Solicitud de Traslado')
                            ->columns(4)
                            ->schema([
                                Forms\Components\TextInput::make('operador_nombre')
                                    ->prefixicon('healthicons-o-call-centre')
                                    ->default(Auth::user()->name)
                                    ->placeholder('Nombre del Operador')
                                    ->readOnly()
                                    ->columnspan(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('operador_numero')
                                    ->placeholder('Número del Operador')
                                    ->columnspan(1)
                                    ->numeric()
                                    ->prefixicon('healthicons-o-call-centre')
                                    ->maxLength(length: 255),
                                Forms\Components\TextInput::make('numero_llamada')
                                    ->placeholder('Telefono Origen')
                                    ->columnspan(1)
                                    ->numeric()
                                    ->prefixicon('healthicons-o-call-centre')
                                    ->maxLength(255),
                                Forms\Components\Actions::make([
                                    Action::make('cerrarCaso')
                                        ->icon('heroicon-m-x-mark')
                                        ->color('danger')
                                        ->label('Cerrar Caso')
                                        ->requiresConfirmation() // Para que se muestre un modal de confirmación
                                        ->modalHeading('Cerrar Caso')
                                        ->modalSubheading('Por favor, ingrese la justificación y la razón para cerrar este caso.')
                                        ->form([
                                            Forms\Components\Textarea::make('justificacion_cierre')
                                                ->label('Justificación de Cierre')
                                                ->placeholder('Por favor, ingrese una justificación para cerrar este caso')
                                                ->required(),
                                            Forms\Components\Select::make('razon_cierre')
                                                ->options([
                                                    'Resuelto' => 'Resuelto',
                                                    'No Resuelto' => 'No Resuelto',
                                                    'Cancelado' => 'Cancelado',
                                                    'Otro' => 'Otro',
                                                ])
                                                ->label('Razón de Cierre')
                                                ->required(),
                                            Forms\Components\TextInput::make('usuario_cierre')
                                                ->label('Usuario')
                                                ->default(Auth::user()->name)
                                                ->disabled(), // Este campo se muestra solo para información, no editable
                                        ])
                                        ->action(function (array $data, $record) {
                                            if (!$record) {
                                                Notification::make()
                                                    ->title('Error')
                                                    ->body('No se encontró el caso.')
                                                    ->danger()
                                                    ->send();
                                                return;
                                            }

                                            // Actualizar el registro con los datos de cierre
                                            $record->update([
                                                'estado' => 'Cerrado',
                                                'justificacion_cierre' => $data['justificacion_cierre'],
                                                'razon_cierre' => $data['razon_cierre'],
                                                'usuario_cierre' => Auth::user()->name, // Usuario autenticado
                                            ]);
                                            Notification::make()->title('Caso cerrado correctamente')->success()->send();
                                        }),
                                ]),
                            ]),
                    ]),

                Forms\Components\Section::make('Traslado Secundario')
                    ->schema([
                        Forms\Components\Fieldset::make('Información de Solicitud de Traslado')
                            ->columns(6)
                            ->schema(components: [
                                Forms\Components\ToggleButtons::make('asunto_traslado')
                                    ->label('Tipo de Traslado ')
                                    ->reactive()
                                    ->required()
                                    ->options([
                                        'Traslado de Paciente' => 'Traslado de Paciente',
                                        'Trasporte de Paciente' => 'Trasporte de Paciente'
                                    ])
                                    ->icons([
                                        'Traslado de Paciente' => 'healthicons-o-hospitalized',
                                        'Trasporte de Paciente' => 'healthicons-o-ambulance',
                                    ])
                                    ->inline()->columnSpanFull(),
                                Forms\Components\ToggleButtons::make('tipo_traslado')
                                    ->label('Tipo de Traslado ')->required()
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
                                    ->label('Tipo de Traslado InterHospitalario')->required()
                                    ->hidden(fn(callable $get) => $get('asunto_traslado') != 'Trasporte de Paciente')
                                    ->options(TipoTraslado::whereIn('id', [1, 4, 7])->pluck('nombre', 'id'))
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
                                    ->placeholder('Nombre del Médico Solicitante')
                                    ->label('Nombre del Médico Solicitante')
                                    ->prefixicon('healthicons-o-doctor')
                                    ->columnSpan(2)
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono_medico_solicitante')
                                    ->prefixicon('healthicons-o-phone')
                                    ->label('Teléfono del Médico Solicitante')
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
                                    ->label('Color')
                                    ->live()
                                    ->columnSpan(1)->extraAttributes(['style' => 'pointer-events: none; width: 0px; height: 0px; border-radius: 0px;']),
                            ]),


                        Forms\Components\Fieldset::make('Información General del Traslado')
                            ->columns(4)
                            ->schema(components: [
                                Forms\Components\ToggleButtons::make('origen_institucion')
                                    ->columnspan(2)
                                    ->reactive()
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
                                Forms\Components\TextInput::make('origen_institucion')
                                    ->label('Otro Destino / Domicilio')->columnspan(2)

                                    ->placeholder('Nombre de la Institución / Dirección')
                                    ->hidden(fn(callable $get) => !in_array($get('origen_traslado'), ['Domicilio', 'Otro']))
                                    ->prefixicon('healthicons-o-hospital')
                                    ->reactive(),
                                Forms\Components\ToggleButtons::make('destino_institucion')
                                    ->reactive()->columnspan(2)

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

                                Forms\Components\TextInput::make('nombre_medico_recibe')
                                    ->placeholder('Nombre del Médico que Recibe')
                                    ->label('Nombre del Médico que Recibe')->columnspan(2)

                                    ->prefixicon('healthicons-o-doctor')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('jvpe_medico_recibe')
                                    ->tel()->columnspan(1)
                                    ->label('JVPE del Médico que Recibe')
                                    ->placeholder('JVPE')
                                    ->prefixicon('healthicons-o-stethoscope')
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('telefono_medico_recibe')
                                    ->tel()->columnspan(1)
                                    ->label('Teléfono del Médico que Recibe')
                                    ->placeholder('0000-0000')
                                    ->prefixicon('healthicons-o-phone')
                                    ->maxLength(255),

                            ]),
                        Forms\Components\Fieldset::make('Datos de Recursos')
                            ->columns(4)
                            ->schema([
                                Forms\Components\DatePicker::make('fecha')
                                    ->label('Fecha del Traslado')
                                    ->default(fn() => Carbon::now())
                                    ->readonly()->columnspan(1)
                                    ->prefixicon('heroicon-o-calendar')
                                    ->required(),
                                Forms\Components\ToggleButtons::make('programado')
                                    ->label('¿Programado?')
                                    ->default('NO')
                                    ->reactive()
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
                                Forms\Components\Select::make('ambulancia')
                                    ->placeholder('Ambulancias')
                                    ->options(Ambulancias::query()->pluck('unidad', 'unidad'))
                                    ->searchable()
                                    ->columnspan(1)
                                    ->prefixicon('healthicons-o-ambulance'),
                                Forms\Components\Select::make('tipo_ambulancia')
                                    ->prefixicon('healthicons-o-ambulance')
                                    ->options([
                                        'A' => 'A',
                                        'B' => 'B',
                                        'C' => 'C',
                                        'M' => 'M',
                                    ])->columnspan(1),

                            ]),
                        Forms\Components\Fieldset::make('Datos de Paciente')
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
                                    ->reactive()
                                    ->columnspan(1)
                                    ->placeholder('00')
                                    ->prefixicon('healthicons-o-insurance-card')
                                    ->numeric(),
                                Forms\Components\Select::make('componente_edad')//PONER EN MODEL
                                    ->label('Componente')->columnspan(1)
                                    ->default('Años')
                                    ->options([
                                        'Horas' => 'Horas',
                                        'Días' => 'Días',
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
                                Forms\Components\Textarea::make('diagnostico_paciente')
                                    ->placeholder('Diagnóstico del Paciente')
                                    ->maxLength(255)
                                    ->columnspan(4),
                            ]),
                        Forms\Components\Fieldset::make('Estado de Paciente')
                            ->columns(4)
                            ->schema([
                                Forms\Components\ToggleButtons::make('tipo_paciente')
                                    ->reactive()
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
                                    ->prefixicon('heroicon-o-calendar')
                                    ->required(),
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
                                    Forms\Components\TextInput::make('FCF')
                                        ->prefixicon('healthicons-o-lungs')
                                        ->placeholder('0 ppm')
                                        ->label('FCF')
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
                        Forms\Components\Fieldset::make('Signos Vitales de Paciente')
                            ->schema([
                                Forms\Components\Repeater::make('signos_vitales')
                                    ->addActionLabel('Nuevo Signo Vital')
                                    ->collapseAllAction(
                                        fn(Action $action) => $action->label('Esconder todo'),
                                    )
                                    ->deletable(false)
                                    ->reorderable(false)
                                    ->schema([
                                        Forms\Components\TextInput::make('presion_arterial')->prefixicon('healthicons-o-blood-pressure')->placeholder('120/80'),
                                        Forms\Components\TextInput::make('FC')->label('FC')->prefixicon('healthicons-o-heart')->placeholder('95 ppm'),
                                        Forms\Components\TextInput::make('FR')->label('FR')->prefixicon('healthicons-o-lungs')->placeholder('15'),
                                        Forms\Components\TextInput::make(name: 'Temp.')->label('Temperatura')->prefixicon('healthicons-o-thermometer')->placeholder('36.5 °C'),
                                        Forms\Components\TextInput::make(name: 'satoxi')->label('Sat. Oxígeno')->prefixicon('healthicons-o-oxygen-tank')->placeholder('95%'),
                                        Forms\Components\TextInput::make(name: 'HGT')->label('HGT')->prefixicon('healthicons-o-blood-bag')->placeholder('80 mg/dl'),
                                        Forms\Components\TextInput::make(name: 'glasgow')->label('Escala Glasgow')->prefixicon('healthicons-o-neurology')->placeholder('15 pts'),
                                        Forms\Components\DateTimePicker::make(name: 'Hora')->label('Hora de Toma')->prefixicon('heroicon-o-clock'),

                                    ])
                                    ->collapsible()
                                    ->columnSpanFull()
                                    ->columns(6),
                            ]),
                        Forms\Components\Fieldset::make('Requerimientos de Paciente')
                            ->columns(5)
                            ->schema([
                                Forms\Components\TextInput::make(name: 'fio2')
                                    ->numeric()
                                    ->label('Fio2')
                                    ->columnSpan(1)
                                    ->prefixicon('healthicons-o-oxygen-tank')
                                    ->placeholder('Fio2'),
                                Forms\Components\ToggleButtons::make('requerimientos_oxigenoterapia')
                                    ->options([
                                        'NO' => 'NO',
                                        'Canula Nasal' => 'Canula Nasal',
                                        'Venturi' => 'Venturi',
                                        'Mascarilla Simple' => 'Mascarilla Simple',
                                        'Mascarilla Reservorio' => 'Mascarilla Reservorio',
                                    ])
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
                        Forms\Components\Fieldset::make('Requerimientos de Parametros de Ventilación')
                            ->columns(5)
                            ->schema([
                                Forms\Components\ToggleButtons::make('asistencia_ventilatoria')
                                    ->reactive()
                                    ->label('Asistencia Ventilatoria (VM)')
                                    ->options([
                                        'NO' => 'NO',
                                        'SI' => 'SI',
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
                                Forms\Components\TextInput::make(name: 'FR')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->label('FR')
                                    ->placeholder('FR'),
                                Forms\Components\TextInput::make(name: 'PEEP')
                                    ->label('PEEP')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag')
                                    ->placeholder('PEEP'),
                                Forms\Components\Select::make(name: 'TRIGGER')
                                    ->options(['SI' => 'SI', 'NO' => 'NO'])
                                    ->label('TRIGGER')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->prefixicon('healthicons-o-blood-bag'),
                                Forms\Components\TextInput::make('bombas_infusion')
                                    ->placeholder('#')
                                    ->hidden(condition: fn(callable $get) => $get('asistencia_ventilatoria') != 'SI')
                                    ->numeric()
                                    ->prefixicon('healthicons-o-blood-bag'),
                            ]),
                        Forms\Components\Fieldset::make('Lugar del Hospital hacia a donde será traladado')
                            ->schema([
                                Forms\Components\Select::make('servicio')
                                    ->prefixicon('healthicons-o-health-worker-form')
                                    ->searchable()
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
                                Forms\Components\TextInput::make('numero_cama')
                                    ->numeric()
                                    ->prefixicon('healthicons-o-hospitalized')
                                    ->placeholder('Número de Cama')
                                    ->label('Número de Cama')
                                    ->maxLength(3),
                            ]),
                        Forms\Components\Fieldset::make('Notas de Seguimiento')
                            ->schema([
                                Forms\Components\TextArea::make('notas_seguimiento')
                                    ->label('')
                                    ->placeholder('Notas de Seguimiento')
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
                    ->default('N/A')
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
                    ->default('N/A')
                    ->sortable()
                    ->badge()->alignment(Alignment::Center)

                    ->label('Correlativo')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('diagnostico_paciente')
                    ->icon('healthicons-o-clinical-f')
                    ->default('N/A')
                    ->sortable()
                    ->alignment(Alignment::Center)
                    ->label('Diagnóstico')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\ColorColumn::make('color')
                    ->label('Prioridad')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('origen_traslado')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Origen')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('destino_traslado')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Destino')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombres_paciente')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Nombres')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('apellidos_paciente')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Apellidos')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('edad_paciente')
                    ->numeric()
                    ->label('Edad')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ambulancia')
                    ->default('N/A')
                    ->icon('healthicons-o-ambulance')
                    ->color('primary')
                    ->sortable()->alignment(Alignment::Center)
                    ->label('Ambulancia')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('programado')
                    ->default('N/A')
                    ->sortable()->alignment(Alignment::Center)

                    ->label('¿Programado?')
                    ->toggleable(isToggledHiddenByDefault: false)
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()->alignment(Alignment::Center)

                    ->label('Fecha de Creación')
                    ->toggleable(isToggledHiddenByDefault: false),
                /////////////////////////////////////////////////////////////////////////////////////////////////
                Tables\Columns\TextColumn::make('numero_llamada')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->date()
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->default('N/A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('hora')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('tipo_ambulancia')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('operador_nombre')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('operador_numero')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_traslado')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('nombre_medico_solicitante')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('telefono_medico_solicitante')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('origen_institucion')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),



                Tables\Columns\TextColumn::make('nombre_medico_recibe')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('telefono_medico_recibe')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),


                Tables\Columns\TextColumn::make('sexo_paciente')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('registro_expediente')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo_critico')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('edad_gestacional')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_probable_parto')
                    ->date()
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('dilatacion')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('borramiento')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('hora_obstetrica')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('FCF')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('membranas_integras')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('mov_fetales')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('trabajo_parto')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('contracciones')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('frecuencia')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('bombas_infusion')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('servicio')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),
                Tables\Columns\TextColumn::make('numero_cama')
                    ->default('N/A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->paginated([10, 25, 50, 100])
            ->actions([
                ActionGroup::make([
                    // ActivityLogTimelineTableAction::make('Historico'),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\ViewAction::make(),
                ]),
            ])

            ->bulkActions([

            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('estado', '!=', 'En curso');
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTrasladoSecundarios::route('/'),
            'create' => Pages\CreateTrasladoSecundario::route('/create'),
            'edit' => Pages\EditTrasladoSecundario::route('/{record}/edit'),
        ];
    }
}
