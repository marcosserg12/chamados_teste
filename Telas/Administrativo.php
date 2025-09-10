<?php
include   '../scripts.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require  '../vendor/autoload.php';

if (!Security::isAuthenticated()) {
    redirect('../index.php');
}
if (Security::getUser()['id_perfil'] != 1) {
    redirect('../index.php');
}

$geral = new Geral();
$chamados = new Chamados();
$totalchamados = $chamados->totalChamados();
$totalchamados_semana = $chamados->totalChamadosSemanas();
$lista_tecnico = $chamados->chamadosTecnico();
$cores_tailwind = ['blue', 'green', 'red', 'yellow', 'indigo', 'purple', 'orange'];

?>

<body class="bg-gray-100 font-sans">
    <?php include   '../menu_lateral.php'; ?>
    <div class="md:ml-64 min-h-screen">
        <?php include  '../header.php'; ?>
        <main class="max-w-8xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <!-- Incluído conteúdo estático do painel -->
            <style>
                .tech-card {
                    transition: all 0.3s ease;
                }

                .tech-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
                }

                /* .demanda-item {
                    border-left: 3px solid #3b82f6;
                } */

                .hidden-details {
                    display: none;
                }

                .show-details {
                    display: block;
                }
            </style>

            <!-- Seção de Status de Chamados -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Status dos Chamados</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Card Chamados Abertos -->
                    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 font-medium">Chamados Abertos</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?= $totalchamados['aberto'] ?></h3>
                            </div>
                            <div class="bg-blue-100 p-3 rounded-full">
                                <i class="fas fa-ticket-alt text-blue-500 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500"><?= ($totalchamados_semana['aberto_atual'] - $totalchamados_semana['aberto_anterior'] >= 0 ? '+' : '') . ($totalchamados_semana['aberto_atual'] - $totalchamados_semana['aberto_anterior']) ?> em relação à semana passada</p>
                        </div>
                    </div>

                    <!-- Card Chamados em Andamento -->
                    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 font-medium">Chamados em Andamento</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?= $totalchamados['andamento'] ?></h3>
                            </div>
                            <div class="bg-yellow-100 p-3 rounded-full">
                                <i class="fas fa-clock text-yellow-500 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500"><?= ($totalchamados_semana['andamento_atual'] - $totalchamados_semana['andamento_anterior'] >= 0 ? '+' : '') . ($totalchamados_semana['andamento_atual'] - $totalchamados_semana['andamento_anterior']) ?> em relação à semana passada</p>
                        </div>
                    </div>

                    <!-- Card Chamados Concluídos -->
                    <div class="bg-white rounded-lg shadow p-6 border-t-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-gray-500 font-medium">Chamados Concluídos</p>
                                <h3 class="text-3xl font-bold text-gray-800"><?= $totalchamados['resolvidos'] ?></h3>
                            </div>
                            <div class="bg-green-100 p-3 rounded-full">
                                <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                            </div>
                        </div>
                        <div class="mt-4">
                            <p class="text-sm text-gray-500"><?= ($totalchamados_semana['resolvidos_atual'] - $totalchamados_semana['resolvidos_anterior'] >= 0 ? '+' : '') . ($totalchamados_semana['resolvidos_atual'] - $totalchamados_semana['resolvidos_anterior']) ?> em relação à semana passada</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Seção de Técnicos -->
            <section class="mb-10">
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Equipe Técnica</h2>

                <!-- Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="cardsContainer">
                    <?php
                    foreach ($lista_tecnico as $i => $tec):
                        $id = $i + 1;
                        $tec['bg'] = $cores_tailwind[$i % count($cores_tailwind)];
                        $iniciais = mb_substr(implode('', array_map(fn($n) => mb_substr($n, 0, 1), explode(' ', $tec['ds_nome']))), 0, 2);
                    ?>
                        <div class="tech-card bg-white rounded-lg shadow overflow-hidden flex flex-col h-full">

                            <div class="p-6 flex-1">

                                <div class="flex items-center space-x-4 mb-4">
                                    <div class="h-12 w-12 rounded-full bg-<?= $tec['bg'] ?>-100 flex items-center justify-center">
                                        <span class="text-<?= $tec['bg'] ?>-600 font-bold"><?= $iniciais ?></span>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-gray-800"><?= $tec['ds_nome'] ?></h3>
                                    </div>
                                </div>
                                <div class="flex justify-between mb-4">
                                    <div class="text-center">
                                        <p class="text-gray-500 text-sm">Em Andamento</p>
                                        <p class="font-bold text-yellow-600"><?= $tec['andamento'] ?></p>
                                    </div>
                                    <div class="text-center">
                                        <p class="text-gray-500 text-sm">Concluídos</p>
                                        <p class="font-bold text-green-600"><?= $tec['resolvidos'] ?></p>
                                    </div>
                                </div>
                                <button onclick="toggleDetails('details<?= $id ?>')" class="w-full py-2 bg-<?= $tec['bg'] ?>-50 text-<?= $tec['bg'] ?>-600 rounded-md font-medium hover:bg-<?= $tec['bg'] ?>-100 transition">
                                    Ver últimas demandas
                                </button>
                            </div>
                            <div id="details<?= $id ?>" class="hidden-details bg-gray-50 border-t p-4">
                                <h4 class="font-medium text-gray-700 mb-3">Últimas 5 demandas atendidas:</h4>
                                <ul class="space-y-2">
                                    <?php
                                    $ultimos_chamados = $chamados->ultimosChamadosTecnico($tec['id_usuario']);
                                    foreach ($ultimos_chamados as $i => $chamado):
                                        if ($chamado['st_status'] == 1) {
                                            $texto = '<p class="text-sm text-gray-500">Chamado em andamento desde ' . $geral->formataData($chamado['ultima_atualizacao']) . '.</p>';
                                            $cor = 'p-6 border-l-4 border-yellow-500';
                                        } else {
                                            $texto = '<p class="text-sm text-gray-500">Chamado concluído em ' . $geral->formataData($chamado['ultima_atualizacao']) . '.</p>';
                                            $cor = 'p-6 border-l-4 border-green-500';
                                        }

                                    ?>
                                        <li class="bg-white pl-3 py-3 <?= $cor ?>">
                                            <a target="_blank" href="../Telas/Detalhe_chamado.php?id_chamado=<?= $chamado['id_chamado'] ?>">
                                                <p class="font-medium text-gray-800">#<?= $chamado['id_chamado'] ?> - <?= $chamado['ds_titulo'] ?></p>
                                                <?= $texto ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </section>


            <!-- Seção de Tabela de Chamados por Técnico -->
            <section>
                <h2 class="text-xl font-semibold mb-4 text-gray-700">Chamados por Técnico</h2>
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Técnico
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                        Chamados em Andamento
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                        Chamados Concluídos
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                        Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($lista_tecnico as $ind => $tec_table):
                                    $tec['bg'] = $cores_tailwind[$ind % count($cores_tailwind)];
                                    $iniciais = mb_substr(implode('', array_map(fn($n) => mb_substr($n, 0, 1), explode(' ', $tec['ds_nome']))), 0, 2);
                                ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-<?= $tec['bg'] ?>-100 flex items-center justify-center">
                                                    <span class="text-<?= $tec['bg'] ?>-600 font-bold"><?= $iniciais ?></span>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?= $tec_table['ds_nome'] ?></div>
                                                    <div class="text-sm text-gray-500"><?= $tec_table['ds_email'] ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 items-center">
                                            <p class="font-bold text-yellow-600 text-center"><?= $tec_table['andamento'] ?></p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 items-center">
                                            <p class="font-bold text-green-600 text-center"><?= $tec_table['resolvidos'] ?></p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                                            <?= $tec_table['andamento'] + $tec_table['resolvidos'] ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

        </main>
    </div>

    <script>
        function toggleDetails(id) {
            document.querySelectorAll('.hidden-details').forEach(el => {
                if (el.id !== id) {
                    el.classList.remove('show-details');
                    el.style.display = 'none';
                }
            });

            const element = document.getElementById(id);
            const isVisible = element.classList.contains('show-details');

            if (isVisible) {
                element.classList.remove('show-details');
                element.style.display = 'none';
            } else {
                element.classList.add('show-details');
                element.style.display = 'block';
            }
        }
    </script>



    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</body>