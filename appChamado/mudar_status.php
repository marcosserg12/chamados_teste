<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();

    $id_chamado = $chamados->mudarStatusChamado($_POST['st_status'], $_POST['id_chamado'], $_POST['id_usuario']);
    $dados = $chamados->mostrarChamado($id_chamado);
    $numero_gestao = $chamados->numerosGestores($dados['id_empresa'], $dados['id_localizacao']);
    $nu_telefone_dono = $chamados->telefoneDono($_POST['id_chamado']);

    $status = $_POST['st_status'];

    if ($status == '0') {
        $st_status = "Status: Aberto";
    } elseif ($status == '1') {
        $st_status = "Status: Em Andamento";
    } elseif ($status == '9') {
        $st_status = "Status: Concluído";
    } else {
        $st_status = "Status inválido";
    }


    // Configurações da Evolution API
    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );


    // Envia mensagem para os números dos gestores
    foreach ($numero_gestao as $nu_telefone_gestor) {
        $mensagemGestor = "Mudança de status do chamado *#{$_POST['id_chamado']}*: \n" .
            "Título: *{$dados['ds_titulo']}* \n" .
            "Responsável: *{$dados['designado']}* \n" .
            "Status: *{$st_status}* \n" .
            "Localização: *{$dados['ds_localizacao']}* \n" .
            "Empresa: *{$dados['ds_empresa']}* \n" .
            "*Confira em:* \n" .
            "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$_POST['id_chamado']}";
        $numero_gestor = '55' . $nu_telefone_gestor['nu_telefone'];
        $evolutionSender->sendMessage($numero_gestor, $mensagemGestor);
    }

    // Envia mensagem para o dono do chamado
    $mensagemDono = "Mudança de status do chamado *#{$_POST['id_chamado']}*: \n" .
                    "Título: *{$dados['ds_titulo']}* \n" .
                    "Status: *{$st_status}* \n" .
                    "Localização: *{$dados['ds_localizacao']}* \n" .
                    "Empresa: *{$dados['ds_empresa']}* \n" .
                    "*Confira em:* \n" .
                    "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$_POST['id_chamado']}";


    $dono = '55' . $nu_telefone_dono;
    $evolutionSender->sendMessage($dono, $mensagemDono);

    echo json_response([
        'message' => 'Chamado alterado com sucesso!',
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
