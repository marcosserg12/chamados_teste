<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $id_usuario = $_REQUEST['id_usuario'];
    $usuario = new Usuario();

    $usuario->resetarSenha($id_usuario);

    echo json_response([
        'message' => 'Senha resetada com sucesso! Senha padrão elite123',
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