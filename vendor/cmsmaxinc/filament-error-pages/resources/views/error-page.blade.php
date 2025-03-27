<div class="flex items-center justify-center min-h-screen">
    <div class="text-center p-10 bg-transparent">
        <!-- Código de error grande -->
        <h1 class="text-2xl font-extrabold text-gray-300 leading-none tracking-wide relative">
            <span class="absolute text-gray-400 transform -rotate-12 -top-4 -left-6 opacity-50">
                {{ $this->getCode() }}
            </span>
            <span class="relative z-10 text-gray-800">{{ $this->getCode() }}</span>
        </h1>
<br>
        <!-- Mensaje principal -->
        <p class="text-2xl font-bold text-gray-700 mb-2">
            {{ $this->getTitle() }}
        </p>

        <!-- Descripción -->
        <p class="text-sm text-gray-500 mb-6">
            {{ $this->getDescription() }}
        </p>
    <br>
        <!-- Botones de navegación -->
        <div class="flex justify-center gap-4">
            @if(url()->previous() != url()->current())
                <x-filament::button icon="heroicon-o-arrow-uturn-left" tag="a" color="gray"
                    class="px-6 py-2 rounded-lg text-gray-700 bg-gray-200 hover:bg-gray-300 transition-all"
                    :href="url()->previous()">
                    {{ __('Volver atrás') }}
                </x-filament::button>
            @endif

            <x-filament::button icon="heroicon-s-home" tag="a" color="primary"
                class="px-6 py-2 rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-all"
                :href="\Filament\Facades\Filament::getCurrentPanel()->getUrl()">
                {{ __('Ir a la página de inicio') }}
            </x-filament::button>
        </div>
    </div>
</div>
