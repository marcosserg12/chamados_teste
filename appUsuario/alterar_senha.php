<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $id_usuario = $_REQUEST['id_usuario'];
    $ds_Senha = $_REQUEST['senha'];
    $usuario = new Usuario();

    $usuario->alterarSenha($id_usuario, $ds_Senha);

    echo json_response([
        'message' => 'Senha alterada com sucesso!',
        'data' => [
            'id_usuario' => $id_usuario
        ]
    ]);
}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}