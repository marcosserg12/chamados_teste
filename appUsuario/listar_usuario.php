<?php

require __DIR__ . '/../vendor/autoload.php';

$usuario = new Usuario();

$usuarios = $usuario->listarUsuario();

?>
<?php include __DIR__ . '/../scripts.php'; ?>

<body id="kt_app_body" data-kt-app-header-fixed="true" data-kt-app-header-fixed-mobile="true" data-kt-app-sidebar-enabled="true" data-kt-app-sidebar-fixed="true" data-kt-app-sidebar-push-header="true" data-kt-app-sidebar-push-toolbar="true" data-kt-app-sidebar-push-footer="true" class="app-default">
	<div class="d-flex flex-column flex-root app-root" id="kt_app_root">
		<div class="app-page  flex-column flex-column-fluid " id="kt_app_page">
			<?php include __DIR__ . '/../menu_central.php'; ?>
			<div class="app-wrapper  flex-column flex-row-fluid " id="kt_app_wrapper">
				<?php include __DIR__ . '/../menu_lateral.php'; ?>
				<div class="app-main flex-column flex-row-fluid " id="kt_app_main">
					<!--begin::Content wrapper-->
					<div class="d-flex flex-column flex-column-fluid">
						<div id="kt_app_content" class="app-content  flex-column-fluid ">
							<div id="kt_app_content_container" class="app-container  container-fluid ">
								<div class="kt-portlet kt-portlet--mobile">
									<div class="kt-portlet__head kt-portlet__head--lg">
										<div class="kt-portlet__head-label">
											<span class="kt-portlet__head-icon">
												<i class="kt-font-brand flaticon-search-1"></i>
											</span>
											<h3 class="kt-portlet__head-title">
												Usuários Cadastrados
											</h3>
										</div>
										<div class="kt-portlet__head-toolbar">
											<div class="kt-portlet__head-wrapper">
												<div class="kt-portlet__head-actions">
													<!--<div class="dropdown dropdown-inline">
						<button type="button" class="btn btn-default btn-icon-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="la la-download"></i> Exportar Relatório
						</button>
						<div class="dropdown-menu dropdown-menu-right">
							<ul class="kt-nav">
								<li class="kt-nav__section kt-nav__section--first">
									<span class="kt-nav__section-text">Selecione uma opção</span>
								</li>
								<li class="kt-nav__item">
									<a href="#" class="kt-nav__link">
										<i class="kt-nav__link-icon la la-print"></i>
										<span class="kt-nav__link-text">Imprimir</span>
									</a>
								</li>
								<li class="kt-nav__item">
									<a href="#" class="kt-nav__link">
										<i class="kt-nav__link-icon la la-copy"></i>
										<span class="kt-nav__link-text">Copiar</span>
									</a>
								</li>
								<li class="kt-nav__item">
									<a href="#" class="kt-nav__link">
										<i class="kt-nav__link-icon la la-file-excel-o"></i>
										<span class="kt-nav__link-text">Excel</span>
									</a>
								</li>
								<li class="kt-nav__item">
									<a href="#" class="kt-nav__link">
										<i class="kt-nav__link-icon la la-file-text-o"></i>
										<span class="kt-nav__link-text">CSV</span>
									</a>
								</li>
								<li class="kt-nav__item">
									<a href="#" class="kt-nav__link">
										<i class="kt-nav__link-icon la la-file-pdf-o"></i>
										<span class="kt-nav__link-text">PDF</span>
									</a>
								</li>
							</ul>
						</div>
					</div>-->
													&nbsp;
													<a href="../appUsuario/cadastro.php" class="btn btn-sm btn-primary">
														<i class="la la-plus"></i>
														Cadastrar novo
													</a>
												</div>
											</div>
										</div>
									</div>
									<div class="kt-portlet__body">

										<!--begin: Datatable -->
										<table class="table table-striped- table-bordered table-hover table-checkable" id="table_usuario">
											<thead>
												<tr>
													<th>Id Usuário</th>
													<th>Usuário</th>
													<th>Status</th>

													<th>Ações</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach ($usuarios as $dados) : ?>
													<tr>
														<td><?php echo $dados['id_usuario']; ?></td>
														<td><?php echo $dados['ds_usuario']; ?></td>
														<td><?php echo $dados['st_ativo']; ?></td>
														<td nowrap></td>
													</tr>
												<?php endforeach; ?>
											</tbody>
										</table>

										<!--end: Datatable -->
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

<script src="../assets/vendors/custom/datatables/datatables.bundle.js" type="text/javascript"></script>
<script src="../assets/js/datatables/appUsuario/lista_usuario.js" type="text/javascript"></script>