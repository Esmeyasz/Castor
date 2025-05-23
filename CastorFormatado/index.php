<?php
$datafile = 'usuarios.json';
$method = $_SERVER['REQUEST_METHOD'];

function carregarUsuarios() {
    global $datafile;
    return file_exists($datafile) ? json_decode(file_get_contents($datafile), true) ?? [] : [];
}

function salvarUsuarios($usuarios) {
    global $datafile;
    file_put_contents($datafile, json_encode($usuarios, JSON_PRETTY_PRINT));
}

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    if (
        empty($input['nome']) ||
        empty($input['email']) ||
        empty($input['senha']) ||
        empty($input['endereco']) ||
        empty($input['telefone'])
    ) {
        http_response_code(400);
        echo json_encode(['error' => 'Todos os campos devem ser preenchidos']);
        exit;
    }

    $usuarios = carregarUsuarios();
    $novoUsuario = [
        "id" => count($usuarios) + 1,
        "nome" => $input['nome'],
        "email" => $input['email'],
        "endereco" => $input['endereco'],
        "telefone" => $input['telefone'],
        "senha" => password_hash($input['senha'], PASSWORD_BCRYPT)
    ];

    $usuarios[] = $novoUsuario;
    salvarUsuarios($usuarios);
    echo json_encode(['message' => 'Usuário cadastrado com sucesso', 'usuario' => $novoUsuario]);
}
?>