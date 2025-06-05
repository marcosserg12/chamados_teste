<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();

    $id_chamado = $chamados->gravarComentario($_POST['id_chamado'], $_POST['ds_comentario'], $_POST['id_usuario']);
    $nu_telefone = $chamados->verificarNumeroTelChamado($_POST['id_chamado']);
    // Configurações da Evolution API
    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );

    $messageText = "Novo comentário no chamado #{$_POST['id_chamado']}: {$_POST['ds_comentario']}";
    $toNumber = '55' . $nu_telefone; // Ajuste para o número desejado

    $result = $evolutionSender->sendMessage($toNumber, $messageText);

    if ($result === false) {
        throw new Exception('Falha ao enviar mensagem via Evolution API');
    }



    echo json_response([
        'message' => 'Comentário enviado!',
        'data' => [
            'id_chamado' => $id_chamado
        ]
    ]);
} catch (Exception $exception) {
    echo json_response([
        'message' => 'ERRO: ' . $exception->getMessage(),
        'data' => []
    ], 500);
}
