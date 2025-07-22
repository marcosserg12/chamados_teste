<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require  '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
$chamado = new Chamados();
$id_usuario = Security::getUser()['id_usuario'];
$lista_tipo_chamado = $chamado->lista_tipos_chamados();
$lista_empresas = $chamado->lista_empresas_usuario($id_usuario);


?>


<body class="bg-gray-100 font-sans">

    <?php include   '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include   '../header.php'; ?>
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="newTicketView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Novo Chamado</h2>
                    <button id="backToTicketsBtn" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md flex items-center">
                        <i class="fas fa-arrow-left mr-2"></i> Voltar
                    </button>
                </div>
                <div class="bg-white shadow rounded-lg p-6">
                    <form id="form_novo_chamado" enctype="multipart/form-data" novalidate>
                        <input hidden id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">
                        <div class="mb-6">
                            <label for="ticketTitle" class="block text-sm font-medium text-gray-700 mb-2">Título <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="ticketTitle" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Descreva o problema resumidamente" name="ds_titulo" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <!-- Select Empresa -->
                            <div>
                                <label for="empresa" class="block text-sm font-medium text-gray-700 mb-2">Empresa <span class="text-red-500">*</span>
                                </label>
                                <select id="empresa" name="id_empresa" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                    <?php foreach ($lista_empresas as $empresa) : ?>
                                        <option value="<?= $empresa['id_empresa'] ?>"><?= $empresa['ds_empresa'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Select Localização -->
                            <div>
                                <label for="localizacao" class="block text-sm font-medium text-gray-700 mb-2">Localização <span class="text-red-500">*</span>
                                </label>
                                <select id="localizacao" name="id_localizacao" class="select2 w-full border border-gray-300 rounded-md p-2" required>
                                    <option value="">Selecione a localização</option>
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
                                        <?php foreach ($lista_tipo_chamado as $tipo) : ?>
                                            <option value="<?= $tipo['id_tipo_chamado'] ?>"><?= $tipo['ds_tipo_chamado'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label for="motivo_principal" class="block text-sm font-medium text-gray-700 mb-2">Motivo <span class="text-red-500">*</span>
                                    </label>
                                    <select id="motivo_principal" name="id_motivo_principal" class="select2" required>
                                        <option value="">Selecione o motivo</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="motivo_associado" class="block text-sm font-medium text-gray-700 mb-2">Detalhamento <span class="text-red-500">*</span>
                                    </label>
                                    <select id="motivo_associado" name="id_motivo_associado" class="select2" required>
                                        <option value="">Selecione o detalhe</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="mb-4" id="div_st_grau" style="display: none;">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Tipo <span class="text-red-500">*</span></label>
                            <div class="flex space-x-6">
                                <label class="inline-flex items-center">
                                    <input type="radio" id="st_grau_1" name="st_grau" value="1" class="form-radio text-blue-600">
                                    <span class="ml-2 text-gray-700">Melhoria</span>
                                </label>
                                <label class="inline-flex items-center">
                                    <input type="radio" id="st_grau_2" name="st_grau" value="2" class="form-radio text-blue-600">
                                    <span class="ml-2 text-gray-700">Problema</span>
                                </label>
                            </div>
                        </div>

                        <div class="mb-6" id="div_patrimonio" style="display: none;">
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
                                placeholder="Digite o número do patrimônio">
                        </div>



                        <div class="mb-6">
                            <label for="ticketDescription" class="block text-sm font-medium text-gray-700 mb-2">
                                Descrição <span class="text-red-500">*</span>
                            </label>
                            <textarea id="ticketDescription" rows="5" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Descreva o problema em detalhes..." required name="ds_descricao"></textarea>

                            <button type="button" id="improveTextBtn" class="mt-2 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Melhorar texto com IA
                            </button>
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
                                <!-- File previews will appear here -->
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="gradient-bg text-white px-6 py-2 rounded-md flex items-center">
                                <i class="fas fa-paper-plane mr-2"></i> Enviar Chamado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>

    <script>
        const input = document.getElementById('file-upload');
        const previewsContainer = document.getElementById('filePreviews');
        document.getElementById('backToTicketsBtn').addEventListener('click', function() {
            window.history.back();
        });

        // Limite de arquivos
        const maxFiles = 5;

        // Armazenar arquivos já selecionados
        let filesArray = [];

        input.addEventListener('change', (event) => {
            const newFiles = Array.from(event.target.files);

            // Verificar se ultrapassou o limite
            if (filesArray.length + newFiles.length > maxFiles) {
                alert(`Você só pode enviar até ${maxFiles} arquivos.`);
                return;
            }

            newFiles.forEach(file => {
                filesArray.push(file);
                createPreview(file);
            });

            // Limpar o input para poder reenviar o mesmo arquivo se quiser
            input.value = '';
        });

        function createPreview(file) {
            const reader = new FileReader();

            // Criar container do preview
            const preview = document.createElement('div');
            preview.className = 'relative w-32 h-32 m-2 rounded-md overflow-hidden shadow-md bg-gray-200 flex items-center justify-center';

            // Botão de remover
            const removeBtn = document.createElement('button');
            removeBtn.className = 'absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs';
            removeBtn.innerHTML = '&times;';
            removeBtn.onclick = () => {
                previewsContainer.removeChild(preview);
                filesArray = filesArray.filter(f => f !== file);
            };
            preview.appendChild(removeBtn);

            // Barra de progresso
            const progressBar = document.createElement('div');
            progressBar.className = 'absolute bottom-0 left-0 h-1 bg-blue-500';
            progressBar.style.width = '0%';
            preview.appendChild(progressBar);

            reader.onloadstart = () => {
                progressBar.style.width = '0%';
            };

            reader.onprogress = (e) => {
                if (e.lengthComputable) {
                    const percentLoaded = Math.round((e.loaded / e.total) * 100);
                    progressBar.style.width = percentLoaded + '%';
                }
            };

            reader.onload = (e) => {
                progressBar.style.width = '100%'; // Finaliza a barra

                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.alt = file.name;
                    img.className = 'object-cover w-full h-full';
                    preview.appendChild(img);
                } else {
                    const icon = document.createElement('div');
                    icon.className = 'text-gray-500 text-center p-2 text-xs';
                    icon.innerHTML = `
          <svg class="mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor" viewBox="0 0 16 16">
            <path d="M4.5 0A1.5 1.5 0 0 0 3 1.5v13A1.5 1.5 0 0 0 4.5 16h7a1.5 1.5 0 0 0 1.5-1.5V5.414a1.5 1.5 0 0 0-.44-1.06L10.646.44A1.5 1.5 0 0 0 9.586 0H4.5zM10 1.5v3a.5.5 0 0 0 .5.5h3l-3.5-3.5z"/>
          </svg>
          <p>${file.name}</p>
        `;
                    preview.appendChild(icon);
                }
            };

            reader.readAsDataURL(file);

            previewsContainer.appendChild(preview);
        }
        $('#form_novo_chamado').on('submit', function(e) {
            e.preventDefault();

            if (!validarFormulario(this)) return;

            var form = $('#form_novo_chamado')[0];
            var formData = new FormData(form);

            // Remover os arquivos antigos
            formData.delete('arquivo[]');

            // Adicionar os arquivos de filesArray
            filesArray.forEach((file) => {
                formData.append('arquivo[]', file);
            });

            $.ajax({
                url: '../appNovoChamado/gravar_chamado.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    swal.fire({
                        title: response.message,
                        icon: "success"
                    }).then((result) => {
                        location.reload();
                    });
                },
                error: function(xhr, status, error) {
                    console.error('Erro:', xhr.responseText);
                }
            });
        });


        $(document).ready(function() {
            var idEmpresa = $('#empresa').val();
            var id_usuario = $('#id_usuario').val();
            var idMotivo = $('#motivo_principal').val();
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
                        let options = '<option value="">Selecione a localização</option>';
                        data.forEach(function(item) {
                            options += `<option value="${item.id_localizacao}">${item.ds_localizacao}</option>`;
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
                            let options = '<option value="">Selecione a localização</option>';
                            data.forEach(function(item) {
                                options += `<option value="${item.id_localizacao}">${item.ds_localizacao}</option>`;
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

    <script>
            document.getElementById("improveTextBtn").addEventListener("click", async function() {
                const textarea = document.getElementById("ticketDescription");
                const originalText = textarea.value;

                if (!originalText.trim()) {
                    alert("Por favor, escreva uma descrição antes de melhorar.");
                    return;
                }

                // Exemplo de chamada para um endpoint backend que você controlaria
                const response = await fetch('.../api/melhorar_texto.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        texto: originalText
                    })
                });

                const data = await response.json();

                if (data?.texto_melhorado) {
                    textarea.value = data.texto_melhorado;
                } else {
                    alert("Ocorreu um erro ao tentar melhorar o texto.");
                }
            });

    </script>