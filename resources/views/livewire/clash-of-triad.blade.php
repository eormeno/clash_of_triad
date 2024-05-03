<div
    class="p-6 bg-white
    dark:bg-gray-900
    dark:bg-gradient-to-bl
    dark:from-gray-800/50
    dark:via-transparent
    border-b border-gray-200 dark:border-gray-800">

    <div x-init="setInterval(() => { $wire.updateState(); }, 500)">
        <!--
        <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
            {{ __('Estado de juego') }} : {{ $current_state_name }} : {{ $current_state_remaining_time }}
        </h1>
        -->
    </div>

    @switch($ui_state)
        @case('buscando oponente')
            <div class="mt-3 align-middle">
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Buscando oponente') }}: {{ $current_state_remaining_time }}
                </h1>
            </div>
        @break

        @case ('oponente encontrado')
            <div class="mt-3 align-middle">
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Oponente encontrado') }}
                </h1>
            </div>
        @break

        @case('pedir jugada')
            <div class="mt-3 align-middle">
                <h1 class="mt-3 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Ronda') }} : {{ $ronda }}
                </h1>
                <!-- the player points and the opponent points -->
                <h1 class="mt-3 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Tu puntaje: ') }} {{ $puntaje_jugador }}
                </h1>
                <h1 class="mt-3 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Puntaje oponente: ') }} {{ $puntaje_oponente }}
                </h1>
                <h1 class="mt-3 mb-2 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Elige tu jugada') }}
                </h1>
                <button wire:click="juegoPropio(0)"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-700 border border-transparent rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Papel</button>
                <button wire:click="juegoPropio(1)"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-green-700 border border-transparent rounded-md shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-geen-500">Piedra</button>
                <button wire:click="juegoPropio(2)"
                    class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-700 border border-transparent rounded-md shadow-sm hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Tijeras</button>
            </div>
        @break

        @case('mostrar resultado ronda')
            <div class="mt-3 align middle">
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Resultado') }} : {{ $resultado_ronda }}
                </h1>
            </div>
        @break

        @case('mostrar número ronda')
            <div class="mt-3 align middle">
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Ronda') }} : {{ $ronda }}
                </h1>
            </div>
        @break

        @case('mostrar resultado juego')
            <div class="mt-3 align middle">
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Tu puntaje: ') }} {{ $puntaje_jugador }}
                </h1>
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ __('Puntaje oponente: ') }} {{ $puntaje_oponente }}
                </h1>
                <h1 class="mt-8 text-2xl font-medium text-gray-900 dark:text-white">
                    {{ $resultado_juego }}
                </h1>
            </div>
        @break

        @case ('fin')
            <button wire:click="clear"
                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-gray-700 border border-transparent rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500">
                Jugar de nuevo
            </button>
        @break

        @default
    @endswitch

    <div class="fixed inset-x-0 bottom-0">
        <div class="bg-gray-800">
            <div class="max-w-7xl mx-auto py-3 px-3 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between flex-wrap">
                    <div class="w-0 flex-1 flex items-center">

                        <span class="flex p-2 rounded-lg text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1"
                                stroke="currentColor" class="w-6 h-6">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 12.75c1.148 0 2.278.08 3.383.237 1.037.146 1.866.966 1.866 2.013 0 3.728-2.35 6.75-5.25 6.75S6.75 18.728 6.75 15c0-1.046.83-1.867 1.866-2.013A24.204 24.204 0 0 1 12 12.75Zm0 0c2.883 0 5.647.508 8.207 1.44a23.91 23.91 0 0 1-1.152 6.06M12 12.75c-2.883 0-5.647.508-8.208 1.44.125 2.104.52 4.136 1.153 6.06M12 12.75a2.25 2.25 0 0 0 2.248-2.354M12 12.75a2.25 2.25 0 0 1-2.248-2.354M12 8.25c.995 0 1.971-.08 2.922-.236.403-.066.74-.358.795-.762a3.778 3.778 0 0 0-.399-2.25M12 8.25c-.995 0-1.97-.08-2.922-.236-.402-.066-.74-.358-.795-.762a3.734 3.734 0 0 1 .4-2.253M12 8.25a2.25 2.25 0 0 0-2.248 2.146M12 8.25a2.25 2.25 0 0 1 2.248 2.146M8.683 5a6.032 6.032 0 0 1-1.155-1.002c.07-.63.27-1.222.574-1.747m.581 2.749A3.75 3.75 0 0 1 15.318 5m0 0c.427-.283.815-.62 1.155-.999a4.471 4.471 0 0 0-.575-1.752M4.921 6a24.048 24.048 0 0 0-.392 3.314c1.668.546 3.416.914 5.223 1.082M19.08 6c.205 1.08.337 2.187.392 3.314a23.882 23.882 0 0 1-5.223 1.082" />
                            </svg>

                        </span>

                        <p class="ml-3 font-mono text-white">
                            <span class="bg-green-900 text-white rounded-lg p-2">{{ $current_state_name }}</span>
                            <span >
                                {{ $current_state_remaining_time }}
                            </span>
                            <span>
                                {{ __('Ping: ') }} {{ $delta_time }}
                            </span>
                            <span>
                                {{ __('Jugador: ') }} {{ $jugador }}
                            </span>
                            <button wire:click="clear"
                                class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-red-700 border border-transparent rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                Reset
                            </button>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>
