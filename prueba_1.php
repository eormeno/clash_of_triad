<?php

const PAPEL = 0;
const PIEDRA = 1;
const TIJERA = 2;

const OPCIONES = ["Papel", "Piedra", "Tijera"];

const EMPATA = 0;
const PIERDE = 1;
const GANA = 2;

/*
mostrarResultado(PAPEL, PAPEL);		// Empata
mostrarResultado(PAPEL, PIEDRA);	// Gana
mostrarResultado(PAPEL, TIJERA);	// Pierde
mostrarResultado(PIEDRA, PAPEL);	// Pierde
mostrarResultado(PIEDRA, PIEDRA);	// Empata
mostrarResultado(PIEDRA, TIJERA);	// Gana
mostrarResultado(TIJERA, PAPEL);	// Gana
mostrarResultado(TIJERA, PIEDRA);	// Pierde
mostrarResultado(TIJERA, TIJERA);	// Empata
*/

for ($opcion = 0; $opcion < count(OPCIONES); $opcion++) {
    for ($opcion2 = 0; $opcion2 < count(OPCIONES); $opcion2++) {
        mostrarResultado($opcion, $opcion2);
    }
}

function calcularResultadoPorAngulo($opciónJugador, $opciónOponente)
{
    $angulo = 360 / count(OPCIONES);
    return ((deg2rad($opciónJugador * $angulo) - deg2rad($opciónOponente * $angulo) + 2 * M_PI) % (2 * M_PI)) / 2;
}

function calcularResultadoPorAngulo2($opciónJugador, $opciónOponente)
{
    if ($opciónJugador == $opciónOponente) {
        return EMPATA;
    }
    $angulo = 360 / count(OPCIONES);
    $diferencia = $opciónJugador * $angulo - $opciónOponente * $angulo;
    if ($diferencia < 0) {
        $diferencia += 360;
    }
    // determina si es par o impar

    return ($diferencia / $angulo) % 2 == 0 ? GANA : PIERDE;
}

function calcularResultadoPorLogica($opciónJugador, $opciónOponente)
{
    if ($opciónJugador == $opciónOponente) {
        return EMPATA;
    }
    if ($opciónJugador == PAPEL && $opciónOponente == PIEDRA) {
        return GANA;
    }
    if ($opciónJugador == PIEDRA && $opciónOponente == TIJERA) {
        return GANA;
    }
    if ($opciónJugador == TIJERA && $opciónOponente == PAPEL) {
        return GANA;
    }
    return PIERDE;
}

function mostrarResultado($opciónJugador, $opciónOponente)
{
    $textoOpciones = OPCIONES[$opciónJugador] . " \t " . OPCIONES[$opciónOponente];
    //$resultadoAngulo = textoResultado(calcularResultadoPorAngulo($opciónJugador, $opciónOponente));
    $resultadoAngulo2 = textoResultado(calcularResultadoPorAngulo2($opciónJugador, $opciónOponente));
    //$resultadoLogica = textoResultado(calcularResultadoPorLogica($opciónJugador, $opciónOponente));
    //echo "$textoOpciones\t$resultadoAngulo\t$resultadoAngulo2\t$resultadoLogica\n";
    echo "$textoOpciones\t$resultadoAngulo2\n";
}

function textoResultado($resultado)
{
    return match ($resultado) {
        EMPATA => "Empata",
        GANA => "Gana",
        PIERDE => "Pierde",
    };
}
