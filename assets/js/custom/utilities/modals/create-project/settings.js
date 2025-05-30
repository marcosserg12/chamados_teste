"use strict";
var KTModalCreateProjectSettings = (function () {
    var e, t, i, o, r, myDropzone, dataURL, projectTypeValue, caminho, st_entrada, imageUrl;
    return {
        init: function () {
            (o = KTModalCreateProject.getForm()),
                (r = KTModalCreateProject.getStepperObj()),
                (e = KTModalCreateProject.getStepper().querySelector(
                    '[data-kt-element="settings-next"]'
                )),
                (t = KTModalCreateProject.getStepper().querySelector(
                    '[data-kt-element="settings-previous"]'
                )),
                myDropzone = new Dropzone("#kt_modal_create_project_settings_logo", {
                    url: "appJustificativa_falta/gravar_arquivo.php",
                    paramName: "file",
                    maxFiles: 1,
                    maxFilesize: 1, // em MB
                    addRemoveLinks: true,
                    autoProcessQueue: false,
                    accept: function (file, done) {
                        if (file.name === "justinbieber.jpg") {
                            done("Naha, you don't.");
                        } else {
                            done();
                        }
                    },
                    success: function (file, response) {
                        var responseObject;
                        try {
                            responseObject = JSON.parse(response);
                        } catch (e) {
                            console.error("Error parsing JSON:", e);
                            return;
                        }

                        if (responseObject.status === 'success') {
                            imageUrl = responseObject.fileUrl;
                            var serializedFormData = $('#kt_modal_create_project_form').serialize();
                            var $form = serializedFormData + '&caminho=' + encodeURIComponent(caminho) + '&uploadedImagePath=' + imageUrl;
                            $.ajax({
                                url: 'appPonto/gravar_justificativa_falta.php',
                                data: $form,
                                type: 'post',
                                success: function (response) {
                                    setTimeout(function () {
                                        location.reload();
                                    }, 6000);
                                },
                                error: function (data) {
                                    $(this).find('button[type="submit"]').prop('disabled', false);
                                    swal.fire("Erro", data.responseJSON.message, "error");
                                }
                            });
                        } else {
                            swal.fire("Erro", 'Não foi possível carregar a imagem', "error");
                            console.error("Upload failed: " + responseObject.message);
                        }
                    },
                    error: function (file, response) {
                        console.error("Upload error: " + response);
                    }
                }),


                // $(o.querySelector('[name="settings_release_date"]')).flatpickr({
                //     enableTime: !0,
                //     dateFormat: "d/m/Y H:i",
                // }),
                $(o.querySelector('[name="settings_release_date_atestado_de"]')).flatpickr({
                    dateFormat: "d/m/Y ",
                }),
                $(o.querySelector('[name="settings_release_date_atestado_ate"]')).flatpickr({
                    dateFormat: "d/m/Y ",
                }),
                st_entrada = $('#st_entrada').val();
            projectTypeValue = 1,
                $('#chegada').show();
            $('#atestado').hide();
            (i = FormValidation.formValidation(o, {
                fields: {
                    settings_confirmation: {
                        validators: { notEmpty: { message: "Confirme que está na base" } },
                    },
                    "settings_notifications[]": {
                        validators: {
                            notEmpty: { message: "Escolha o tipo de notificação que deseja receber" },
                        },
                    },
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap: new FormValidation.plugins.Bootstrap5({
                        rowSelector: ".fv-row",
                        eleInvalidClass: "",
                        eleValidClass: "",
                    }),
                },
            }))

            // Verifica o valor do project_type e exibe a div correta
            $('input[name="project_type"]').on('change', function () {
                projectTypeValue = $('input[name="project_type"]:checked').val();

                if (projectTypeValue == 1) {
                    $('#chegada').show();
                    $('#atestado').hide();
                    (i = FormValidation.formValidation(o, {
                        fields: {
                            settings_confirmation: {
                                validators: { notEmpty: { message: "Confirme que está na base" } },
                            },
                            "settings_notifications[]": {
                                validators: {
                                    notEmpty: { message: "Escolha o tipo de notificação que deseja receber" },
                                },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: ".fv-row",
                                eleInvalidClass: "",
                                eleValidClass: "",
                            }),
                        },
                    }))
                } else if (projectTypeValue == 2) {
                    $('#atestado').show();
                    $('#chegada').hide();
                    (i = FormValidation.formValidation(o, {
                        fields: {
                            settings_release_date_atestado_de: {
                                validators: { notEmpty: { message: "Confirme a data inicial" } },
                            },
                            settings_release_date_atestado_ate: {
                                validators: { notEmpty: { message: "Confirme a data final" } },
                            },
                            settings_confirmation_atestado: {
                                validators: { notEmpty: { message: "Confirme que quer enviar mesmo o arquivo" } },
                            },
                        },
                        plugins: {
                            trigger: new FormValidation.plugins.Trigger(),
                            bootstrap: new FormValidation.plugins.Bootstrap5({
                                rowSelector: ".fv-row",
                                eleInvalidClass: "",
                                eleValidClass: "",
                            }),
                        },
                    }))
                } else {
                    // Se nenhum valor válido estiver selecionado, esconde ambas
                    $('#atestado').hide();
                    $('#chegada').hide();
                }
            }),


                e.addEventListener("click", function (t) {
                    t.preventDefault();
                    e.disabled = true;

                    if (i) {
                        i.validate().then(function (t) {
                            console.log("validated!");
                            if (t === "Valid") {
                                e.setAttribute("data-kt-indicator", "on");
                                console.log(st_entrada)
                                // Lançar o ajax para salvar assinatura
                                if (st_entrada == 1 || projectTypeValue == 2) {
                                    // dataURL = signaturePad.toDataURL();
                                    // $.ajax({
                                    // url: 'appAssinatura/salvar_assinatura.php',
                                    // type: 'POST',
                                    // data: {
                                    //     imagem: dataURL
                                    // },
                                    // success: function (response) {
                                    // console.log('Assinatura salva com sucesso!');
                                    // caminho = response;
                                    // caminho = caminho.substring(3);
                                    // Verificar se projectTypeValue é 2
                                    if (projectTypeValue == 1) {
                                        var serializedFormData = $('#kt_modal_create_project_form').serialize();

                                        // Combine os dados do formulário com o caminho da assinatura
                                        var $form = serializedFormData + '&caminho=' + null;
                                        // var $form = serializedFormData + '&caminho=' + encodeURIComponent(caminho);

                                        $.ajax({
                                            url: 'appPonto/gravar_entrada.php',
                                            data: $form,
                                            type: 'post',
                                            success: function (response) {
                                                $.ajax({
                                                    url: 'appPonto/enviar_mensagem_wpp.php',
                                                    data: $form,
                                                    type: 'post',
                                                    success: function (response) {
                                                    },
                                                });

                                                setTimeout(function () {
                                                    location.reload();
                                                }, 4500);

                                            },
                                            error: function (data) {
                                                $(this).find('button[type="submit"]').prop('disabled', false);
                                                swal.fire("Erro", data.responseJSON.message, "error");
                                            }
                                        });
                                    }


                                    // }
                                    // });
                                    if (projectTypeValue == 2) {
                                        dataURL = signaturePad3.toDataURL();
                                        $.ajax({
                                            url: 'appAssinatura/salvar_assinatura.php',
                                            type: 'POST',
                                            data: {
                                                imagem: dataURL
                                            },
                                            success: function (response) {
                                                console.log('Assinatura salva com sucesso!');
                                                caminho = response;
                                                caminho = caminho.substring(3);
                                                // Verificar se projectTypeValue é 2

                                                myDropzone.processQueue();

                                            }
                                        });
                                    }
                                } else {
                                    var $form = $('#kt_modal_create_project_form').serialize();
                                    $.ajax({
                                        url: 'appPonto/gravar_saida.php',
                                        data: $form,
                                        type: 'post',
                                        success: function (response) {
                                            $.ajax({
                                                url: 'appPonto/enviar_mensagem_wpp.php',
                                                data: $form,
                                                type: 'post',
                                                success: function (response) {
                                                },
                                            });

                                            setTimeout(function () {
                                                location.reload();
                                            }, 4500);

                                        },
                                        error: function (data) {
                                            $(this).find('button[type="submit"]').prop('disabled', false);
                                            swal.fire("Erro", data.responseJSON.message, "error");
                                        }
                                    });
                                }
                                // Timeout para continuar o processo
                                setTimeout(function () {
                                    e.removeAttribute("data-kt-indicator");
                                    e.disabled = !1
                                    r.goNext();
                                }, 1500);
                            } else {
                                e.disabled = !1
                                Swal.fire({
                                    text: "Desculpe, parece que você não preencheu todos os campos.",
                                    icon: "error",
                                    buttonsStyling: false,
                                    confirmButtonText: "Ok, vou preencher!",
                                    customClass: { confirmButton: "btn btn-primary" },
                                });
                            }
                        });
                    }
                });

            t.addEventListener("click", function () {
                r.goPrevious();
            });
        },
    };
})();
"undefined" != typeof module &&
    void 0 !== module.exports &&
    (window.KTModalCreateProjectSettings = module.exports =
        KTModalCreateProjectSettings);

