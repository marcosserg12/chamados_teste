<?php include __DIR__ . '/scripts.php'; ?>
<!-- 0 - Chamado em Aberto -->
<!-- 1 - Chamado em Andamento -->
<!-- 9 - Chamado Finalizado -->
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require __DIR__ . '../vendor/autoload.php';
session_start();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral = new Geral();
$dados = $chamados->listaChamadosRecentes(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
$totalchamados = $chamados->totalChamados(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);

?>

<body class="bg-gray-100 font-sans">

    <?php include __DIR__ . '/menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include __DIR__ . '/header.php'; ?>
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">

            <div id="dashboardView">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                                <i class="fas fa-ticket-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Chamados Abertos</p>
                                <p class="text-2xl font-semibold text-gray-800" id="openTicketsCount"><?= $totalchamados['aberto']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100 text-yellow-600 mr-4">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Em Andamento</p>
                                <p class="text-2xl font-semibold text-gray-800" id="inProgressTicketsCount"><?= $totalchamados['andamento']; ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white shadow rounded-lg p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-500">Resolvidos</p>
                                <p class="text-2xl font-semibold text-gray-800" id="resolvedTicketsCount"><?= $totalchamados['resolvidos']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white shadow rounded-lg p-6 mb-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">Últimos Chamados</h2>
                        <?php if (Security::getUser()['id_perfil'] == 1) : ?>
                            <a href="../Telas/Todos_chamados.php" class="text-sm text-blue-600 hover:underline" id="viewAllTickets">Ver todos</a>
                        <?php endif ?>
                    </div>
                    <div id="noDataMessage" class="p-8 text-center text-gray-500 hidden">
                        <i class="fas fa-ticket-alt text-4xl mb-4 text-gray-300"></i>
                        <p>Nenhum chamado encontrado</p>
                    </div>

                    <div class="overflow-x-auto">
                        <table id="tabelaSimples" class=" min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="recentTicketsTable">
                                <?php foreach ($dados as $dado) {
                                    if ($dado['st_status'] == 0) {
                                        $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">
                            Aberto
                                </span>';
                                    } else if ($dado['st_status'] == 1) {
                                        $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">
                                    Em Andamento
                                </span>';
                                    } else {
                                        $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">
                                    Resolvido
                                </span>';
                                    }
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">#<?= $dado['id_chamado'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= $dado['ds_titulo'] ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap"><?= $st_status ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $geral->formataData($dado['dt_data_chamado']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $geral->formataHora($dado['dt_data_chamado']) ?></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado']  ?>" class="text-blue-600 hover:text-blue-900 view-ticket" data-id="1">Ver</a>
                                        </td>
                                    </tr>
                                    <!-- Tickets will be loaded here -->

                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const tabela = document.getElementById('tabelaSimples');
            const tbody = document.getElementById('recentTicketsTable');
            const noDataMessage = document.getElementById('noDataMessage');

            const temChamados = tbody.querySelectorAll('tr').length > 0;

            if (temChamados) {
                tabela.classList.remove('hidden');
                noDataMessage.classList.add('hidden');
            } else {
                tabela.classList.add('hidden');
                noDataMessage.classList.remove('hidden');
            }
        });
    </script>