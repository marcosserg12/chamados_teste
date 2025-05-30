<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
session_start();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral = new Geral();
$dados = $chamados->meusChamados(Security::getUser()['id_perfil'], Security::getUser()['id_usuario']);
?>

<?php include   '../scripts.php'; ?>


<body class="bg-gray-100 font-sans">

    <?php include   '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include   '../header.php'; ?>
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div id="myTicketsView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Meus Chamados</h2>
                    <?php if(Security::getUser()['id_perfil'] == 2) : ?>
                    <a href="../Telas/Novo_chamado.php" id="createNewTicketBtn" class="gradient-bg text-white px-4 py-2 rounded-md flex items-center">
                        <i class="fas fa-plus mr-2"></i> Novo Chamado
                    </a>
                    <?php endif ?>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex flex-wrap items-center justify-between">
                            <div class="flex items-center space-x-4 mb-4 md:mb-0">
                                <div class="relative">
                                    <select id="ticketFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                        <option value="">Todos</option>
                                        <option value="1">Em Andamento</option>
                                        <option value="9">Resolvidos</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                        <i class="fas fa-chevron-down"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="relative w-full md:w-auto">
                                <input type="text" id="ticketSearch" placeholder="Buscar chamados..." class="bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded w-full focus:outline-none focus:bg-white focus:border-gray-500">
                                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200" id="myTicketsList">
                        <div id="noDataMessage" class="p-8 text-center text-gray-500 hidden">
                            <i class="fas fa-ticket-alt text-4xl mb-4 text-gray-300"></i>
                            <p>Nenhum chamado encontrado</p>
                        </div>

                        <!-- Tickets will be loaded here -->
                        <div class="overflow-x-auto" style="padding: 10px;">
                            <table id="tabelaChamados" class="min-w-[700px]">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="recentTicketsTable" class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($dados as $dado): ?>
                                        <?php
                                        $status = $dado['st_status'];
                                        if ($status == 0) {
                                            $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Aberto</span>';
                                        } else if ($status == 1) {
                                            $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Em Andamento</span>';
                                        } else {
                                            $st_status = '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Resolvido</span>';
                                            $status = 9; // Reescreve para 9 (Resolvido), para combinar com o filtro
                                        }
                                        ?>
                                        <tr data-status="<?= $status ?>">
                                            <td class="px-6 py-4 text-sm text-gray-500">#<?= $dado['id_chamado'] ?></td>
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $dado['ds_titulo'] ?></td>
                                            <td class="px-6 py-4"><?= $st_status ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"><?= $geral->formataData($dado['dt_data_chamado']) ?></td>
                                            <td class="px-6 py-4 text-sm text-gray-500"><?= $geral->formataHora($dado['dt_data_chamado']) ?></td>
                                            <td class="px-6 py-4 text-sm font-medium">
                                                <a href="../Telas/Detalhe_chamado.php?id_chamado=<?= $dado['id_chamado'] ?>" class="text-blue-600 hover:text-blue-900">Ver</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        $(document).ready(function() {
            const table = $('#tabelaChamados').DataTable({
                ordering: true,
                responsive: true, // permite adaptação automática
                dom: `
                <"w-full"t>
                <"flex flex-col md:flex-row md:justify-between md:items-center mt-4"
                <"text-sm text-gray-600"i>
                <"mt-2 md:mt-0"p>
                >
                `,

                lengthMenu: [10, 15, 20, 30, 50, 80, 100],
                pageLength: 10,

                language: {
                    lengthMenu: '',
                    search: '',
                    info: 'Mostrando _START_ até _END_ de _TOTAL_',
                    // zeroRecords: 'Não existem Chamados cadastrados',
                    // emptyTable: 'Não existem Chamados cadastrados',
                    zeroRecords: '',
                    emptyTable: '',
                    infoEmpty: 'Mostrando 0 até 0 de 0',
                    paginate: {
                        first: 'Primeira',
                        last: 'Última',
                        next: '>',
                        previous: '<'
                    }
                }
            });

            function toggleNoDataMessage() {
                const hasData = table.rows({
                    filter: 'applied'
                }).data().length > 0;
                if (hasData) {
                    $('#noDataMessage').addClass('hidden');
                    $('#tabelaChamados').removeClass('hidden');
                } else {
                    $('#noDataMessage').removeClass('hidden');
                    $('#tabelaChamados').addClass('hidden');
                }
            }

            // Verifica na carga inicial
            toggleNoDataMessage();

            // Verifica após qualquer evento de busca, paginação ou redraw
            table.on('draw', function() {
                toggleNoDataMessage();
            });

            // Filtro de status
            $('#ticketFilter').on('change', function() {
                const status = $(this).val();
                const texto = statusTexto(status);
                table.column(2).search(texto, true, false).draw();
            });

            // Busca personalizada (campo fora do DataTables)
            $('#ticketSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Tradução de status
            function statusTexto(code) {
                return {
                    '0': 'Aberto',
                    '1': 'Em Andamento',
                    '9': 'Resolvido'
                } [code] || '';
            }
        });
    </script>