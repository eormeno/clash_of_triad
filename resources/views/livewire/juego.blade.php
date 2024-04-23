<div
    class="p-6 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

    <button wire:click="clear"
        class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-700 border border-transparent rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
        Reset
    </button>

    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        {{ __('Jugador') }}: {{ $jugador }}
    </h1>

    <div x-init="setInterval(() => { $wire.updateState(); }, 1000)">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Estado de juego') }} : {{ $estadoActual }} : {{ $remainingTime }}
        </h1>
    </div>

    <div x-show="$wire.estadoActual === 'buscando oponente'" class="mt-6 align-middle">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Esperando al otro jugador') }}
        </h1>
    </div>

    <div x-show="$wire.estadoActual === 'pedir jugada'" class="mt-6 align-middle">
        <button wire:click="play(0)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-700 border border-transparent rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Papel</button>
        <button wire:click="play(1)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 border border-transparent rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-geen-500">Piedra</button>
        <button wire:click="play(2)"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 border border-transparent rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Tijera</button>
    </div>

    <div x-show="$wire.estadoActual === 'mostrar resultado ronda'" class="mt-6 align middle">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Resultado') }} : {{ $resultadoRonda }}
        </h1>
    </div>

    <div x-show="$wire.estadoActual === 'mostrar número ronda'" class="mt-6 align middle">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Ronda') }} : {{ $ronda }}
        </h1>
    </div>

</div>
