<?php

$array_config = [
    "host" => 'mysql-gsb.alwaysdata.net',
    "dbname" => 'gsb_bdd',
    "username" => 'gsb',
    "password" => 'hxGtYdzT.aGWEa6',
];

function getPDO($array_config) {
    // Extract values from the array
    $host = $array_config['host'];
    $dbname = $array_config['dbname'];
    $username = $array_config['username'];
    $password = $array_config['password'];

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Erreur de connexion : " . $e->getMessage());
    }

    return $pdo;
}
