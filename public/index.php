<?php

// Habilitamos el manejo de errores durante el desarrollo.

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Autoload de composer

require __DIR__ . '/../vendor/autoload.php';

// Incluimos el archivo de ruteo principal

require __DIR__ . '/../src/index.php';