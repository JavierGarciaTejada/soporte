$(function(){

    //VALIDACION NUEVO REPORTE
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
            nombre_reporta: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese nombre de quién reporta'
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
            fecha_fin_falla: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione fecha y hora'
                    }
                }
            },
            entidad: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Entidad'
                    }
                }
            },
            evento: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Evento'
                    }
                }
            },
            fecha_soporte: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione fecha de soporte'
                    }
                    // ,regexp: {
                    //     regexp: /^[a-zA-Z0-9_]+$/,
                    //     message: 'Por favor, solo utilice números y letras'
                    // }
                }
            },
            fecha_falla: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione fecha de falla'
                    }
                }
            },
            fecha_reporte_falla: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione fecha de reporte'
                    }
                }
            },
            lugar: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese Lugar'
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






    //VALIDACIONES FINALIZAR
    $('#form-finalizar')
    .bootstrapValidator({
        message: 'Valor no es válido',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'],
        fields: {
            fecha_fin_falla_upd: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione fecha y hora'
                    }
                }
            },
            solucion: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese la solución del reporte'
                    }
                }
            }

        }
    });



    //VALIDACIONES ESCALADO
    $('#form-escalado')
    .bootstrapValidator({
        message: 'Valor no es válido',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        excluded: [':disabled'],
        fields: {
            reporte_escalado: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Número de Reporte'
                    }
                }
            },
            ingeniero_escalado: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese Ingeniero que Atiende'
                    }
                }
            },
            proveedor_escalado: {
                validators: {
                    notEmpty: {
                        message: 'Seleccione Empresa'
                    }
                }
            },
            solucion: {
                validators: {
                    notEmpty: {
                        message: 'Ingrese la solución del reporte'
                    }
                }
            }

        }
    });

})