@php
    use Illuminate\Support\Str;

    $fieldLabels = [
        'ambulancia_id' => 'Ambulancia',
        'estado' => 'Estado',
        'justificacion_cierre' => 'Justificación',
        'usuario_cierre' => 'Usuario que cerró',
    ];

    $excludedFields = [
        'tipo_traslado_id',
        'destino_institucion',
        'destino_traslado',
        'origen_traslado',
        'origen_institucion',
        'tipo_traslado_id',
        'ambulancia_id',
        'user_id',
        'color',
        'notas_seguimiento',
        'formula_obstetrica',
        'id',
        'signos_vitales',
    ];

    $formatValue = function ($oldValue, $newValue) {
        $fieldTranslation = [
            'presion_arterial' => 'Presión arterial',
            'FC' => 'Frecuencia cardíaca',
            'fr' => 'Frecuencia respiratoria',
            'Temp' => 'Temperatura',
            'satoxi' => 'Saturación O₂',
            'HGT' => 'Glicemia',
            'glasgow' => 'Escala Glasgow',
            'Hora' => 'Hora de toma',
            'componente_edad' => 'Complemento',
        ];

        $normalize = function ($value) {
            if (is_array($value)) {
                return $value;
            }
            if (is_string($value) && str_starts_with($value, '[{')) {
                $decoded = json_decode($value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : null;
            }
            return $value;
        };

        $oldDecoded = $normalize($oldValue);
        $newDecoded = $normalize($newValue);

        if (
            (is_array($oldDecoded) && isset($oldDecoded[0]) && is_array($oldDecoded[0])) ||
            (is_array($newDecoded) && isset($newDecoded[0]) && is_array($newDecoded[0]))
        ) {
            $oldVitals = $oldDecoded[0] ?? [];
            $newVitals = $newDecoded[0] ?? [];

            $html = '<table class="text-sm text-gray-700 table-auto border border-gray-300 mt-2 w-full">';
            $html .=
                '<thead><tr><th class="border px-2 py-1">Campo</th><th class="border px-2 py-1">Antes</th><th class="border px-2 py-1">Después</th></tr></thead>';
            $html .= '<tbody>';

            $allKeys = array_unique(array_merge(array_keys($oldVitals), array_keys($newVitals)));

            foreach ($allKeys as $key) {
                $label = $fieldTranslation[$key] ?? ucfirst(str_replace('_', ' ', $key));
                $old = is_array($oldVitals[$key] ?? null) ? '—' : $oldVitals[$key] ?? '—';
                $new = is_array($newVitals[$key] ?? null) ? '—' : $newVitals[$key] ?? '—';

                $html .=
                    "<tr>
                    <td class='border px-2 py-1'>{$label}</td>
                    <td class='border px-2 py-1'>" .
                    e($old) .
                    "</td>
                    <td class='border px-2 py-1'>" .
                    e($new) .
                    "</td>
                </tr>";
            }

            $html .= '</tbody></table>';
            return $html;
        }

        return '<div class="text-sm text-gray-700">"' . e($oldValue) . '" → "' . e($newValue) . '"</div>';
    };
@endphp

<div class="space-y-4">
    @foreach ($audits as $audit)
        <div class="p-4 bg-gray-50 rounded shadow">
            <div class="text-sm text-gray-600">
                <strong>📅 Fecha:</strong> {{ $audit->created_at->format('d/m/Y H:i') }} <br>
                <strong>👤 Usuario:</strong> {{ $audit->user?->name ?? 'Sistema' }} <br>
                <strong>📝 Evento:</strong> {{ ucfirst($audit->event) }}
            </div>

            <ul class="mt-2 text-sm text-gray-800 list-disc list-inside">
                @foreach ($audit->getModified() as $field => $change)
                    @continue(Str::endsWith($field, '_id') || in_array($field, $excludedFields))

                    <li>
                        <strong>{{ $fieldLabels[$field] ?? ucfirst(str_replace('_', ' ', $field)) }}</strong>:
                        {!! $formatValue($change['old'] ?? '-', $change['new'] ?? '-') !!}
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach
</div>
