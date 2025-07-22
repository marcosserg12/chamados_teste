<?php

require __DIR__ . '/../vendor/autoload.php';

try {
    $chamados = new Chamados();

    $id_chamado = $chamados->gravar($_POST);
    $numerosTecnico = $chamados->numerosTecnicos();
    $numero_dono = $chamados->buscarNumeroPeloUsr($_POST['id_usuario']);
    $numero_gestao = $chamados->numerosGestores($_POST['id_empresa'],$_POST['id_localizacao']);
    $loc_empresa = $chamados->infoLocEmpresa($_POST['id_empresa'],$_POST['id_localizacao']);

    // Configurações da Evolution API
    $evolutionSender = new EvolutionWhatsAppSender(
        'http://145.223.26.225:8081/',
        'B825E8E34BE9-4486-AD4E-C4B8ECA49257',
        'teste'
    );

    // Envia mensagem para os números dos técnicos
    foreach ($numerosTecnico as $nu_telefone_tecnico) {
        if($_POST['id_tipo_chamado'] == 1){
            if($nu_telefone_tecnico['id_usuario'] == 41) {
                $numero_tecnico = '55' . $nu_telefone_tecnico['nu_telefone'];
                $mensagemTecnico = "Novo chamado aberto #{$id_chamado}: {$_POST['ds_titulo']}\n".
                                "Empresa: {$loc_empresa['ds_empresa']}\n".
                                "* Confira em: * \n" .
                                "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$id_chamado}";
                $result = $evolutionSender->sendMessage($numero_tecnico, $mensagemTecnico);
            }
        }else{
            $numero_tecnico = '55' . $nu_telefone_tecnico['nu_telefone'];
            $mensagemTecnico = "Novo chamado aberto #{$id_chamado}: {$_POST['ds_titulo']}\n".
                                "Localização: {$loc_empresa['ds_localizacao']}\n".
                                "Empresa: {$loc_empresa['ds_empresa']}\n".
                                "* Confira em: * \n" .
                                "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$id_chamado}";
            $result = $evolutionSender->sendMessage($numero_tecnico, $mensagemTecnico);
        }
    }

    // Envia mensagem para os números dos gestores
    foreach ($numero_gestao as $nu_telefone_gestor) {
        $mensagemGestor = "Novo chamado aberto #{$id_chamado}: {$_POST['ds_titulo']}\n".
                                "Localização: {$loc_empresa['ds_localizacao']}\n".
                                "Empresa: {$loc_empresa['ds_empresa']}\n".
                                "* Confira em: * \n" .
                                "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$id_chamado}";
        $numero_gestor = '55' . $nu_telefone_gestor['nu_telefone'];
        $result = $evolutionSender->sendMessage($numero_gestor, $mensagemGestor);
    }

    // Envia mensagem para o dono do chamado
    // $mensagemDono = "Chamado aberto #{$id_chamado}: {$_POST['ds_titulo']} ";
    $mensagemDono = "Chamado aberto #{$id_chamado}: {$_POST['ds_titulo']}\n".
                    "* Confira em: * \n" .
                    "https://chamados.sisibranutro.com.br/Telas/Detalhe_chamado.php?id_chamado={$id_chamado}";


    $numero_dono_msg = '55' . $numero_dono;
    $result = $evolutionSender->sendMessage($numero_dono_msg, $mensagemDono);

    echo json_response([
        'message' => 'Chamado cadastrado com sucesso!',
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
