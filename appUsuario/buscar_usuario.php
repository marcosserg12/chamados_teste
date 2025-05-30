<?php

require __DIR__ . '/../vendor/autoload.php';

try {

    // Validação simples do ID
    if (empty($_GET['id'])) {
        throw new Exception('ID do usuário não foi fornecido.');
    }

    $idUsuario = intval($_GET['id']);

    $usuario = new Usuario();
    $dados = $usuario->buscaUsuario($idUsuario);

    if (!$dados) {
        throw new Exception('Usuário não encontrado.');
    }

    // Resposta de sucesso
    echo json_response([
        'message' => 'Usuário encontrado com sucesso.',
        'data' => $dados
    ]);

} catch (Exception $e) {
    // Resposta de erro
    echo json_response([
        'message' => 'ERRO: ' . $e->getMessage(),
        'data' => []
    ], 500);
}
