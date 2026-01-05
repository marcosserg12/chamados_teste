<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require  '../vendor/autoload.php';


$segredo_sistema = 'S!st3m@D3Ch@mados#2026$Ibr@Nutr0%Sup3rS3cr3tK3y&Vasco';
// ... seus includes ...
$segredo_sistema = 'a3f9e2d1c8b7a6059483726150493827160594837261504938271605948372b1'; // A MESMA DO N8N

if (isset($_GET['id_chamado']) && isset($_GET['token'])) {
    $id = $_GET['id_chamado'];
    $token_recebido = $_GET['token'];
    $token_calculado = hash_hmac('sha256', (string)$id, $segredo_sistema);

    echo "<h1>Debug do Link Mágico</h1>";
    echo "ID recebido: " . $id . "<br>";
    echo "Token Recebido (URL): " . $token_recebido . "<br>";
    echo "Token Calculado (PHP): " . $token_calculado . "<br>";

    if (hash_equals($token_calculado, $token_recebido)) {
        echo "<h2 style='color:green'>SUCESSO: Os tokens batem!</h2>";
        echo "O problema é que as variáveis de sessão não estão satisfazendo o Security::isAuthenticated().";
        die(); // Para o script aqui para você ler
    } else {
        echo "<h2 style='color:red'>ERRO: Os tokens são diferentes.</h2>";
        echo "Verifique se a senha secreta é EXATAMENTE igual no n8n e aqui.";
        die();
    }
}
// ... resto do código ...

if (isset($_GET['id_chamado']) && isset($_GET['token'])) {

    $id_recebido = $_GET['id_chamado'];
    $token_recebido = $_GET['token'];

    $token_valido = hash_hmac('sha256', $id_recebido, $segredo_sistema);

    if (hash_equals($token_valido, $token_recebido)) {



        $_SESSION['autenticado'] = true;
        $_SESSION['usuario_id'] = 0; // ID 0 ou um ID de "Visitante"
        $_SESSION['usuario_nome'] = "Acesso via WhatsApp";
    }
}


if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

// ... Restante do código da página ...
$id_usuario = Security::getUser()['id_usuario'];
$id_perfil = Security::getUser()['id_perfil'];
$id_chamado = $_REQUEST['id_chamado'];
$chamados = new Chamados();
$usuario = new Usuario();
$geral = new Geral();
$dados = $chamados->mostrarChamado($id_chamado);
$dados_arquivos = $chamados->mostrararquivosChamado($id_chamado);
$historicos = $chamados->lista_historico($id_chamado);
$lista_usuarios = $usuario->listarUsuario_adm($dados['id_usuario_designado']);

$lista_empresas = $chamados->lista_empresas();
$lista_localizacao = $chamados->lista_localizacao($dados['id_empresa']);
$lista_tipo_chamado = $chamados->lista_tipos_chamados();
$lista_motivo = $chamados->lista_motivo($dados['id_tipo_chamado']);
$lista_motivo_associado = $chamados->lista_motivo_associado($dados['id_motivo_principal']);

$dataAtual = new DateTime('now', new DateTimeZone('America/Sao_Paulo'));
$dt_update = new DateTime($dados['dt_update']); // se vier no formato Y-m-d H:i:s
$intervalo = $dataAtual->diff($dt_update)->days;

if ($dados['st_status'] == 0) {
    $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            Aberto
                        </span>';
} else if ($dados['st_status'] == 1) {
    $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                            Em Andamento
                        </span>';
} else {
    $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                            Resolvido
                        </span>';
}

?>


<body class="bg-gray-100 font-sans">

    <?php include   '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include   '../header.php'; ?>
        <main class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <input hidden id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">
            <input hidden id="id_chamado" name="id_chamado" value="<?php echo $id_chamado; ?>">
            <div id="ticketDetailView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Detalhes do Chamado</h2>
                    <button id="backFromDetailBtn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                    </button>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex flex-wrap justify-between gap-4 items-start">
                            <div>
                                <h3 class="text-lg font-bold text-gray-800" id="detailTicketTitle"><?= $dados['ds_titulo'] ?> <span class="ml-3 text-sm text-gray-500" id="detailTicketId">#<?= $dados['id_chamado'] ?></span></h3>
                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                                    Criado por : <?= $dados['criador'] ?>
                                </span>
                                <span class="ml-3 text-sm text-gray-500" id="detailTicketDate">Abertura em: <?= $geral->formataData($dados['dt_data_chamado']) ?></span>
                                <span class="ml-3 text-sm text-gray-500" id="detailTicketHour"><?= $geral->formataHora($dados['dt_data_chamado']) ?></span>
                                <div class="flex items-center mt-2">
                                    <span id="detailTicketStatus"><?= $st_status ?></span>


                                    <span class="ml-3 text-sm text-gray-500" id="detailTicketDate">Ultimo status: <?= $geral->formataData($dados['dt_update']) ?></span>
                                    <span class="ml-3 text-sm text-gray-500" id="detailTicketHour"><?= $geral->formataHora($dados['dt_update']) ?></span>
                                </div>
                                <?php if ($dados['st_status'] != 0) : ?>
                                    <div class="flex items-center mt-2">
                                        <span class="px-2 py-1 text-xs rounded-full bg-purple-100 text-purple-800">
                                            Responsável : <?= $dados['designado'] ?>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div id="adminTicketActions">
                                <div class="flex flex-wrap gap-2">
                                    <?php if ($id_perfil == 1 || $id_perfil == 4): ?>
                                        <?php if ($dados['st_status'] != 9): ?>
                                            <?php if ($dados['id_usuario_designado'] != $id_usuario): ?>
                                                <button id="assignToMeBtn" onclick="assumirChamado()" class="bg-blue-100 text-blue-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                                    <i class="fas fa-user-plus mr-1"></i> Assumir
                                                </button>
                                            <?php endif; ?>
                                            <div class="relative">
                                                <button id="assignDropdownBtn" class="bg-gray-100 text-gray-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                                    <i class="fas fa-users mr-1"></i> Designar
                                                    <i class="fas fa-chevron-down ml-1"></i>
                                                </button>

                                                <div id="assignDropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 hidden">
                                                    <div class="py-1" id="adminUsersList">
                                                        <?php foreach ($lista_usuarios as $usuario): ?>
                                                            <button
                                                                class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                                                                onclick="designarUsuario(<?= $usuario['id_usuario'] ?>,'<?= $usuario['ds_nome'] ?>')">
                                                                <?= htmlspecialchars($usuario['ds_nome']) ?>
                                                            </button>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if ($dados['st_status'] != 0 && ($id_perfil == 1 || $id_perfil == 4)) : ?>
                                        <button id="resolveTicketBtn" onclick="resolver_reabrir(<?= $id_usuario ?>,<?= $dados['id_chamado'] ?>,0)" class="bg-orange-100 text-orange-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                            <i class="fas fa-redo mr-1"></i> Reabrir Chamado
                                        </button>
                                    <?php endif; ?>

                                    <?php if ($dados['st_status'] != 9 && $dados['designado'] != null && ($id_perfil == 1 || $id_perfil == 4)): ?>
                                        <button id="resolveTicketBtn" onclick="resolver_reabrir(<?= $id_usuario ?>,<?= $dados['id_chamado'] ?>,9)" class="bg-green-100 text-green-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                            <i class="fas fa-check mr-1"></i> Resolvido
                                        </button>
                                    <?php endif; ?>

                                    <?php if (($dados['id_usuario_designado'] == $id_usuario || $dados['id_usuario'] == $id_usuario) && $dados['st_status'] != 9): ?>
                                        <a href="../Telas/Editar_chamado.php?id_chamado=<?= $dados['id_chamado'] ?>" class="bg-purple-100 text-purple-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                            <i class="fas fa-edit mr-1"></i> Editar
                                        </a>
                                    <?php endif; ?>

                                    <?php if ($id_perfil == 2 && $dados['st_status'] == 9 && $intervalo <= 3): ?>
                                        <button id="resolveTicketBtn" onclick="resolver_reabrir(<?= $id_usuario ?>,<?= $dados['id_chamado'] ?>,0)" class="bg-orange-100 text-orange-700 px-3 py-2 rounded-md text-sm flex items-center justify-center">
                                            <i class="fas fa-redo mr-1"></i> Reabrir Chamado
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                        </div>
                    </div>
                    <!-- Empresa e Localização -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-6">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Empresa</h4>
                            <?php
                            foreach ($lista_empresas as $empresa) {
                                if ($empresa['id_empresa'] == $dados['id_empresa']) {
                                    $nome_empresa = htmlspecialchars($empresa['ds_empresa']);

                                    // Define a cor da badge com base no id_empresa (ajuste conforme seu critério real)
                                    $corBadge = '';
                                    $corTexto = 'text-white';

                                    switch ($empresa['id_empresa']) {
                                        case 1:
                                            $corBadge = 'bg-[#E69923]'; // laranja
                                            break;
                                        case 2:
                                            $corBadge = 'bg-[#02be9b]'; // verde
                                            break;
                                        case 3:
                                            $corBadge = 'bg-[#00ABCA]'; // azul
                                            break;
                                        default:
                                            $corBadge = 'bg-gray-300';
                                            $corTexto = 'text-gray-800';
                                    }

                                    echo "<span class=\"px-3 py-1 text-sm font-medium rounded-full {$corBadge} {$corTexto}\">{$nome_empresa}</span>";
                                    break;
                                }
                            }
                            ?>


                        </div>

                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Localização</h4>
                            <p class="text-gray-700">
                                <?php
                                foreach ($lista_localizacao as $localizacao) {
                                    if ($localizacao['id_localizacao'] == $dados['id_localizacao']) {
                                        $nome_local = htmlspecialchars($localizacao['ds_localizacao']);
                                        echo "<span class=\"px-3 py-1 text-sm font-medium rounded-full bg-gray-200 text-gray-800\">{$nome_local}</span>";
                                        break;
                                    }
                                }
                                ?>
                            </p>
                        </div>

                    </div>

                    <!-- Categoria do Chamado -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Tipo de Chamado</h4>
                                <p class="text-gray-700">
                                    <?php
                                    foreach ($lista_tipo_chamado as $tipo) {
                                        if ($tipo['id_tipo_chamado'] == $dados['id_tipo_chamado']) {
                                            echo htmlspecialchars($tipo['ds_tipo_chamado']);
                                            break;
                                        }
                                    }
                                    ?>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Motivo</h4>
                                <p class="text-gray-700">
                                    <?php
                                    foreach ($lista_motivo as $motivo) {
                                        if ($motivo['id_motivo_principal'] == $dados['id_motivo_principal']) {
                                            echo htmlspecialchars($motivo['ds_descricao']);
                                            break;
                                        }
                                    }
                                    ?>
                                </p>
                            </div>

                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Detalhamento</h4>
                                <p class="text-gray-700">
                                    <?php
                                    foreach ($lista_motivo_associado as $detalhe) {
                                        if ($detalhe['id_motivo_associado'] == $dados['id_motivo_associado']) {
                                            echo htmlspecialchars($detalhe['ds_descricao_motivo']);
                                            break;
                                        }
                                    }
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6" <?php echo $dados['id_motivo_associado'] == '6' && $dados['id_tipo_chamado'] == '2' ? '' : 'style="display: none;"' ?>>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Tipo</h4>
                                <p class="text-gray-700">
                                    <?php echo $dados['st_grau'] == '1' ? 'Melhoria' : '' ?>
                                    <?php echo $dados['st_grau'] == '2' ? 'Problema' : '' ?>
                                    <?php echo $dados['st_grau'] == '3' ? 'Cadastro de Paciente' : '' ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6" <?php echo $dados['id_tipo_chamado'] == '1' && $dados['ds_patrimonio'] != '' ? '' : 'style="display: none;"' ?>>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Patrimônio</h4>
                                <p class="text-gray-700">
                                    <?php echo $dados['ds_patrimonio']   ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="p-6">
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Descrição</h4>
                            <p class="text-gray-700" id="detailTicketDescription"><?= $dados['ds_descricao'] ?></p>
                        </div>
                        <?php if ($dados_arquivos[0]["id_chamado"]) { ?>
                            <div class="mb-6">
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Anexos</h4>

                                <!-- Imagens -->
                                <div id="lightgallery" class="grid grid-cols-5 sm:grid-cols-5 md:grid-cols-5 gap-4 mb-6">
                                    <?php
                                    $dados_arquivos = $chamados->mostrararquivosChamado($_REQUEST['id_chamado']);
                                    foreach ($dados_arquivos as $arquivo) {
                                        $extensao = strtolower(pathinfo($arquivo['ds_caminho_arquivo'], PATHINFO_EXTENSION));
                                        $nome = basename($arquivo['ds_caminho_arquivo']);
                                        $caminho = '/uploads/' . $arquivo['ds_caminho_arquivo']; // URL pública

                                        if (in_array($extensao, ['jpg', 'jpeg', 'png', 'gif'])) {
                                            // Imagem - usa LightGallery
                                            echo '
                                        <a href="' . $caminho . '" data-lg-size="1600-1067" class="block border p-2 rounded shadow bg-white w-28">
                                        <img src="' . $caminho . '" class="h-16 w-full object-cover rounded" />
                                            </a>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- PDFs -->
                                <div class="grid grid-cols-1 sm:grid-cols-5 md:grid-cols-5 gap-4">
                                    <?php
                                    foreach ($dados_arquivos as $arquivo) {
                                        $extensao = strtolower(pathinfo($arquivo['ds_caminho_arquivo'], PATHINFO_EXTENSION));
                                        $nome = basename($arquivo['ds_caminho_arquivo']);
                                        $caminho = '/uploads/' . $arquivo['ds_caminho_arquivo']; // URL pública

                                        if ($extensao === 'pdf') {
                                            echo '
                                            <div class="border p-2 rounded shadow bg-white w-44">
                                                <div class="text-sm text-gray-600 truncate mb-2">' . $nome . '</div>
                                                <button onclick="previewPDF(\'' . $caminho . '\')" class="text-blue-600 text-sm hover:underline mb-1">
                                                    Visualizar PDF
                                                </button>
                                                <a href="' . $caminho . '" download class="text-gray-500 text-xs hover:underline">Baixar</a>
                                            </div>';
                                        } else if ($extensao === 'pptx' || $extensao === 'docx' || $extensao === 'xlsx' || $extensao === 'xlsm ' || $extensao === 'xltx' || $extensao === 'xls') {
                                            echo '
                                            <div class="border p-2 rounded shadow bg-white w-44">
                                                <div class="text-sm text-gray-600 truncate mb-2">' . $nome . '</div>
                                                <a href="' . $caminho . '" download class="text-gray-500 text-xs hover:underline">Baixar Arquivo</a>
                                            </div>';
                                        }
                                    }
                                    ?>
                                </div>

                                <!-- Preview PDF -->
                                <div id="pdf-preview" class="mt-6 border rounded p-4 hidden bg-white">
                                    <h5 class="text-sm font-semibold mb-2">Pré-visualização do PDF</h5>
                                    <div id="pdf-pages" class="space-y-4 max-h-[600px] overflow-y-auto border rounded p-2"></div>
                                </div>
                            </div>
                        <?php } ?>


                        <div>
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Histórico</h4>
                            <div class="space-y-4 max-h-[400px] overflow-y-auto" id="ticketHistory">
                                <?php foreach ($historicos as $historico) : ?>
                                    <!-- Historico de pessoas atribuidas para o chamado -->
                                    <?php if ($historico['origem'] == 'usuario_chamado') : ?>
                                        <div class="flex items-start mt-4">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                                    <i class="fas fa-user-plus"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">Chamado atribuído</div>
                                                <div class="text-sm text-gray-500">Atribuído para <?= $historico['ds_nome_usuario_designado']; ?></div>
                                                <div class="mt-1 text-sm text-gray-500"><?= $geral->formataData($historico['dt_evento']) ?>, <?= $geral->formataHora($historico['dt_evento']) ?></div>
                                            </div>
                                        </div>
                                    <?php elseif ($historico['origem'] == 'status_chamado') :
                                        if ($historico['st_status'] == 0) {
                                            $st_status_historico_icone = '<div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                                <i class="fas fa-envelope-open"></i>
                                            </div>';
                                            $st_status_historico = 'Chamado em Aberto';
                                        } else if ($historico['st_status'] == 1) {
                                            $st_status_historico_icone = '<div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                                                <i class="fas fa-spinner"></i>
                                            </div>';
                                            $st_status_historico = 'Chamado em Andamento';
                                        } else {
                                            $st_status_historico_icone = '<div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                                                <i class="fas fa-check"></i>
                                            </div>';
                                            $st_status_historico = 'Chamado Resolvido';
                                        }
                                    ?>
                                        <!-- Historico do Status do chamado -->
                                        <div class="flex items-start mt-4">
                                            <div class="flex-shrink-0 mr-3">
                                                <?= $st_status_historico_icone; ?>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">Status alterado</div>
                                                <div class="text-sm text-gray-500"><?= $st_status_historico; ?></div>
                                                <div class="mt-1 text-sm text-gray-500"><?= $geral->formataData($historico['dt_evento']) ?>, <?= $geral->formataHora($historico['dt_evento']) ?></div>
                                            </div>
                                        </div>
                                    <?php elseif ($historico['origem'] == 'edicao_chamado') : ?>
                                        <!-- Historico do Status do chamado -->
                                        <div class="flex items-start mt-4">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 rounded-full bg-purple-100 flex items-center justify-center text-purple-600">
                                                    <i class="fas fa-edit"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900">Chamado Alterado</div>
                                                <div class="text-sm text-gray-500">Editado por <?= $historico['ds_nome_usuario']; ?></div>
                                                <div class="mt-1 text-sm text-gray-500"><?= $geral->formataData($historico['dt_evento']) ?>, <?= $geral->formataHora($historico['dt_evento']) ?></div>
                                            </div>
                                        </div>
                                    <?php else : ?>
                                        <!-- Mensagens -->
                                        <div class="flex items-start mt-4">
                                            <div class="flex-shrink-0 mr-3">
                                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-600">
                                                    <?= strtoupper(substr($historico['ds_nome_usuario'], 0, 1)) ?>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-gray-900"><?= $historico['ds_nome_usuario']; ?></div>
                                                <div class="text-sm text-gray-700 mt-1"><?= $historico['ds_comentario']; ?></div>
                                                <div class="mt-1 text-sm text-gray-500"><?= $geral->formataData($historico['dt_evento']) ?>, <?= $geral->formataHora($historico['dt_evento']) ?></div>
                                            </div>
                                        </div>
                                <?php endif;
                                endforeach; ?>
                                <div class="flex items-start">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                                            <i class="fas fa-ticket-alt"></i>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-medium text-gray-900"><?= $dados['criador'] ?></div>
                                        <div class="text-sm text-gray-500">Criou o chamado</div>
                                        <div class="mt-1 text-sm text-gray-500"><?= $geral->formataData($dados['dt_data_chamado']) ?>, <?= $geral->formataHora($dados['dt_data_chamado']) ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if ($dados['st_status'] != 9): ?>
                        <div class="p-6 border-t border-gray-200">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Adicionar Comentário</h4>
                            <form id="addCommentForm">
                                <input hidden id="id_usuario_comentario" name="id_usuario_comentario" value="<?php echo $id_usuario; ?>">
                                <input hidden id="id_chamado_comentario" name="id_chamado_comentario" value="<?php echo $id_chamado; ?>">
                                <textarea id="commentText" rows="3" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border mb-3" placeholder="Digite seu comentário..."></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="gradient-bg text-white px-4 py-2 rounded-md flex items-center">
                                        <i class="fas fa-comment mr-2"></i> Enviar Comentário
                                    </button>
                                </div>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</body>

<!-- JS para LightGallery -->
<script>
    lightGallery(document.getElementById('lightgallery'), {
        plugins: [lgZoom, lgThumbnail],
        speed: 400,
    });
</script>

<!-- JS para PDF.js -->
<script>
    function previewPDF(url) {
        const container = document.getElementById('pdf-preview');
        const pagesContainer = document.getElementById('pdf-pages');
        pagesContainer.innerHTML = ''; // Limpa visualizações anteriores
        container.classList.remove('hidden');
        container.scrollIntoView({
            behavior: 'smooth'
        });

        pdfjsLib.getDocument(url).promise.then(pdf => {
            const totalPages = pdf.numPages;

            for (let pageNum = 1; pageNum <= totalPages; pageNum++) {
                pdf.getPage(pageNum).then(page => {
                    const scale = 1.2;
                    const viewport = page.getViewport({
                        scale
                    });

                    const canvas = document.createElement('canvas');
                    const context = canvas.getContext('2d');
                    canvas.height = viewport.height;
                    canvas.width = viewport.width;

                    page.render({
                        canvasContext: context,
                        viewport
                    });
                    pagesContainer.appendChild(canvas);
                });
            }
        }).catch(error => {
            alert("Erro ao carregar o PDF: " + error.message);
        });
    }
</script>

<script>
    document.getElementById('backFromDetailBtn').addEventListener('click', function() {
        window.location.href = '../Telas/Meus_chamados.php'
    });
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById('assignDropdownBtn');
        const dropdown = document.getElementById('assignDropdown');

        // só adiciona os eventos se os elementos existirem
        if (btn && dropdown) {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                dropdown.classList.toggle('hidden');
            });

            document.addEventListener('click', function(e) {
                if (!dropdown.contains(e.target) && !btn.contains(e.target)) {
                    dropdown.classList.add('hidden');
                }
            });
        }
    });

    function designarUsuario(id, nome) {
        Swal.fire({
            title: "Tem certeza?",
            text: "Quer designar esse chamado para o " + nome + "?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#15803D",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, designar!",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const dados = {
                    id_chamado: $('#id_chamado').val(),
                    id_usuario_adm: $('#id_usuario').val(),
                    id_usuario_desginado: id
                };
                $('#loader').show();
                $.ajax({
                    url: '../appChamado/atribuir_chamado.php',
                    method: 'POST',
                    data: dados,
                    success: function(response) {
                        $('#loader').hide();
                        // dropdown.classList.add('hidden');
                        swal.fire({
                            title: "Designado!",
                            text: response.message,
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Erro!",
                            text: "Ocorreu um erro ao designar o chamado.",
                            icon: "error"
                        });
                        console.error('Erro:', error);
                    }
                });
            }
        });
    }

    function assumirChamado() {
        Swal.fire({
            title: "Deseja assumir este chamado?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#15803D",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, assumir",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const id_usuario = $('#id_usuario').val();
                const dados = {
                    id_chamado: $('#id_chamado').val(),
                    id_usuario_adm: id_usuario,
                    id_usuario_desginado: id_usuario
                };
                $('#loader').show();
                $.ajax({
                    url: '../appChamado/atribuir_chamado.php',
                    method: 'POST',
                    data: dados,
                    success: function(response) {
                        $('#loader').hide();
                        swal.fire({
                            title: "Designado!",
                            text: 'Você assumiu o chamado com sucesso.',
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Erro!",
                            text: "Não foi possível assumir o chamado.",
                            icon: "error"
                        });
                        console.error('Erro:', error);
                    }
                });
            }
        });
    }

    function resolver_reabrir(id_usuario, id_chamado, st_status) {
        Swal.fire({
            title: "Deseja mudar o status deste chamado?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#15803D",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const dados = {
                    id_chamado: id_chamado,
                    id_usuario: id_usuario,
                    st_status: st_status
                };
                $('#loader').show();
                $.ajax({
                    url: '../appChamado/mudar_status.php',
                    method: 'POST',
                    data: dados,
                    success: function(response) {
                        $('#loader').hide();
                        swal.fire({
                            title: "Estado do Chamado Alterado!",
                            text: response.message,
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            title: "Erro!",
                            text: "Não foi possível assumir o chamado.",
                            icon: "error"
                        });
                        console.error('Erro:', error);
                    }
                });
            }
        });
    }

    $('#addCommentForm').on('submit', function(e) {
        e.preventDefault();

        const comentario = $('#commentText').val().trim();

        if (comentario === '') {
            Swal.fire({
                icon: 'warning',
                title: 'Comentário vazio',
                text: 'Digite algo antes de enviar.',
            });
            return;
        }

        Swal.fire({
            title: "Enviar comentário?",
            text: "Deseja realmente adicionar este comentário?",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#15803D",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sim, enviar!",
            cancelButtonText: "Cancelar"
        }).then((result) => {
            if (result.isConfirmed) {
                const dados = {
                    id_usuario: $('#id_usuario_comentario').val(),
                    id_chamado: $('#id_chamado_comentario').val(),
                    ds_comentario: comentario
                };
                $('#loader').show();

                $.ajax({
                    url: '../appChamado/gravar_comentario.php',
                    method: 'POST',
                    data: dados,
                    success: function(response) {
                        $('#loader').hide();
                        swal.fire({
                            title: "Comentário enviado!",
                            text: response.message,
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire("Erro!", res.error || "Erro ao salvar o comentário.", "error");
                        console.error('Erro:', error);
                    }
                });
            }
        });
    });
</script>