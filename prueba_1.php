<?php

const PAPEL = 0;
const PIEDRA = 1;
const TIJERA = 2;

const EMPATA = 0;
const PIERDE = 1;
const GANA = 2;

mostrarResultado(PAPEL, PAPEL);		// Empata
mostrarResultado(PAPEL, PIEDRA);	// Gana
mostrarResultado(PAPEL, TIJERA);	// Pierde
mostrarResultado(PIEDRA, PAPEL);	// Pierde
mostrarResultado(PIEDRA, PIEDRA);	// Empata
mostrarResultado(PIEDRA, TIJERA);	// Gana
mostrarResultado(TIJERA, PAPEL);	// Gana
mostrarResultado(TIJERA, PIEDRA);	// Pierde
mostrarResultado(TIJERA, TIJERA);	// Empata

function calcularResultadoPorAngulo($opciónJugador, $opciónOponente)
{
	return ((deg2rad($opciónJugador * 120) - deg2rad($opciónOponente * 120) + 2 * M_PI) % (2 * M_PI)) / 2;
}

function calcularResultadoPorAngulo2($opciónJugador, $opciónOponente)
{
	$diferencia = $opciónJugador * 120 - $opciónOponente * 120;
	if ($diferencia < 0) {
		$diferencia += 360;
	}
	return $diferencia / 120;
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
    $textoOpciones = textoOpción($opciónJugador) . " \t " . textoOpción($opciónOponente);
	$resultadoAngulo = textoResultado(calcularResultadoPorAngulo($opciónJugador, $opciónOponente));
	$resultadoAngulo2 = textoResultado(calcularResultadoPorAngulo2($opciónJugador, $opciónOponente));
	$resultadoLogica = textoResultado(calcularResultadoPorLogica($opciónJugador, $opciónOponente));
	echo "$textoOpciones\t$resultadoAngulo\t$resultadoAngulo2\t$resultadoLogica\n";
}

function textoResultado($resultado)
{
	return match ($resultado) {
		EMPATA => "Empata",
		GANA => "Gana",
		PIERDE => "Pierde",
	};
}

function textoOpción($opción)
{
    return match ($opción) {
        PAPEL => "Papel",
        PIEDRA => "Piedra",
        TIJERA => "Tijera",
    };
}
