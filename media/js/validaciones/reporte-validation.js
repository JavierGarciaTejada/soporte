$(function(){

    //VALIDACION FORMULARIO LIBERAR
    $('#form-reportes')
    .bootstrapValidator({
        message: 'Valor no es válido',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'],
        fields: {
            nombre: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Ingeniero'
                    }
                }
            },
            inicio_falla: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Inicio Falla'
                    }
                }
            },
            inicio_soporte: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese Inicio Soporte'
                    }
                    // ,regexp: {
                    //     regexp: /^[a-zA-Z0-9_]+$/,
                    //     message: 'Por favor, solo utilice números y letras'
                    // }
                }
            },
            asunto: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese Asunto'
                    }
                }
            },
            impacto: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese impacto'
                    }
                    // ,emailAddress: {
                    //     message: 'Se detecto un correo no válido'
                    // }
                }
            },
            descripcion: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese Descripción'
                    }
                }
            }

        }
    });

})