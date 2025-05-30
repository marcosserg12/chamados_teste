<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $dadosUsuario = Security::getUser();

    $usuario = new Usuario();

    $isValid = $usuario->verificarSenhaAtual($dadosUsuario['id_usuario'], $_POST['ds_senha_atual']);

    echo json_response([
        'data' => [
            'is_valid' => $isValid
        ]
    ]);

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}