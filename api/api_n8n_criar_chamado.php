<?php
// api_n8n_criar_chamado.php
ini_set('memory_limit', '256M');
ini_set('post_max_size', '20M');
header('Content-Type: application/json');
date_default_timezone_set('America/Sao_Paulo');

require_once '../Classes/Conecta.php';

try {
    // 1. Recebe e valida dados
    $input = file_get_contents('php://input');
    $dados = json_decode($input, true);

    if (!$dados) throw new Exception("Nenhum dado JSON recebido");

    $connection = Conecta::getConexao();
    $connection->beginTransaction();

    $dataAtual = date('Y-m-d H:i:s');

    // Lógica st_anexo
    $temAnexo = !empty($dados['url_anexo']) && $dados['url_anexo'] !== "";
    $st_anexo = $temAnexo ? 'S' : 'N';

    if(isset($dados['st_grau']) && $dados['st_grau'] == 777) {
        $dados['st_grau'] = null;
    }

    // 2. Insert Principal
    $sql = "INSERT INTO tb_chamados (
                id_usuario, ds_titulo, ds_descricao, dt_data_chamado,
                id_empresa, id_localizacao, id_tipo_chamado,
                id_motivo_principal, id_motivo_associado, st_grau
            ) VALUES (
                :id_usuario, :ds_titulo, :ds_descricao, :dt_data_chamado,
                :id_empresa, :id_localizacao, :id_tipo_chamado,
                :id_motivo_principal, :id_motivo_associado, :st_grau
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
        ':st_anexo' => $st_anexo
    ]);

    $id_chamado = $connection->lastInsertId();

    // 3. Processamento do Anexo (CORRIGIDO PARA BASE64 PURO)
    if ($temAnexo) {

        $dataPasta = date('Y-m-d');
        // Caminho Absoluto Seguro
        $pastaRelativa = "uploads/{$dataPasta}/{$id_chamado}/";
        $pastaAbsoluta = __DIR__ . "/../" . $pastaRelativa;

        // Cria a pasta se não existir
        if (!is_dir($pastaAbsoluta)) {
            // 0777 garante permissão de escrita
            mkdir($pastaAbsoluta, 0777, true);
        }

        $dadoAnexo = $dados['url_anexo'];
        $conteudoArquivo = null;

        // --- ETAPA A: Decodificação ---

        // Verifica se é URL (http...)
        if (filter_var($dadoAnexo, FILTER_VALIDATE_URL)) {
            $conteudoArquivo = file_get_contents($dadoAnexo);
        }
        // Se não for URL, assumimos Base64
        else {
            // Se vier com prefixo "data:image/...", removemos
            if (strpos($dadoAnexo, 'base64,') !== false) {
                $dadoAnexo = explode('base64,', $dadoAnexo)[1];
            }

            // Remove espaços e quebras de linha que podem corromper
            $dadoAnexo = str_replace([' ', "\n", "\r"], ['+', '', ''], $dadoAnexo);

            // Transforma o TEXTO em ARQUIVO BINÁRIO REAL
            $conteudoArquivo = base64_decode($dadoAnexo);
        }

        // --- ETAPA B: Descobrir a Extensão Real (Magia do finfo) ---
        if ($conteudoArquivo) {

            // Usa o PHP para ler os bits do arquivo e dizer o que é
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($conteudoArquivo);

            // Mapa de Mimes para Extensões
            $extensoes = [
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/webp' => 'webp',
                'application/pdf' => 'pdf',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx', // Excel novo
                'application/vnd.ms-excel' => 'xls', // Excel velho
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx', // Word
                'application/msword' => 'doc',
                'application/zip' => 'zip',
                'text/plain' => 'txt'
            ];

            // Define extensão (Padrão jpg se não achar)
            $extensao = isset($extensoes[$mimeType]) ? $extensoes[$mimeType] : 'jpg';

            // Se for application/zip mas estamos esperando Excel, força xlsx
            if ($mimeType == 'application/zip' && strpos($dadoAnexo, 'UEsDB') === 0) {
                $extensao = 'xlsx';
            }

            // Gera nome e salva
            $nomeArquivo = uniqid("anexo_{$id_chamado}_") . "." . $extensao;
            $caminhoFinal = $pastaAbsoluta . $nomeArquivo;

            // Salva o binário no disco
            $salvou = file_put_contents($caminhoFinal, $conteudoArquivo);

            if ($salvou !== false) {
                $caminhoParaBanco = $dataPasta . "/" . $id_chamado . "/" . $nomeArquivo;
                $stmtArq = $connection->prepare("INSERT INTO rl_arquivo_chamado (id_chamado, ds_caminho_arquivo) VALUES (?, ?)");
                $stmtArq->execute([$id_chamado, $caminhoParaBanco]);
            }
        }
    }

    $connection->commit();
    echo json_encode([
        'status' => 'sucesso',
        'id_chamado' => $id_chamado,
        'tem_anexo' => $st_anexo,
        'mensagem' => 'Chamado criado.'
    ]);
} catch (Exception $e) {
    if (isset($connection)) $connection->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
}
?>