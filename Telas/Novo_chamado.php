<?php include   '../scripts.php'; ?>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require  '../vendor/autoload.php';
session_start();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}


$id_usuario = Security::getUser()['id_usuario']; ?>


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
                    <form id="form_novo_chamado" enctype="multipart/form-data">
                        <input hidden id="id_usuario" name="id_usuario" value="<?php echo $id_usuario; ?>">
                        <div class="mb-6">
                            <label for="ticketTitle" class="block text-sm font-medium text-gray-700 mb-2">Título</label>
                            <input type="text" id="ticketTitle" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Descreva o problema resumidamente" name="ds_titulo" required>
                        </div>
                        <div class="mb-6">
                            <label for="ticketDescription" class="block text-sm font-medium text-gray-700 mb-2">Descrição</label>
                            <textarea id="ticketDescription" rows="5" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md p-2 border" placeholder="Descreva o problema em detalhes..." required name="ds_descricao"></textarea>
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
            var form = $('#form_novo_chamado')[0];
            var formData = new FormData(form);

            // Remover os arquivos antigos
            formData.delete('arquivo[]');

            // Adicionar os arquivos de filesArray
            filesArray.forEach((file) => {
                formData.append('arquivo[]', file);
            });

            $.ajax({
                url: '../appChamado/gravar_chamado.php',
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
    </script>