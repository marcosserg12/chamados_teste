<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();

    $id_chamado = $chamados->gravarComentario($_POST['id_chamado'], $_POST['ds_comentario'], $_POST['id_usuario']);
    $nu_telefone_responsavel = $chamados->verificarNumeroTelChamado($_POST['id_chamado']);
    $nu_telefone_dono = $chamados->telefoneDono($_POST['id_chamado']);
    // Configurações da Evolution API
    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );

    $messageText = "Novo comentário no chamado #{$id_chamado}: \n".
                    "{$_POST['ds_comentario']}\n".
                    "*Confira em:* \n" .
                    "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$id_chamado}";

    $responsavel = '55' . $nu_telefone_responsavel; // Ajuste para o número desejado
    $dono = '55' . $nu_telefone_dono; // Ajuste para o número desejado

    $evolutionSender->sendMessage($responsavel, $messageText);
    $evolutionSender->sendMessage($dono, $messageText);

    // if ($result === false) {
    //     throw new Exception('Falha ao enviar mensagem via Evolution API');
    // }



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
