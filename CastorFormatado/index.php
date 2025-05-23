<?php
require_once 'utils.php';

$datafile = 'usuarios.json';
$method = $_SERVER['REQUEST_METHOD'];

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if ($method === 'GET') {
    $usuarios = carregarUsuarios();

    // Remove a senha de cada usuário antes de retornar
    $usuariosSemSenha = array_map(function ($usuario) {
        unset($usuario['senha']);
        return $usuario;
    }, $usuarios);

    echo json_encode($usuariosSemSenha);
    exit;
}

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

    if (emailExiste($input['email'])) {
        http_response_code(409);
        echo json_encode(['error' => 'E-mail já cadastrado']);
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