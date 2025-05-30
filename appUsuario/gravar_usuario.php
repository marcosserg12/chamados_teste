<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $usuario = new Usuario();

    $id_usuario = $usuario->gravarUsuario($_POST);
    echo json_response([
        'message' => 'Usuário cadastrado com sucesso!',
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