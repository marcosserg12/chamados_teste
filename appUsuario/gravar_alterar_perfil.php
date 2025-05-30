<?php

require __DIR__ . '/../vendor/autoload.php';

try{

    $dadosUsuario = Security::getUser();

    $usuario = new Usuario();

    $usuario->alterarDadosUsuario($dadosUsuario['id_usuario'], $_POST);

    echo json_response([
        'message' => 'Senha alterada com sucesso!',
        'data' => []
    ]);

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}