<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require '../vendor/autoload.php';
session_start();

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
if (Security::getUser()['id_perfil'] != 1) {
    redirect('../index.php');
}

$chamados = new Chamados();
$geral = new Geral();
$dados = $chamados->todosChamados();
?>
<?php include  '../scripts.php'; ?>

<body class="bg-gray-100 font-sans">

    <?php include  '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include  '../header.php'; ?>
        <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <input hidden name="nome_usuario" id="nome_usuario" value="<?= Security::getUser()['ds_nome'] ?>">
            <div id="adminTicketsView">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800">Todos os Chamados</h2>
                    <div class="flex items-center space-x-4">
                        <div class="relative">
                            <select id="adminTicketFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option value="">Todos</option>
                                <option value="0">Abertos</option>
                                <option value="1">Em Andamento</option>
                                <option value="9">Resolvidos</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                        <div class="relative">
                            <select id="adminAssignedFilter" class="block appearance-none bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500">
                                <option value="">Todos</option>
                                <option value="eu">Atribuídos a mim</option>
                                <option value="outros">Atribuídos a outros</option>
                                <option value="nao_atribuido">Não atribuídos</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="p-4 border-b border-gray-200">
                        <div class="relative w-full md:w-64">
                            <input type="text" id="adminTicketSearch" placeholder="Buscar chamados..." class="bg-gray-100 border border-gray-200 text-gray-700 py-2 px-4 pr-8 rounded w-full focus:outline-none focus:bg-white focus:border-gray-500">
                            <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>
                    <div class="overflow-x-auto" style="padding: 20px;">
                        <table id="tabelaChamados" class="min-w-[900px]">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Título</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Criador </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Atribuído </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data</th>
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
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $dado['criado'] ?></td>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900"><?= $dado['atribuido'] ?></td>
                                        <td class="px-6 py-4"><?= $st_status ?></td>
                                        <td class="px-6 py-4 text-sm text-gray-500"><?= $geral->formataData($dado['dt_data_chamado']) ?></td>
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
        </main>
    </div>
    <script>
        $(document).ready(function() {
            let filtroAtribuicao = '';
            const meuNome = $('#nome_usuario').val();
            const table = $('#tabelaChamados').DataTable({
                ordering: true,
                responsive: false, // permite adaptação automática
                dom: `
                <"w-full"t>
                <"flex flex-col md:flex-row md:justify-between md:items-center mt-4"
                <"text-sm text-gray-600"i>
                <"mt-2 md:mt-0"p>
                >
                `,
                order: [
                    [1, 'desc']
                ],
                lengthMenu: [10, 15, 20, 30, 50, 80, 100],
                pageLength: 10,

                language: {
                    lengthMenu: '',
                    search: '',
                    info: 'Mostrando _START_ até _END_ de _TOTAL_',
                    zeroRecords: 'Não existem dados cadastrados',
                    infoEmpty: 'Mostrando 0 até 0 de 0',
                    paginate: {
                        first: 'Primeira',
                        last: 'Última',
                        next: '>',
                        previous: '<'
                    }
                }
            });

            // Filtro de status
            $('#adminTicketFilter').on('change', function() {
                const status = $(this).val();
                const texto = statusTexto(status);
                table.column(4).search(texto, true, false).draw();
            });

            // Busca personalizada (campo fora do DataTables)
            $('#adminTicketSearch').on('keyup', function() {
                table.search(this.value).draw();
            });

            // Filtro de atribuição
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const nomeAtribuido = data[3] || ''; // Ajuste se necessário

                if (filtroAtribuicao === 'eu') {
                    return nomeAtribuido === meuNome;
                } else if (filtroAtribuicao === 'outros') {
                    return nomeAtribuido && nomeAtribuido !== meuNome;
                } else if (filtroAtribuicao === 'nao_atribuido') {
                    return nomeAtribuido === '';
                }

                return true; // caso "Todos"
            });

            // Atualiza valor do filtro e redesenha
            $('#adminAssignedFilter').on('change', function() {
                filtroAtribuicao = $(this).val();
                table.draw();
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