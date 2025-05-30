<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $usuario = new Usuario();
    $usuario->redefinirSenha($_POST);

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