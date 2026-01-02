<?php
// api_n8n_criar_chamado.php
header('Content-Type: application/json');

// Define o fuso horário
date_default_timezone_set('America/Sao_Paulo');

// Inclua sua conexão
require_once '../Classes/Conecta.php';

try {
    // 1. Recebe os dados do n8n
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);

    if (!$dados) {
        throw new Exception("Nenhum dado JSON recebido");
    }

    $connection = Conecta::getConexao();
    $connection->beginTransaction();

    $dataAtual = date('Y-m-d H:i:s');

    // --- NOVA LÓGICA DO ST_ANEXO ---
    // Verifica se url_anexo existe e não está vazia
    $temAnexo = !empty($dados['url_anexo']) && $dados['url_anexo'] !== "";
    $st_anexo = $temAnexo ? 'S' : 'N';
    // -------------------------------

    // 2. Query de Inserção (Atualizada com st_anexo)
    $sql = "INSERT INTO tb_chamados (
                id_usuario,
                ds_titulo,
                ds_descricao,
                dt_data_chamado,
                id_empresa,
                id_localizacao,
                id_tipo_chamado,
                id_motivo_principal,
                id_motivo_associado,
                st_grau,
                st_status,
                st_anexo  -- Coluna Nova
            ) VALUES (
                :id_usuario,
                :ds_titulo,
                :ds_descricao,
                :dt_data_chamado,
                :id_empresa,
                :id_localizacao,
                :id_tipo_chamado,
                :id_motivo_principal,
                :id_motivo_associado,
                :st_grau,
                0,
                :st_anexo -- Parâmetro Novo
            )";

    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $dados['id_usuario'],
        ':ds_titulo'  => $dados['ds_titulo'],
        ':ds_descricao' => $dados['ds_descricao'],
        ':dt_data_chamado' => $dataAtual,
        ':id_empresa' => $dados['id_empresa'],
        ':id_localizacao' => $dados['id_localizacao'],
        ':id_tipo_chamado' => $dados['id_tipo_chamado'],
        ':id_motivo_principal' => $dados['id_motivo_principal'],
        ':id_motivo_associado' => !empty($dados['id_motivo_associado']) ? $dados['id_motivo_associado'] : null,
        ':st_grau' => $dados['st_grau'] ?? null,
        ':st_anexo' => $st_anexo // Passando o valor S ou N
    ]);

    $id_chamado = $connection->lastInsertId();

    // 3. Processamento do Arquivo (Só entra aqui se tiver anexo)
    if ($temAnexo && filter_var($dados['url_anexo'], FILTER_VALIDATE_URL)) {

        $dataPasta = date('Y-m-d');
        // Ajuste o caminho relativo:
        $pastaRelativa = "uploads/{$dataPasta}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        if (!is_dir($pastaAbsoluta)) {
            mkdir($pastaAbsoluta, 0755, true);
        }

        $pathInfo = pathinfo(parse_url($dados['url_anexo'], PHP_URL_PATH));
        $extensao = isset($pathInfo['extension']) ? $pathInfo['extension'] : 'jpg';

        $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
        $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

        // Baixa o arquivo
        $conteudoArquivo = file_get_contents($dados['url_anexo']);

        if ($conteudoArquivo !== false) {
            file_put_contents($caminhoFinal, $conteudoArquivo);

            $caminhoParaBanco = $dataPasta . "/" . $id_chamado . "/" . $nomeArquivo;

            $stmtArq = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
            $stmtArq->execute([$id_chamado, $caminhoParaBanco]);
        }
    }

    $connection->commit();
    echo json_encode([
        'status' => 'sucesso',
        'id_chamado' => $id_chamado,
        'tem_anexo' => $st_anexo,
        'mensagem' => 'Chamado criado com sucesso.'
    ]);
} catch (Exception $e) {
    if (isset($connection)) $connection->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
