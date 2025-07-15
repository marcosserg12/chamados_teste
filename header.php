<header class="bg-white shadow-sm">
    <div class="max-w-8xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-lg font-semibold text-gray-900" id="pageTitle"></h1>
        <div class="flex items-center space-x-4">
            <div class="relative group">
                <button onclick="abrirModalImagem()" id="tooltipButton" class="text-gray-600 hover:text-gray-900 text-xl font-bold">?</button>

                <!-- Tooltip com imagem pequena -->
                <div class="absolute right-0 mt-2 w-[400px] p-4 bg-white border border-gray-300 rounded-lg shadow-xl z-50 hidden group-hover:block group-focus:block">
                    <img src="../assets/media/regra_chamados.jpg" alt="Como funciona o tempo de resposta" class="w-full rounded cursor-pointer"  />
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