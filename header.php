<header class="bg-white shadow-sm">
    <div class="max-w-8xl mx-auto py-4 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
        <h1 class="text-lg font-semibold text-gray-900" id="pageTitle"></h1>
        <div class="flex items-center space-x-4">
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
<script>
    const pageTitle = document.getElementById('pageTitle');
    const path = window.location.pathname;
    console.log(path);

    // if (path.includes('/principal')) {
    //     pageTitle.textContent = 'Dashboard';
    // } else if (path.includes('/Telas/Meus_chamados.php')) {
    //     pageTitle.textContent = 'Meus Chamados';
    // } else if (path.includes('/Telas/Novo_chamado.php')) {
    //     pageTitle.textContent = 'Novo Chamado';
    // } else if (path.includes('/Telas/Gerenciar_usuario.php')) {
    //     pageTitle.textContent = 'Gerenciar Usuários';
    // } else if (path.includes('/Telas/Todos_chamados.php')) {
    //     pageTitle.textContent = 'Todos os Chamados';
    // } else if (path.includes('/Telas/Detalhe_chamado.php')) {
    //     pageTitle.textContent = 'Detalhes do Chamado';
    // } else {
    //     pageTitle.textContent = 'Página';
    // }
    if (path.includes('/principal')) {
        pageTitle.textContent = 'Dashboard';
    } else {
        pageTitle.textContent = '';
    }
</script>