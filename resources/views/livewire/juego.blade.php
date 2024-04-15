<div
    class="p-6 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">

    <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
        {{ __('Jugador') }}: {{ $jugador }}
    </h1>

    <!-- An alpinejs component to show the countdown -->
    <div x-init="setInterval(() => { $wire.updateState(); }, 1000)">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Estado de juego') }} : {{ $estadoDeJuego[0] }} : {{ $remainingTime }}
        </h1>
    </div>

    <!-- Show a message that says 'Waiting for the other player' -->
    <div x-show="$wire.indiceEstadoActual === 0" class="mt-6 align-middle">
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Esperando al otro jugador') }}
        </h1>
    </div>

    <div x-show="$wire.indiceEstadoActual === 1" class="mt-6 align-middle">
        <button wire:click="rock"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 border border-transparent rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-geen-500">Rock</button>
        <button wire:click="paper"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-700 border border-transparent rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Paper</button>
        <button wire:click="scissors"
            class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 border border-transparent rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Scissors</button>
    </div>

</div>
