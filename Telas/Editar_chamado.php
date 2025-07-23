<?php
include '../scripts.php';
require '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$id_usuario = Security::getUser()['id_usuario'];
$id_chamado = $_GET['id_chamado'] ?? null;

if (!$id_chamado) {
    exit('Chamado não especificado.');
}

$chamados = new Chamados();
$usuario = new Usuario();

$dados = $chamados->mostrarChamado($id_chamado);
$lista_tipo_chamado = $chamados->lista_tipos_chamados();
$lista_empresas = $chamados->lista_empresas_usuario($dados['criador']);
$lista_localizacao = $chamados->lista_localizacao_usuario($dados['id_empresa'], $dados['criador']);
$lista_motivo = $chamados->lista_motivo($dados['id_tipo_chamado']);
$lista_motivo_associado = $chamados->lista_motivo_associado($dados['id_motivo_principal']);
$dados_arquivos = $chamados->mostrararquivosChamado($id_chamado);
?>


<body class="bg-gray-100 font-sans">

    <?php include '../menu_lateral.php'; ?>

    <div class="md:ml-64 min-h-screen">
        <?php include '../header.php'; ?>

        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="newTicketView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Editar Chamado</h2>
                    <button id="backToTicketsBtn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                    </button>
                </div>
                <div class="bg-white shadow rounded-lg p-6">

                    <form id="form_editar_chamado" enctype="multipart/form-data"  novalidate>
                        <input type="hidden" name="id_chamado" value="<?= $id_chamado ?>">
                        <input type="hidden" name="id_usuario" value="<?= $id_usuario ?>">

                        <div class="mb-6">
                            <label for="ticketTitle" class="block text-sm font-medium text-gray-700 mb-2">Título <span class="text-red-500">*</span></label>
                            <input type="text" id="ticketTitle" name="ds_titulo" value="<?= htmlspecialchars($dados['ds_titulo']) ?>" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Select Empresa -->
                            <div>
                                <label for="empresa" class="block text-sm font-medium text-gray-700 mb-2">Empresa <span class="text-red-500">*</span>
                                </label>
                                <select id="empresa" name="id_empresa" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                    <?php foreach ($lista_empresas as $empresa) :
                                        if ($empresa['id_empresa']  ==  $dados['id_empresa']) { ?>
                                            <option value="<?= $empresa['id_empresa'] ?>" selected><?= $empresa['ds_empresa'] ?></option>
                                        <?php } else { ?>
                                            <option value="<?= $empresa['id_empresa'] ?>"><?= $empresa['ds_empresa'] ?></option>
                                    <?php }
                                    endforeach; ?>
                                </select>
                            </div>

                            <!-- Select Localização -->
                            <div>
                                <label for="localizacao" class="block text-sm font-medium text-gray-700 mb-2">Localização <span class="text-red-500">*</span>
                                </label>
                                <select id="localizacao" name="id_localizacao" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                    <option value="">Selecione a localização</option>
                                    <?php foreach ($lista_localizacao as $localizacao) :
                                        if ($localizacao['id_localizacao']  ==  $dados['id_localizacao']) { ?>
                                            <option value="<?= $localizacao['id_localizacao'] ?>" selected><?= $localizacao['ds_localizacao'] ?></option>
                                        <?php } else { ?>
                                            <option value="<?= $localizacao['id_localizacao'] ?>"><?= $localizacao['ds_localizacao'] ?></option>
                                    <?php }
                                    endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Categoria do Chamado</label>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label for="tipo_chamado" class="block text-sm font-medium text-gray-700 mb-2">Tipo de Chamado <span class="text-red-500">*</span>
                                    </label>
                                    <select id="tipo_chamado" name="id_tipo_chamado" class="select2" required>
                                        <option value="">Selecione o tipo</option>
                                        <?php foreach ($lista_tipo_chamado as $tipo) :
                                            if ($tipo['id_tipo_chamado'] == $dados['id_tipo_chamado']) { ?>
                                                <option value="<?= $tipo['id_tipo_chamado'] ?>" selected><?= $tipo['ds_tipo_chamado'] ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $tipo['id_tipo_chamado'] ?>"><?= $tipo['ds_tipo_chamado'] ?></option>
                                        <?php }
                                        endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="motivo_principal" class="block text-sm font-medium text-gray-700 mb-2">Motivo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="motivo_principal" name="id_motivo_principal" class="select2" required>
                                        <option value="">Selecione o motivo</option>
                                        <?php foreach ($lista_motivo as $motivo_principal) :
                                            if ($motivo_principal['id_motivo_principal'] == $dados['id_motivo_principal']) { ?>
                                                <option value="<?= $motivo_principal['id_motivo_principal'] ?>" selected><?= $motivo_principal['ds_descricao'] ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $motivo_principal['id_motivo_principal'] ?>"><?= $motivo_principal['ds_descricao'] ?></option>
                                        <?php }
                                        endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="motivo_associado" class="block text-sm font-medium text-gray-700 mb-2">Detalhamento <span class="text-red-500">*</span>
                                    </label>
                                    <select id="motivo_associado" name="id_motivo_associado" class="select2" required>
                                        <option value="">Selecione o detalhe</option>
                                        <?php foreach ($lista_motivo_associado as $motivo_associado) :
                                            if ($motivo_associado['id_motivo_associado'] == $dados['id_motivo_associado']) { ?>
                                                <option value="<?= $motivo_associado['id_motivo_associado'] ?>" selected><?= $motivo_associado['ds_descricao_motivo'] ?></option>
                                            <?php } else { ?>
                                                <option value="<?= $motivo_associado['id_motivo_associado'] ?>"><?= $motivo_associado['ds_descricao_motivo'] ?></option>
                                        <?php }
                                        endforeach; ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="mb-4" id="div_st_grau" <?php echo $dados['id_motivo_associado'] == '6' && $dados['id_tipo_chamado'] == '2' ? '' : 'style="display: none;"' ?>>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo <span class="text-red-500">*</span></label>
                            <div class="flex space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" id="st_grau_1" <?php echo $dados['st_grau'] == '1' ? 'checked' : '' ?> name="st_grau" value="1" class="form-radio text-blue-600">
                                    <span class="ml-2 text-gray-700">Melhoria</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" id="st_grau_2" <?php echo $dados['st_grau'] == '2' ? 'checked' : '' ?> name="st_grau" value="2" class="form-radio text-blue-600">
                                    <span class="ml-2 text-gray-700">Problema</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6" id="div_patrimonio" <?php echo $dados['id_tipo_chamado'] == '1' ? '' : 'style="display: none;"' ?>>
                            <label for="ticketTitle" class="block text-sm font-medium text-gray-700 mb-2">
                                Patrimônio
                                <span class="ml-1 relative group cursor-pointer">
                                    <i class="fas fa-info-circle text-blue-500"></i>
                                    <div class="absolute z-10 hidden group-hover:block w-64 bg-white border border-gray-300 shadow-lg rounded-md p-4 mb-2 bottom-full left-1/2 -translate-x-1/2">
                                        <img src="../assets/media/patrimonio.jpg" alt="Etiqueta de patrimônio" class="mb-2 rounded w-full h-auto border border-gray-200">
                                        <p class="text-sm text-gray-700">
                                            Encontre a etiqueta colada no item (como na imagem) e informe o número exatamente como aparece.
                                        </p>
                                    </div>
                                </span>
                            </label>

                            <input type="text" id="ticketTitle" name="ds_patrimonio"
                                class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border"
                                placeholder="Digite o número do patrimônio" value="<?= htmlspecialchars($dados['ds_patrimonio'] ?? '') ?>">
                        </div>

                        <div class="mb-6">
                            <label for="ticketDescription" class="block text-sm font-medium text-gray-700 mb-2">Descrição <span class="text-red-500">*</span></label>
                            <textarea id="ticketDescription" name="ds_descricao" rows="5" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" required><?= htmlspecialchars($dados['ds_descricao']) ?></textarea>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Anexos</label>

                            <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                                <div class="space-y-1 text-center">
                                    <div class="flex text-sm text-gray-600">
                                        <label for="file-upload" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                            <span>Enviar arquivos</span>
                                            <input id="file-upload" name="arquivo[]" type="file" class="sr-only" multiple>
                                        </label>
                                        <p class="pl-1">ou arraste e solte</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, PDF até 10MB</p>
                                </div>
                            </div>

                            <div id="filePreviews" class="mt-4 flex flex-wrap gap-4">
                                <!-- Arquivos existentes -->
                                <?php if (!empty($dados_arquivos) && is_array($dados_arquivos)): ?>
                                    <?php foreach ($dados_arquivos as $arquivo): ?>
                                        <?php if (!empty($arquivo['ds_caminho_arquivo'])): ?>
                                            <div class="relative w-32 h-32 rounded-md overflow-hidden shadow-md bg-gray-100 flex items-center justify-center" data-id="<?= $arquivo['id_arquivo'] ?>">
                                                <button type="button" onclick="removerArquivoExistente(<?= $arquivo['id_arquivo'] ?>)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs" title="Remover">
                                                    &times;
                                                </button>

                                                <?php
                                                $ext = pathinfo($arquivo['ds_caminho_arquivo'], PATHINFO_EXTENSION);
                                                $isImage = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif']);
                                                $caminho = "../uploads/" . $arquivo['ds_caminho_arquivo'];
                                                ?>

                                                <?php if ($isImage): ?>
                                                    <img src="<?= $caminho ?>" class="object-cover w-full h-full" alt="Anexo">
                                                <?php else: ?>
                                                    <div class="text-gray-500 text-center p-2 text-xs">
                                                        <i class="fas fa-file-alt fa-2x mb-1"></i>
                                                        <p class="break-words text-xs"><?= $arquivo['ds_caminho_arquivo'] ?></p>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>



                        <div class="flex justify-end">
                            <button type="submit" class="gradient-bg text-white px-6 py-2 rounded-md flex items-center">
                                <i class="fas fa-save mr-2"></i> Atualizar Chamado
                            </button>
                        </div>
                    </form>
                </div>
        </main>
    </div>
</body>

<script>
    let arquivosNovos = []; // Arquivos adicionados manualmente
    const fileInput = document.getElementById('file-upload');
    const filePreviews = document.getElementById('filePreviews');

    document.getElementById('backToTicketsBtn').addEventListener('click', function() {
        const idChamado = $('input[name="id_chamado"]').val();
        window.location.href = 'Detalhe_chamado.php?id_chamado=' + idChamado;
    });


    fileInput.addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        files.forEach(file => {
            arquivosNovos.push(file);
            mostrarPreviewNovo(file);
        });
        fileInput.value = ''; // limpa para permitir reanexar o mesmo
    });

    function mostrarPreviewNovo(file) {
        const reader = new FileReader();
        const container = document.createElement('div');
        container.className = 'relative w-32 h-32 m-2 rounded-md overflow-hidden shadow-md bg-gray-100 flex items-center justify-center';

        const removeBtn = document.createElement('button');
        removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
        removeBtn.innerHTML = '&times;';
        removeBtn.onclick = () => {
            filePreviews.removeChild(container);
            arquivosNovos = arquivosNovos.filter(f => f !== file);
        };

        container.appendChild(removeBtn);

        reader.onload = (e) => {
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'object-cover w-full h-full';
                container.appendChild(img);
            } else {
                const icon = document.createElement('div');
                icon.className = 'text-gray-500 text-center p-2 text-xs';
                icon.innerHTML = `<i class="fas fa-file-alt fa-2x mb-1"></i><p>${file.name}</p>`;
                container.appendChild(icon);
            }
        };

        reader.readAsDataURL(file);
        filePreviews.appendChild(container);
    }

    function removerArquivoExistente(id_arquivo) {
        Swal.fire({
            title: 'Excluir arquivo?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33'
        }).then(result => {
            if (result.isConfirmed) {
                $.post('../appNovoChamado/excluir_arquivo.php', {
                    id_arquivo: id_arquivo
                }, function() {
                    $(`[data-id="${id_arquivo}"]`).remove();
                    Swal.fire('Removido!', '', 'success');
                }).fail(() => {
                    Swal.fire('Erro!', 'Não foi possível excluir o arquivo.', 'error');
                });
            }
        });
    }

    $('#form_editar_chamado').on('submit', function(e) {
        e.preventDefault();

        if (!validarFormulario(this)) return;

        const idChamado = $('input[name="id_chamado"]').val();

        const formData = new FormData(this);
        arquivosNovos.forEach(file => {
            formData.append('arquivo[]', file);
        });
        $('#loader').show();

        $.ajax({
            url: '../appNovoChamado/editar_chamado.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                $('#loader').hide();
                Swal.fire({
                    title: response.message ?? 'Chamado atualizado com sucesso!',
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'Detalhe_chamado.php?id_chamado=' + idChamado;
                });
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire('Erro!', 'Falha ao atualizar chamado.', 'error');
            }
        });
    });

    $(document).ready(function() {

        // Quando escolher o tipo de chamado, carrega os motivos principais
        $('#tipo_chamado').on('change', function() {
            const idTipo = $(this).val();

            if (idTipo === "1") {
                $('#div_patrimonio').show();

            } else {
                $('#div_patrimonio').hide();
            }

            $('#motivo_principal').html('<option value="">Carregando...</option>');
            $('#motivo_associado').html('<option value="">Selecione o detalhe</option>');

            if (idTipo) {
                $.ajax({
                    url: '../appNovoChamado/carrega_motivo.php',
                    data: {
                        id_tipo_chamado: idTipo
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Selecione o motivo</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id_motivo_principal}">${item.ds_descricao}</option>`;
                        });
                        $('#motivo_principal').html(options).trigger('change.select2');
                    },
                    error: function() {
                        alert('Erro ao carregar os motivos.');
                    }
                });
            }
        });

        // Quando escolher o motivo principal, carrega os associados
        $('#motivo_principal').on('change', function() {
            const idMotivo = $(this).val();
            const id_empresa = $('#empresa').val();

            if (idMotivo === '6') {
                $('#div_st_grau').show();
                $('input[name="st_st_grau"]').attr('required', true);
            } else {
                $('#div_st_grau').hide();
                $('input[name="st_st_grau"]').removeAttr('required');
            }
            $('#motivo_associado').html('<option value="">Carregando...</option>');

            if (idMotivo && idMotivo === '6') {
                $.ajax({
                    url: '../appNovoChamado/carrega_motivo_associado_empresa.php',
                    data: {
                        id_motivo_principal: idMotivo,
                        id_empresa: id_empresa
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Selecione o detalhe</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id_motivo_principal}">${item.ds_descricao_motivo}</option>`;
                        });
                        $('#motivo_associado').html(options).trigger('change.select2');
                    },
                    error: function() {
                        alert('Erro ao carregar os detalhes 2.');
                    }
                });
            } else if (idMotivo) {
                $.ajax({
                    url: '../appNovoChamado/carrega_motivo_associado.php',
                    data: {
                        id_motivo_principal: idMotivo
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Selecione o detalhe</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id_motivo_principal}">${item.ds_descricao_motivo}</option>`;
                        });
                        $('#motivo_associado').html(options).trigger('change.select2');
                    },
                    error: function() {
                        alert('Erro ao carregar os detalhes.');
                    }
                });
            }
        });

        $('#empresa').on('change', function() {
            const idEmpresa = $(this).val();
            const idMotivo = $('#motivo_principal').val();
            const id_usuario = $('#id_usuario').val();
            $('#localizacao').html('<option value="">Carregando...</option>');

            if (idMotivo && idMotivo === '6') {
                $.ajax({
                    url: '../appNovoChamado/carrega_motivo_associado_empresa.php',
                    data: {
                        id_motivo_principal: idMotivo,
                        id_empresa: idEmpresa
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        let options = '<option value="">Selecione o detalhe</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id_motivo_principal}">${item.ds_descricao_motivo}</option>`;
                        });
                        $('#motivo_associado').html(options).trigger('change.select2');
                    },
                    error: function() {
                        alert('Erro ao carregar os detalhes 2.');
                    }
                });
            }

            if (idEmpresa) {
                $.ajax({
                    url: '../appNovoChamado/carrega_localizacao_por_usuario.php',
                    data: {
                        id_empresa: idEmpresa,
                        id_usuario: id_usuario
                    },
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // let options = '<option value="">Selecione a localização</option>';
                        data.forEach(function(item) {
                            options = `<option value="${item.id_localizacao}">${item.ds_localizacao}</option>`;
                        });
                        $('#localizacao').html(options).trigger('change.select2');
                    },
                    error: function() {
                        alert('Erro ao carregar as localizações.');
                    }
                });
            } else {
                $('#localizacao').html('<option value="">Selecione a localização</option>').trigger('change.select2');
            }
        });

    });
</script>