"use strict";
var KTModalCreateProjectType = (function () {
    var e, t, o, r;
    return {
        init: function () {
            (o = KTModalCreateProject.getForm()),
                (r = KTModalCreateProject.getStepperObj()),
                (e = KTModalCreateProject.getStepper().querySelector(
                    '[data-kt-element="type-next"]'
                )),
                (t = FormValidation.formValidation(o, {
                    fields: {
                        project_type: {
                            validators: { notEmpty: { message: "Selecione o tipo de entrada." } },
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
                })),
                e.addEventListener("click", function (o) {
                    o.preventDefault(),
                        (e.disabled = !0),
                        t &&
                        t.validate().then(function (t) {
                            if ("Valid" == t) {
                                var projectTypeValue = $('input[name="project_type"]:checked').val();
                                if (projectTypeValue == 2) {
                                    // Se o project_type for 2, vá diretamente para a próxima etapa
                                    e.setAttribute("data-kt-indicator", "on"),
                                        setTimeout(function () {
                                            e.removeAttribute("data-kt-indicator"),
                                                (e.disabled = !1),
                                                r.goNext();  // Avança para a próxima etapa
                                        }, 1000);
                                } else {
                                    var st_liberado = document.getElementById('st_liberado').value
                                    var st_liberado_localizacao = document.getElementById('st_liberado_localizacao').value
                                    var raio_localizacao = document.getElementById('raio_localizacao').value
                                    var latitude_localizacao = document.getElementById('latitude_localizacao').value
                                    var longitude_localizacao = document.getElementById('longitude_localizacao').value
                                    // Se o project_type for diferente de 2, faça a verificação de geolocalização
                                    if (st_liberado == 1) {
                                        e.setAttribute("data-kt-indicator", "on"),
                                            setTimeout(function () {
                                                e.removeAttribute("data-kt-indicator"),
                                                    (e.disabled = !1),
                                                    r.goNext();  // Avança para a próxima etapa
                                            }, 1000);
                                    } else if (st_liberado_localizacao == 1) {
                                        geolocalizacao(raio_localizacao,latitude_localizacao,longitude_localizacao).then(function (result) {
                                            if (result === true) {
                                                e.setAttribute("data-kt-indicator", "on"),
                                                    setTimeout(function () {
                                                        e.removeAttribute("data-kt-indicator"),
                                                            (e.disabled = !1),
                                                            r.goNext();  // Avança para a próxima etapa
                                                    }, 1000);
                                            } else {
                                                (e.disabled = !1);
                                                Swal.fire({
                                                    text: result.message, // Mostra a mensagem específica do erro
                                                    icon: "error",
                                                    buttonsStyling: !1,
                                                    confirmButtonText: "Ok",
                                                    customClass: { confirmButton: "btn btn-primary" },
                                                });
                                            }
                                        });
                                    }

                                    else {
                                        geolocalizacao(70,-15.790058,-47.997703).then(function (result) {
                                            if (result === true) {
                                                e.setAttribute("data-kt-indicator", "on"),
                                                    setTimeout(function () {
                                                        e.removeAttribute("data-kt-indicator"),
                                                            (e.disabled = !1),
                                                            r.goNext();  // Avança para a próxima etapa
                                                    }, 1000);
                                            } else {
                                                (e.disabled = !1);
                                                Swal.fire({
                                                    text: result.message, // Mostra a mensagem específica do erro
                                                    icon: "error",
                                                    buttonsStyling: !1,
                                                    confirmButtonText: "Ok",
                                                    customClass: { confirmButton: "btn btn-primary" },
                                                });
                                            }
                                        });
                                    }
                                }
                            } else {
                                (e.disabled = !1),
                                    Swal.fire({
                                        text: "Desculpe, selecione ao menos um tipo de entrada.",
                                        icon: "error",
                                        buttonsStyling: !1,
                                        confirmButtonText: "Ok, vou selecionar!",
                                        customClass: { confirmButton: "btn btn-primary" },
                                    });
                            }
                        });
                });
        },
    };
})();
"undefined" != typeof module &&
    void 0 !== module.exports &&
    (window.KTModalCreateProjectType = module.exports = KTModalCreateProjectType);

function geolocalizacao(raio,lat_base,long_base) {
    return new Promise(function (resolve, reject) {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(function (position) {
                var lat_usuario = position.coords.latitude;
                var long_usuario = position.coords.longitude;
                if (lat_usuario != "" && long_usuario != '') {

                    // var lat_base = -15.790058; // base
                    // var long_base = -47.997703;// base
                    // var lat_base = -15.79875152673027; //casa
                    // var long_base = -47.94323368775266; //casa

                    var distancia = calcularDistancia(lat_usuario, long_usuario, lat_base, long_base);
                    $("#nu_lat_long").val(distancia);
                    console.log(distancia);

                    // Verifica a distância do usuário em relação à base
                    if (distancia <= raio) {
                        // Usuário está dentro da distância permitida
                        resolve(true);
                    } else {
                        // Usuário está muito longe da base
                        resolve({ success: false, message: 'Você está muito longe da base.' });
                    }

                } else {
                    // Localização não disponível
                    resolve({ success: false, message: 'Localização indisponível.' });
                }

            }, function (error) {
                // Tratamento dos erros de geolocalização
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        resolve({ success: false, message: 'Permissão de localização negada.' });
                        break;
                    case error.POSITION_UNAVAILABLE:
                        resolve({ success: false, message: 'Localização indisponível.' });
                        break;
                    case error.TIMEOUT:
                        resolve({ success: false, message: 'Tempo de resposta excedido.' });
                        break;
                    default:
                        resolve({ success: false, message: 'Erro desconhecido ao obter localização.' });
                        break;
                }
            });
        } else {
            resolve({ success: false, message: 'Geolocalização não suportada pelo navegador.' });
        }
    });
}



function calcularDistancia(lat1, lon1, lat2, lon2) {
    var earthRadius = 6371; // Raio médio da Terra em quilômetros

    // Converter graus para radianos
    var lat1Rad = deg2rad(lat1);
    var lon1Rad = deg2rad(lon1);
    var lat2Rad = deg2rad(lat2);
    var lon2Rad = deg2rad(lon2);

    // Diferenças de coordenadas
    var deltaLat = lat2Rad - lat1Rad;
    var deltaLon = lon2Rad - lon1Rad;

    // Fórmula de Haversine
    var a = Math.sin(deltaLat / 2) * Math.sin(deltaLat / 2) + Math.cos(lat1Rad) * Math.cos(lat2Rad) * Math.sin(deltaLon / 2) * Math.sin(deltaLon / 2);
    var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    var distance = earthRadius * c;

    return distance * 1000; // Distância em metros
}

function deg2rad(deg) {
    return deg * (Math.PI / 180);
}
