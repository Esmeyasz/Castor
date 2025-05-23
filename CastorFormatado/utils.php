<?php

$datafile = 'usuarios.json';

function carregarUsuarios() {
    global $datafile;
    $json = file_get_contents($datafile);
    return json_decode($json, true) ?? [];
}

function salvarUsuarios($usuarios) {
    global $datafile;
    file_put_contents($datafile, json_encode($usuarios, JSON_PRETTY_PRINT));
}

function emailExiste($email) {
    $usuarios = carregarUsuarios();
    foreach ($usuarios as $usuario) {
        if (strtolower($usuario['email']) === strtolower($email)) {
            return true;
        }
    }
    return false;
}