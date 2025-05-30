<?php

require __DIR__ . '/../vendor/autoload.php';

$id_usuario = Security::getUser()['id_usuario'];

$usuario = new Usuario();
$dadosUsuario = $usuario->buscaUsuario($id_usuario);
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
								<!--begin::Portlet-->
								<div class="kt-portlet">
									<div class="kt-portlet__head">
										<div class="kt-portlet__head-label">
											<h3 class="kt-portlet__head-title">
												Meu Perfil
											</h3>
										</div>
									</div>
									<!--begin::Form-->
									<form class="kt-form" name="form_alterar_perfil">
										<div class="kt-portlet__body">
											<div class="form-group row">
												<div class="col-md-8">
													<label for="ds_nome">Nome Usuário</label>
													<input type="text" id="ds_nome" name="ds_nome" class="form-control" value="<?php echo $dadosUsuario['ds_nome'] ?>">
												</div>
											</div>

											<div class="form-group row">
												<div class="col-md-8">
													<label>Login</label>
													<input type="text" id="ds_usuario" name="ds_usuario" class="form-control" value="<?php echo $dadosUsuario['ds_usuario'] ?>">
												</div>
											</div>

											<div class="form-group row">
												<div class="col-md-8">
													<label>E-mail</label>
													<input type="text" id="ds_email" name="ds_email" class="form-control" value="<?php echo $dadosUsuario['ds_email'] ?>">
												</div>

											</div>
										</div>
										<div class="kt-portlet__foot">
											<div class="kt-form__actions">
												<button type="submit" class="btn btn-success">Alterar</button>
												<button type="button" class="btn btn-danger" id="cancelar">Cancelar</button>
											</div>
										</div>
									</form>
									<!--end::Form-->
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>
<script src="../assets/js/appUsuario/alterar_perfil.js" type="text/javascript"></script>