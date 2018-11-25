[![Build Status](https://travis-ci.org/DanielLezcano727/Multiple-Choice.svg?branch=master)](https://travis-ci.org/DanielLezcano727/Multiple-Choice)

[![Coverage Status](https://coveralls.io/repos/github/DanielLezcano727/Multiple-Choice/badge.svg?branch=master)](https://coveralls.io/github/DanielLezcano727/Multiple-Choice?branch=master) DESACTUALIZADO. Entrar para ver la cobertura actualizada.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/DanielLezcano727/Multiple-Choice/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/DanielLezcano727/Multiple-Choice/?branch=master)

Micaela Siles - Daniel Lezcano

# Multiple-Choice

## Función

Este proyecto genera exámenes del estilo multiple choice a partir de un archivo .yml que contiene las preguntas y respuestas. Los mismos se crearán aleatoriamente (a menos que se indique lo contrario) y se podrá elegir la cantidad de temas distintos para el mismo examen que se quieran generar. El resultado se devolverá en un archivo .html, que puede ser convertido a pdf.

## Características

 - Mezcla las preguntas que se le ingresen y las elige al azar (opcional).
 - Mezcla las respuestas (opcional).
 - Incluye las opciones “Ninguna de las anteriores” y “Todas las anteriores” (opcional).
 - Posibilidad de modificar el texto “Ninguna de las anteriores”.
 - Si existe más de una opción correcta se reemplazará esa opción por una que posea ambas opciones correctas en conjunto, y se generarán distintas opciones que  también contengan más de una opción en conjunto.
 - El texto “Todas las anteriores” aparece al final de todas las respuestas, antes del texto “Ninguna de las anteriores”.
 - La ejecución del programa devuelve el examen en formato .html.
 - Se pueden generar una cantidad de temas arbitrarios.
 - Se puede generar el examen ya resuelto.

## Uso

Dentro del directorio “Preguntas” se coloca un archivo .yml que contenga las preguntas con una estructura similar a la que se provee en el ejemplo. Dentro de ese archivo deberá indicarse cuáles son las preguntas que no deben tener las opciones “todas las anteriores” y “ninguna de las anteriores” y, en el caso de querer utilizar diferentes frases para la opción “ninguna de las anteriores”, también deberán incluirse estos textos en el mismo archivo. Luego de esto deberá ejecutarse un archivo que cree una instancia de la clase Multiple choice y ejecutar la función generarPrueba, a la cual se le indica que incluya o no la resolución del examen. Esta función devuelve un string, por lo que se debe guardar dentro de un archivo .html para poder visualizarlo en un navegador.

## Errores y limitaciones

 - Solo acepta modificación del texto en la opción “Ninguna de las anteriores” y solo en el caso de que sea “Ninguno de los anteriores.”.
 - Si hay más de una respuesta correcta y está activada la opción de “todas las anteriores”, ésta se generará luego de las opciones que incluyen más de una opción.
 - Se devuelve el examen generado como string, por lo que el usuario debe realizar el procedimiento para guardarlo.

