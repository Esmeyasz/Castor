<?php
require_once 'utils.php';

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = $_POST;

    // Validar campos obrigatórios
    $camposObrigatorios = ['nome', 'email', 'idade', 'tipo', 'rua', 'numero', 'cidade', 'estado'];
    foreach ($camposObrigatorios as $campo) {
        if (empty($input[$campo])) {
            http_response_code(400);
            echo "<p class='erro'>Campo obrigatório não preenchido: $campo</p>";
            exit;
        }
    }

   if (emailExiste($input['email'])) {
    header("Location: index.html?erro=email");
    exit;
}

    // Formatando telefone brasileiro com DDD
    $telefone = preg_replace('/[^0-9]/', '', $input['telefone']);
    if (strlen($telefone) >= 10) {
        $telefone = sprintf("(%s) %s-%s",
            substr($telefone, 0, 2),
            substr($telefone, 2, -4),
            substr($telefone, -4)
        );
    }

    $usuarios = carregarUsuarios();

    $novoUsuario = [
        "id" => count($usuarios) + 1,
        "nome" => $input['nome'],
        "email" => $input['email'],
        "telefone" => $telefone ?? '',
        "idade" => $input['idade'],
        "genero" => $input['genero'] ?? '',
        "tipo" => $input['tipo'],
        "endereco" => [
            "rua" => $input['rua'],
            "numero" => $input['numero'],
            "cidade" => $input['cidade'],
            "estado" => $input['estado']
        ],
        "promocoes" => isset($input['promocoes']),
        "comentarios" => $input['comentarios'] ?? ''
    ];

    $usuarios[] = $novoUsuario;
    salvarUsuarios($usuarios);
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado do Cadastro</title>
    <link rel="stylesheet" href="resultado.css">
</head>
<body>
    <div class="container">
        <h2>Usuário cadastrado com sucesso!</h2>
        <ul>
            <li><strong>Nome:</strong> <?= htmlspecialchars($novoUsuario['nome']) ?></li>
            <li><strong>Email:</strong> <?= htmlspecialchars($novoUsuario['email']) ?></li>
            <li><strong>Telefone:</strong> <?= htmlspecialchars($novoUsuario['telefone']) ?></li>
            <li><strong>Idade:</strong> <?= htmlspecialchars($novoUsuario['idade']) ?></li>
            <li><strong>Gênero:</strong> <?= htmlspecialchars($novoUsuario['genero']) ?></li>
            <li><strong>Tipo de Cliente:</strong> <?= htmlspecialchars($novoUsuario['tipo']) ?></li>
            <li><strong>Endereço:</strong> <?= htmlspecialchars($novoUsuario['endereco']['rua']) ?>,
                <?= htmlspecialchars($novoUsuario['endereco']['numero']) ?>,
                <?= htmlspecialchars($novoUsuario['endereco']['cidade']) ?>/<?= htmlspecialchars($novoUsuario['endereco']['estado']) ?></li>
            <li><strong>Promoções:</strong> <?= $novoUsuario['promocoes'] ? 'Sim' : 'Não' ?></li>
            <li><strong>Comentários:</strong> <?= nl2br(htmlspecialchars($novoUsuario['comentarios'])) ?></li>
        </ul>
    </div>
</body>
</html>