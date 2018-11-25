<?php

namespace MultiChoice;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'MultipleChoice';



$multiple = new MultipleChoice(7, 3);
$examen = [];
$correctos = [];
for ($i = 0;$i < 3;$i++) {
    array_push($examen, $multiple->generarPrueba($i));
    array_push($correctos, $multiple->generarPrueba($i, TRUE));
}
$examen1 = fopen("examen1.txt", "w");
$examen2 = fopen("examen1.txt", "w");
$examen3 = fopen("examen1.txt", "w");
$resueltos1 = fopen("examen1.txt", "w");
$resueltos2 = fopen("examen1.txt", "w");
$resueltos3 = fopen("examen1.txt", "w");
fwrite($examen1, $examen[0]);
fwrite($examen2, $examen[1]);
fwrite($examen3, $examen[2]);
fwrite($resueltos1, $examen[0]);
fwrite($resueltos2, $examen[1]);
fwrite($resueltos3, $examen[2]);