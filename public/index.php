<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

include __DIR__ . '/../vendor/autoload.php';

if (empty($_GET['presentation'])) {
    $template      = __DIR__ . '/../templates/default/index.html.php';
    $presentations = \App\Model\Presentation::findAll();
}



include __DIR__ . '/../templates/default/base.html.php';