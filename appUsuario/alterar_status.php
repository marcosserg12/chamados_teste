<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $id_usuario = $_REQUEST['id_usuario'];
    $st_ativo = $_REQUEST['st_ativo'];
    $usuario = new Usuario();

    $usuario->alterarStatus($id_usuario, $st_ativo);

    echo json_response([
        'message' => 'Dados alterados com sucesso!',
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