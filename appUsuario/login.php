<?php

require __DIR__ . '/../vendor/autoload.php';
session_start();

// $id_hospital   	= $_REQUEST['id_hospital'];
$ds_usuario  	= $_REQUEST['ds_usuario'];
$ds_senha  		= $_REQUEST['ds_senha'];


try{
    $security = new Security();

    $security->authorize([
        // 'id_hospital' => $id_hospital,
        'ds_usuario' => $ds_usuario,
        'ds_senha' => $ds_senha
    ]);

    echo json_response([
        'message' => 'Usuário autenticado!',
        'data' => [
            'usuario' => Security::getUser()
        ]
    ]);

}catch (Exception $exception) {
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}