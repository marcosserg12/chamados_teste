<header class="bg-white shadow-sm">
    <div class="max-w-8xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-lg font-semibold text-gray-900" id="pageTitle"></h1>
        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button onclick="abrirModalImagem()" id="tooltipButton" class="text-gray-600 hover:text-gray-900 text-xl font-bold">?</button>

                <!-- Tooltip com imagem pequena -->
                <div class="absolute right-0 mt-2 w-[400px] p-4 bg-white border border-gray-300 rounded-lg shadow-xl z-50 hidden group-hover:block group-focus:block">
                    <img src="../assets/media/regra_chamados.jpg" alt="Como funciona o tempo de resposta" class="w-full rounded cursor-pointer" />
                    <p class="text-sm text-gray-600 mt-2">Clique no <b>?</b> para ver ampliada.</p>
                </div>
            </div>



            <span class="text-sm text-gray-600" id="userEmailDisplay"><?= Security::getUser()['ds_nome'] ?></span>
            <div class="relative">
                <button id="userMenuButton" class="flex items-center space-x-2 focus:outline-none">
                    <div class="w-8 h-8 rounded-full gradient-bg flex items-center justify-center text-white font-bold">
                        <span id="userInitials"><?= strtoupper(substr(Security::getUser()['ds_nome'], 0, 1)) ?></span>
                    </div>
                </button>
            </div>
        </div>
    </div>
</header>

<!-- Modal oculto -->
<div id="modalImagem" class="fixed inset-0 bg-black bg-opacity-60 z-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded-lg shadow-2xl w-[90%] max-w-[450px] relative">
        <button onclick="fecharModalImagem()" class="absolute top-2 right-4 text-gray-600 text-2xl font-bold hover:text-gray-800">&times;</button>
        <img src="../assets/media/regra_chamados.jpg" alt="Imagem ampliada" class="w-full rounded border" />
    </div>
</div>

<!-- Overlay de fundo que cobre a tela -->
<div id="loader" class="fixed inset-0 bg-white/80 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
        <div class="flex items-center justify-center h-16 gap-1 mb-4">
            <div class="w-3 h-10 bg-indigo-500 wave-bar"></div>
            <div class="w-3 h-10 bg-indigo-500 wave-bar"></div>
            <div class="w-3 h-10 bg-indigo-500 wave-bar"></div>
            <div class="w-3 h-10 bg-indigo-500 wave-bar"></div>
            <div class="w-3 h-10 bg-indigo-500 wave-bar"></div>
        </div>
        <p class="text-gray-700 text-lg font-medium">Carregando...</p>
    </div>
</div>

<script>
    const pageTitle = document.getElementById('pageTitle');
    const path = window.location.pathname;

    function abrirModalImagem() {
        document.getElementById('modalImagem').classList.remove('hidden');
    }

    function fecharModalImagem() {
        document.getElementById('modalImagem').classList.add('hidden');
    }

    // Fecha o modal clicando fora da imagem
    document.getElementById('modalImagem').addEventListener('click', function(e) {
        if (e.target === this) {
            fecharModalImagem();
        }
    });


    if (path.includes('/principal')) {
        pageTitle.textContent = 'Dashboard';
    } else {
        pageTitle.textContent = '';
    }
</script>

<style>
    /* === SELECT PRINCIPAL COM ESTILO DE INPUT TAILWIND === */
    .select2-container .select2-selection--single {
        background-color: #fff;
        border: 1px solid #d1d5db;
        /* border-gray-300 */
        border-radius: 0.375rem;
        /* rounded-md */
        padding: 0.5rem 0.75rem;
        /* px-3 py-2 */
        font-size: 0.875rem;
        /* text-sm */
        color: #111827;
        /* text-gray-900 */
        height: auto;
        min-height: 2.5rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        /* shadow-sm */
        transition: border 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    /* === COMPORTAMENTO DE FOCO (como input padrão do Tailwind) === */
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default .select2-selection--single:focus-visible,
    .select2-container--default .select2-selection--single:focus-within {
        border-color: #3b82f6 !important;
        /* border-blue-500 */
        outline: none !important;
        box-shadow: 0 0 0 3px rgba(59, 131, 246, 0.4);
        /* ring-blue-500 */
    }

    /* === TEXTO DENTRO DO SELECT === */
    .select2-selection__rendered {
        line-height: 1.5rem;
        color: #111827;
        /* text-gray-900 */
    }

    /* === ÍCONE SETA === */
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 100%;
        right: 0.75rem;
    }

    /* === DROPDOWN ESTILO TAILWIND === */
    .select2-container .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        background-color: #fff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
        font-size: 0.875rem;
    }

    /* === OPÇÕES DA LISTA === */
    .select2-container .select2-results__option {
        padding: 0.5rem 0.75rem;
        color: #374151;
        /* text-gray-700 */
        cursor: pointer;
    }

    /* Hover no item */
    .select2-container .select2-results__option--highlighted {
        background-color: rgba(88, 96, 255, 0.55) !important;
        /* bg-gray-200 */
        color: #111827 !important;
        /* text-gray-900 */
    }

    /* Item selecionado */
    .select2-container--default .select2-results__option--selected {
        background-color: rgba(88, 96, 255, 0.30) !important;
        /* bg-gray-100 */
        color: #111827 !important;
        /* text-gray-900 */
    }
</style>
<script>
    function validarFormulario(formSelector) {
        const $form = $(formSelector);
        let isValid = true;
        const validatedRadios = [];

        $form.find('.error-message').remove(); // limpa mensagens anteriores
        $form.find('.border-red-500').removeClass('border-red-500');

        $form.find('[required]').each(function() {
            const name = $(this).attr('name');
            const type = $(this).attr('type');

            if (type === 'radio') {
                if (validatedRadios.includes(name)) return;
                validatedRadios.push(name);

                if ($(`input[name="${name}"]:checked`).length === 0) {
                    isValid = false;
                    const group = $(`input[name="${name}"]`).last().parent().parent();
                    group.after('<p class="text-red-500 text-sm mt-1 error-message">Este campo é obrigatório.</p>');
                }
            } else {
                const value = $(this).val()?.trim();
                if (!value) {
                    isValid = false;
                    $(this).addClass('border-red-500');
                    const isSelect2 = $(this).hasClass('select2');
                    const errorMsg = '<p class="text-red-500 text-sm mt-1 error-message">Este campo é obrigatório.</p>';

                    if (isSelect2) {
                        $(this).next('.select2').after(errorMsg);
                    } else {
                        $(this).after(errorMsg);
                    }
                }
            }
        });

        return isValid;
    }
</script>