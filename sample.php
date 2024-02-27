<?php

ini_set('max_execution_time', 0);
ini_set('memory_limit', -1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'src/MiniTimer.php';

$miniTimer = new MiniTimer();

$miniTimer->start('Do a thing');
usleep(100000); // Simule un travail

$miniTimer->start('Sub Task 1');
usleep(200000); // Simule un travail pour la sous-tâche
$miniTimer->stop('Sub Task 1');

$miniTimer->start('Sub Task 2');
usleep(150000); // Simule un autre travail pour une deuxième sous-tâche
$miniTimer->stop('Sub Task 2');

$miniTimer->stop('Do a thing');

$miniTimer->display();
