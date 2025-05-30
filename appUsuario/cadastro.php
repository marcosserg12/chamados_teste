<?php

require __DIR__ . '/../vendor/autoload.php';

$hospital = new Hospital();
// $optionsHospital = $hospital->listarOptionsHospitalCheckbox();

$perfil = new Perfil();
$optionsPerfil   = $perfil->listarOptionsPerfilFaturamento();
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
												Cadastro Usuário
											</h3>
										</div>
									</div>
									<!--begin::Form-->
									<form class="kt-form" name="form_usuario">
										<div class="kt-portlet__body">
											<div class="form-group row">
												<div class="col-md-8">
													<label for="ds_nome">Nome Usuário</label>
													<input type="text" id="ds_nome" name="ds_nome" class="form-control">
												</div>
											</div>

											<div class="form-group row">
												<div class="col-md-8">
													<label for="ds_usuario">Login</label>
													<input type="text" id="ds_usuario" name="ds_usuario" class="form-control">
												</div>
											</div>

											<div class="form-group row">
												<div class="col-md-4">
													<label for="ds_email">E-mail</label>
													<input type="text" id="ds_email" name="ds_email" class="form-control">
												</div>

											</div>
											<div class="form-group row">
												<div class="col-md-4">
													<label for="id_perfil">Perfil</label>
													<select class="form-control" id="id_perfil" name="id_perfil">
														<option value=""></option>
														<?php echo $optionsPerfil; ?>
													</select>
												</div>
											</div>
											<div class="form-group row">
												<div class="col-md-8">
													<label>Ativo</label>
													<div class="kt-radio-inline">
														<label class="kt-radio">
															<input type="radio" id="st_ativo" name="st_ativo" value="A" checked="checked"> Sim
															<span></span>
														</label>
														<label class="kt-radio">
															<input type="radio" name="st_ativo" value="D"> Não
															<span></span>
														</label>
													</div>
												</div>
											</div>
											<!-- <div class="form-group row">
												<div class="col-md-4">
													<h3 class="sub-header">Hospital</h3>
													<div class="kt-checkbox-list">
														<?php foreach ($optionsHospital as $value) : ?>
															<label class='kt-checkbox'>
																<input type='checkbox' name='id_hospital[]' id='id_hospital_<?php echo $value['id_hospital']; ?>' value="<?php echo $value['id_hospital']; ?>" <?php if ($value['checked']) {
																																																					echo "checked";
																																																				} ?>> <?php echo $value['ds_hospital']; ?>
																<span></span>
															</label>
														<?php endforeach; ?>
													</div>
												</div>

											</div> -->
										</div>
										<div class="kt-portlet__foot">
											<div class="kt-form__actions">
												<button type="submit" class="btn btn-success">Salvar</button>
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
<script src="../assets/js/appUsuario/cadastro.js" type="text/javascript"></script>