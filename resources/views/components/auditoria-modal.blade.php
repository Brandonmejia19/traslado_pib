<x-filament::table>
    <x-slot name="head">
        <x-filament::table.heading>Fecha</x-filament::table.heading>
        <x-filament::table.heading>Usuario</x-filament::table.heading>
        <x-filament::table.heading>Evento</x-filament::table.heading>
        <x-filament::table.heading>Datos Cambiados</x-filament::table.heading>
    </x-slot>

    <x-slot name="body">
        @foreach($audits as $audit)
            <x-filament::table.row>
                <x-filament::table.cell>{{ $audit->created_at->format('d/m/Y H:i') }}</x-filament::table.cell>
                <x-filament::table.cell>{{ $audit->user->name ?? 'Sistema' }}</x-filament::table.cell>
                <x-filament::table.cell>{{ ucfirst($audit->event) }}</x-filament::table.cell>
                <x-filament::table.cell>
                    <ul class="text-xs">
                        @foreach($audit->getModified() as $field => $change)
                            <li>
                                <strong>{{ $field }}</strong>: "{{ $change['old'] }}" → "{{ $change['new'] }}"
                            </li>
                        @endforeach
                    </ul>
                </x-filament::table.cell>
            </x-filament::table.row>
        @endforeach
    </x-slot>
</x-filament::table>
