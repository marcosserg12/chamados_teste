<?php

require __DIR__ . '/../vendor/autoload.php';

try{
    $usuario = new Usuario();

    $id_usuario = $_REQUEST["id_usuario"];
    $dados = $usuario->liberarUsuario($id_usuario); 

}catch (Exception $exception){
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(), 
        'data' => []
    ], 500);
}