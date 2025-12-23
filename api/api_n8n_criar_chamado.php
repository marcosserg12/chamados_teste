<?php
// api_n8n_criar_chamado.php
header('Content-Type: application/json');

// Define o fuso horário para o Brasil (Brasília)
date_default_timezone_set('America/Sao_Paulo');

// Inclua sua conexão
require_once '../conexao.php'; // AQUI: Ajuste o caminho para voltar uma pasta se necessário, pois a URL tem /api/

try {
    // 1. Recebe os dados do n8n
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);

    if (!$dados) {
        throw new Exception("Nenhum dado JSON recebido");
    }

    $connection = Conecta::getConexao();
    $connection->beginTransaction();

    // 2. Gera a data atual pelo PHP (formato YYYY-MM-DD HH:MM:SS para o MySQL)
    $dataAtual = date('Y-m-d H:i:s');

    // 3. Query de Inserção
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
                st_status
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
                0
            )";

    $stmt = $connection->prepare($sql);
    $stmt->execute([
        ':id_usuario' => $dados['id_usuario'],
        ':ds_titulo'  => $dados['ds_titulo'],
        ':ds_descricao' => $dados['ds_descricao'],
        ':dt_data_chamado' => $dataAtual, // Usa a data do PHP
        ':id_empresa' => $dados['id_empresa'],
        ':id_localizacao' => $dados['id_localizacao'],
        ':id_tipo_chamado' => $dados['id_tipo_chamado'],
        ':id_motivo_principal' => $dados['id_motivo_principal'],
        ':id_motivo_associado' => !empty($dados['id_motivo_associado']) ? $dados['id_motivo_associado'] : null,
        ':st_grau' => $dados['st_grau'] ?? null
    ]);

    $id_chamado = $connection->lastInsertId();

    // 4. Processamento do Anexo (Salva na pasta uploads/ANO-MES-DIA/ID/)
    if (!empty($dados['url_anexo']) && filter_var($dados['url_anexo'], FILTER_VALIDATE_URL)) {

        // Data para a pasta (apenas Y-m-d)
        $dataPasta = date('Y-m-d');

        // Ajuste o caminho relativo conforme a estrutura do seu servidor
        // Se este arquivo está em /api/, e uploads está na raiz, use ../uploads
        $pastaRelativa = "uploads/{$dataPasta}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        if (!is_dir($pastaAbsoluta)) {
            mkdir($pastaAbsoluta, 0755, true);
        }

        // Extrai extensão ou usa jpg como padrão
        $pathInfo = pathinfo(parse_url($dados['url_anexo'], PHP_URL_PATH));
        $extensao = isset($pathInfo['extension']) ? $pathInfo['extension'] : 'jpg';

        $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
        $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

        // Baixa a imagem
        $conteudoArquivo = file_get_contents($dados['url_anexo']);

        if ($conteudoArquivo !== false) {
            file_put_contents($caminhoFinal, $conteudoArquivo);

            // Salva no banco rl_arquivo_chamado o caminho relativo
            // Atenção: O caminho salvo deve ser aquele que o seu front-end lê.
            // Geralmente removemos o "../" para salvar no banco.
            $caminhoParaBanco = $dataPasta . "/" . $id_chamado . "/" . $nomeArquivo;

            $stmtArq = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
            $stmtArq->execute([$id_chamado, $caminhoParaBanco]);
        }
    }

    $connection->commit();
    echo json_encode([
        'status' => 'sucesso',
        'id_chamado' => $id_chamado,
        'mensagem' => 'Chamado criado em ' . date('d/m/Y H:i:s')
    ]);

} catch (Exception $e) {
    if (isset($connection)) $connection->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>