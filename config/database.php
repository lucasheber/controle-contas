<?php

define('DB_HOST', 'localhost');
define('DB_NAME', 'controle_contas');
define('DB_USER', 'root');
define('DB_PASS', '');

try {
    $db = new PDO('mysql:host=' . DB_HOST . ';dbname='. DB_NAME, DB_USER, DB_PASS);

    $db->exec('SET NAMES UTF8');
} catch (PDOException $e) {
    $db = null;
    die("Erro de conexao: " . $e->getMessage());
}

