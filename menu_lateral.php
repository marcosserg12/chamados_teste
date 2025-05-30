<div class="md:hidden fixed top-4 left-4 z-40">
    <button id="menuToggle" class="p-2 rounded-md bg-blue-600 text-white">
        <i class="fas fa-bars"></i>
    </button>
</div>
<div id="sidebar" class="sidebar bg-white w-64 h-screen fixed shadow-lg">
    <div class="p-4 border-b border-gray-200">
        <h1 class="text-xl font-bold text-gray-800">Sistema de Chamados</h1>
        <p class="text-sm text-gray-600" id="userRoleDisplay"><?= Security::getUser()['id_perfil'] == 1 ? 'Administrador' : 'Usuário' ?></p>
    </div>
    <nav class="p-4">
        <ul>
            <li class="mb-2">
                <a href="../principal.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-blue-50" id="dashboardLink">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="mb-2">
                <a href="../Telas/Meus_chamados.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-blue-50" id="ticketsLink">
                    <i class="fas fa-ticket-alt mr-3"></i>
                    <span>Meus Chamados</span>
                </a>
            </li>
            <?php if(Security::getUser()['id_perfil'] != 1) : ?>
            <li class="mb-2">
                <a href="../Telas/Novo_chamado.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-blue-50" id="newTicketLink">
                    <i class="fas fa-plus-circle mr-3"></i>
                    <span>Novo Chamado</span>
                </a>
            </li>
            <?php endif; ?>
            <?php if(Security::getUser()['id_perfil'] == 1) : ?>
            <li class="mb-2 " id="adminUsersLink">
                <a href="../Telas/Gerenciar_usuario.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-blue-50">
                    <i class="fas fa-users-cog mr-3"></i>
                    <span>Gerenciar Usuários</span>
                </a>
            </li>
            <li class="mb-2 " id="adminTicketsLink">
                <a href="../Telas/Todos_chamados.php" class="flex items-center p-2 text-gray-700 rounded-lg hover:bg-blue-50">
                    <i class="fas fa-clipboard-list mr-3"></i>
                    <span>Todos os Chamados</span>
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </nav>
    <div class="absolute bottom-0 w-full p-4 border-t border-gray-200">
        <a href="../appUsuario/logout.php" id="logoutBtn" class="flex items-center p-2 text-red-600 rounded-lg hover:bg-red-50 w-full">
            <i class="fas fa-sign-out-alt mr-3"></i>
            <span>Sair</span>
        </a>
    </div>
</div>
<style>
    /* Oculta o sidebar por padrão em telas menores */
    #sidebar {
        transition: transform 0.3s ease;
    }

    @media (max-width: 767px) {
        #sidebar {
            transform: translateX(-100%);
        }

        #sidebar.open {
            transform: translateX(0);
        }
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', () => {
        const menuToggle = document.getElementById('menuToggle');
        const sidebar = document.getElementById('sidebar');

        // Alterna o menu
        menuToggle.addEventListener('click', (e) => {
            e.stopPropagation(); // evita fechar ao clicar no botão
            sidebar.classList.toggle('open');
        });

        // Fecha ao clicar fora (somente mobile)
        document.addEventListener('click', (e) => {
            const isMobile = window.innerWidth < 768;

            if (
                isMobile &&
                sidebar.classList.contains('open') &&
                !sidebar.contains(e.target) &&
                !menuToggle.contains(e.target)
            ) {
                sidebar.classList.remove('open');
            }
        });

        // Evita que clique dentro do menu feche ele
        sidebar.addEventListener('click', (e) => {
            e.stopPropagation();
        });
    });
</script>