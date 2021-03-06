$(function(){
	
	var e = {
		url: path + "index.php/reporte/"
	}

	var puesto = $("#puesto").val();
	var role = $("#role").val();
	var ni = $("#ni").val().split('@');
	var autorizacionCambios = ( puesto == "Gerente" || role == "Administrador") ? true : false;


	var botonesSoporte = function( td, data ) {
		var botones = [];
		if( data.estado == "En Proceso" || data.estado == "Finalizado" )
			botones.push( '<button class="btn btn-sm btn-info modificar-rep" id='+ data.id +' title="Actualizar Reporte"><i class="fa fa-edit" aria-hidden="true"></i></button>' );

		if( data.estado == "En Proceso" ){
			botones.push( '<button class="btn btn-sm btn-success finalizar" data-ref="finalizarReporte" data-ope="finalizar" id='+ data.id +' title="Finalizar Reporte"><i class="fa fa-check" aria-hidden="true"></i></button>' );
			botones.push( '<button class="btn btn-sm btn-danger estado" data-ref="cancelarReporte" data-ope="cancelar" id='+ data.id +' title="Cancelar Reporte"><i class="fa fa-times" aria-hidden="true"></i></button>' );
		}

		if( data.estado == "Liquidado" && autorizacionCambios)
		botones.push( '<button class="btn btn-sm btn-info modificar-rep" id='+ data.id +' title="Actualizar Reporte"><i class="fa fa-edit" aria-hidden="true"></i></button>' );;

		$( td ).html( '' + botones.join(' ') + '' );
	};

	if( ni[1] != "telmex.com" && ni[1] != "telmexomsasi.com" )
		botonesSoporte = null;

	$.datetimepicker.setLocale('es');
	$('#fecha_soporte').datetimepicker({
		minDate:'-1970/01/30',
 		maxDate:'+1970/01/01',
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});
	$('#fecha_falla').datetimepicker({
		minDate:'-1970/01/30',
 		maxDate:'+1970/01/01',
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	$('#fecha_escalado').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	$('#fecha_fin_escalado').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	$('#fecha_fin_falla').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()',
		onShow:function( ct ){
			this.setOptions({
				minDate:$('#fecha_soporte').val() ? $('#fecha_soporte').val() : false
			})
		},
		// ,minDate:0,
		// minTime:0
	});

	$('#fecha_fin_falla_upd').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		// format:'Y-m-d h:m:s',
		// startDate:	'NOW()',
		onShow:function( ct ){
			this.setOptions({
				minDate:$('#fecha_soporte_upd').val() ? $('#fecha_soporte_upd').val() : false
			})
		},
	});

	$('#fecha_reporte_falla').datetimepicker({
		minDate:'-1970/01/30',
 		maxDate:'+1970/01/01',
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	getJson(e.url + "getIngenieros", null, function(a){
		setValuesSelect('nombre', a.data, 'label', 'label', 'id');
	})

	getJson(e.url + "getClientes", null, function(a){
		$( "#nombre_reporta" ).autocomplete({
	     	source: a.data,
	     	select: function (event, ui) {
	  	   		$("#id_reporta").val(ui.item.id);
	  	   		$("#nombre_reporta").val(ui.item.value);        
			    return false;
			}
	    });
	})

	getJson(e.url + "getEntidades", null, function(a){
	 	setValuesSelect('entidad', a.data, 'label', 'label', 'id');
	})

	getJson(e.url + "getEquipos", null, function(a){
	 	setValuesSelect('equipo', a.data, 'label', 'label', 'id');
	})

	getJson(e.url + "getLugares", null, function(a){
	    setValuesSelect('lugar', a.data, 'label', 'label', 'id');
	})

	getJson(e.url + "getProveedores", null, function(a){
	 	setValuesSelect('proveedor', a.data, 'label', 'label', 'id');
	})

	var setValoresFormulario = function(row, formId){

		$(formId)[0].reset();
    	var dataRow = tableReportes.row( row.parents('tr') ).data();
    	$.each(dataRow, function(i, v){
    		$(formId +" #"+i).val(v);
    	})

	}

	var setListadoAnexos = function(id){
		var data = { id: id };
		var urlRef = path + "anexos/reportes/";
		$("#anexos_listado").html('');
		getJson(e.url + "getAnexos", data, function(a){
			if( a == false )
				return 0;
			var anexos = [];
			$.each(a, function(i, v) {
				anexos.push("<li><a href='"+ urlRef + v.no_generado +"'> "+ v.no +" </a></li>");
			})
			$("#anexos_listado").html(anexos.join(''));
			
		});
	}

	var tableReportes = $( '#table-reportes' ).DataTable({
		"processing": true,
		"serverSide": true,
		"sAjaxSource": e.url + "getReportes/",
		"language" : lenguageTable,
		"lengthChange": false,
		"scrollX" : true,
		"scrollY" : '67vh',
		"dom": 'Bfrtip',
		"buttons": [
	        {
	        	text: 'Exportar Filtrado',
	        	titleAttr: 'Exporta un excel con los datos de la tabla inferior respetando filtros',
	        	extend: 'excelHtml5',
	        },
	        {
	            text: 'Nuevo Reporte',
	            action: function ( dt ) {

	            	if( botonesSoporte == null )
	            		return 0;

	        		$("#form-reportes")[0].reset();
					$("#form-reportes").data('bootstrapValidator').resetForm();
					$("#id").val( "" );
					$("#solucion, #fecha_fin_falla").attr('readonly', 'readonly');
					$("#modal-reporte").modal('show');
					$( ".auc" ).autocomplete( "option", "appendTo", "#form-reportes" );

	            }
	        },
	        {
	        	text: 'Exportar Completo',
	        	titleAttr: 'Todos los eventos que corresponden a su perfil',
	        	action: function(dt){
	        		$("#link-xlsx").remove();
					$.ajax({
						url: e.url + "getExcelSpout",
						beforeSend: function( xhr ) {
							$( '#modal-loader' ).modal( 'show' );
						},
						complete: function(){
							$( '#modal-loader' ).modal( 'hide' );
						}
					})
					.done(function( a ) {
	        			var link = '<a href="'+a.path+'" id="link-xlsx" target="_blank"><button class="dt-button buttons-excel buttons-html5 btn btn-success btn-sm" tabindex="0" aria-controls="table-reportes" type="button"><span>XLSX</span></button></a>';
						$(".dt-buttons").append(link);
					});

	        	}
	        }
	        
	    ],
		"columns" : [
			{
				"targets" : 0,
				"width": "20%",
				"class" : "details-control",
				"orderable" : false,
				"data" : null,
				"defaultContent" : "",
				"searchable" : false,
				"createdCell" : botonesSoporte
			},
			{ "data" : "folio"},
			{ "data" : "nombre"},
			{ "data" : "evento"},
			{ "data" : "estado"},
			{ "data" : "impacto"},
			{ "data" : "equipo"},
			{ "data" : "proveedor"},
			{ "data" : "equipo_clli"},
			{ 
				"data" : "comentarios",
				"createdCell" : function( td, data ) {
					if( data !== null && data !== "" )
						$( td ).html('<textarea name="textarea" readonly rows="3" cols="50">'+data+'</textarea>')
				}
			},
			{ "data" : "lugar"},

			{ "data" : "cobo"},
			{ "data" : "subevento"},
			{ "data" : "causa_falla"},
			{ "data" : "imputable"},
			{ "data" : "area"},

			{ "data" : "fecha_falla"},
			{ "data" : "fecha_reporte_falla"},
			{ "data" : "fecha_soporte"},
			{ "data" : "fecha_fin_falla"},
			{ "data" : "tiempo"},
			{ 
				"data" : "solucion",
				"createdCell" : function( td, data ) {
					if( data !== null && data !== "" )
						$( td ).html('<textarea name="textarea" readonly rows="3" cols="50">'+data+'</textarea>')
				}
			},
			{ "data" : "reporte_refaccion"},
			{ "data" : "cantidad_refaccion"},
			{ "data" : "codigos_refaccion"},
			{ "data" : "origen_refaccion"},

			{ "data" : "fecha_escalado"},
			{ "data" : "reporte_escalado"},
			{ "data" : "fecha_fin_escalado"},
			{ 
				"data" : "solucion_escalado",
				"createdCell" : function( td, data ) {
					if( data !== null && data !== "" )
						$( td ).html('<textarea name="textarea" readonly rows="3" cols="50">'+data+'</textarea>')
				}
			}
		] 
	}); 
 	
	tableReportes.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );
	$("button.dt-button").addClass('btn btn-primary btn-sm');

	$("#btn-guardar-reporte").click(function(){
		var validator = $('#form-reportes').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
			return false;

		$("#id_ingeniero").val( $( "#nombre option:selected" ).attr('data-ref') );

		var serial = $("#form-reportes").serialize();
		var captura = $( "#nombre option:selected" ).attr('data-ref');
		serial += "&captura=" + captura;

		var ope = "registraReporte";
		if( $("#id").val() != "" )
			ope = "modificarReporte";


		if( $("#reporte_refaccion").val().length > 0 || $("#cantidad_refaccion").val().length > 0 || $("#codigos_refaccion").val().length > 0 ){
			var cant = parseInt( $("#cantidad_refaccion").val() );
			if( cant === 0 || isNaN(cant) || $("#cantidad_refaccion").val() == "" ){
				alert("Verifique la cantidad de refacciones");
				$("#cantidad_refaccion").focus();
				return 0;
			}
			if( $("#codigos_refaccion").val() == "" ){
				var text = ( cant == 1 ) ? 'código' : 'códigos';
				alert("Ingrese "+cant+" "+text+" de refacción");
				$("#codigos_refaccion").focus();
				return 0;
			}
			if( $("#reporte_refaccion").val() == "" ){
				alert("Ingrese el reporte de refacción");
				$("#reporte_refaccion").focus();
				return 0;
			}
		}

		setPost(e.url + ope, serial, function(response){
			console.log(response);
			if( response === true ){
				// notificacionCorreo();
				mensaje = "Se realizó correctamente.";
				clase = "alertify-success";
				$("#modal-evaluacion").modal('hide');
				tableReportes.ajax.reload();
			}else{
				mensaje = "Ocurrio un error.";
				clase = "alertify-danger";
			}

			$("#modal-reporte").modal('hide');
			alertMessage(mensaje, clase);
		});

	})



	//BOTON EDITAR
	$(document).on('click', '.modificar-rep', function(){
		$("#form-reportes")[0].reset();
    	$("#form-reportes").data('bootstrapValidator').resetForm();
    	$("#solucion, #fecha_fin_falla").attr('readonly', true);
		setValoresFormulario( $(this), "#form-reportes" );
		$("#modal-reporte").modal('show');
		$( ".auc" ).autocomplete( "option", "appendTo", "#form-reportes" );
	})

	//BOTON CANCELAR
	$(document).on('click', '.estado', function(){

    	var ope = $(this).attr('data-ref');
    	var mov = $(this).attr('data-ope'); 

    	var msg = "¿Está seguro de "+mov+" el soporte?";
    	if( confirm(msg) ){

    		var dataRow = tableReportes.row( $(this).parents('tr') ).data();
    		setPost(e.url + ope, dataRow, function(response){
				console.log(response);
				if( response === true ){
					mensaje = "Se realizó correctamente.";
					clase = "alertify-success";
					$("#modal-evaluacion").modal('hide');
					tableReportes.ajax.reload();
				}else{
					mensaje = "Ocurrio un error.";
					clase = "alertify-danger";
				}

				$("#modal-reporte").modal('hide');
				alertMessage(mensaje, clase);
			});

    	}

	})

	$(document).on('click', '.finalizar', function(){

		$("#form-finalizar")[0].reset();
		$("#form-finalizar").data('bootstrapValidator').resetForm();
		$("#id_rep_fin").val( $(this).attr('id') );
		var dataRow = tableReportes.row( $(this).parents('tr') ).data();
		$("#fecha_soporte_upd").val( dataRow.fecha_soporte );
		$("#modal-finalizar").modal('show');

	})

	$("#btn-finalizar").click(function(){

		var validator = $('#form-finalizar').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
			return false;

		var serial = $("#form-finalizar").serialize();

		var diff = moment( $("#fecha_fin_falla_upd").val() ).diff( $("#fecha_soporte_upd").val() );
		if( diff <= 0 ){
			alert("La fecha fin de falla debe ser mayor al inicio del soporte");
			return 0;
		}

		setPost(e.url + 'finalizarReporte', serial, function(response){
			if( response === true ){
				mensaje = "Se realizó correctamente.";
				clase = "alertify-success";
				$("#modal-evaluacion").modal('hide');
				tableReportes.ajax.reload();
			}else{
				mensaje = "Ocurrio un error.";
				clase = "alertify-danger";
			}

			$("#modal-reporte").modal('hide');
			alertMessage(mensaje, clase);
		});

	})


	//BOTON ESCALADO
	$(document).on('click', '.escalado', function(){
    	$("#form-escalado").data('bootstrapValidator').resetForm();
		setValoresFormulario( $(this), "#form-escalado" );
		$("#id_rep_escalado").val( $(this).attr('id') );
		$("#modal-escalado").modal('show');
	});

	//BOTON ARCHIVOS
	$(document).on('click', '.archivos', function(){

		$("#id_rep").val( $(this).attr('id') );
		$(".messages").hide();
		setListadoAnexos($(this).attr('id'));
    	$("#modal-anexo").modal('show');

	})

	//ANEXO DE EVALUACION
	$(".messages").hide();
	function showMessage(message){
		$(".messages").html("").show();
		$(".messages").html(message);
	}

    $( document ).on('change','#file-soporte' , function(){
        var file = $("#file-soporte")[0].files[0];
        var fileName = file.name;
        showMessage("<span class='info'>Archivo para subir: <strong>"+fileName+"</strong></span>");
	});

	var registraArchivoBD = function(result){

		var data = {
			no: result.nombre,
			no_generado: result.nombreGenerado,
			rx: $("#id_rep").val(),
			tp: 'liberacion'
		}
		setPost(e.url + "registaAnexo", data, function(response){
			console.log(response);
			setListadoAnexos($("#id_rep").val());
		});
  
	}
	
	$('#btn-file').click(function(ev){
		ev.preventDefault();

		//valida si se cargo archivo para enviar
		if( $("#file-soporte")[0].files[0] == undefined )
			return 0;

		//información del formulario
		var formData = new FormData($(".formulario")[0]);
		var message = ""; 

		//hacemos la petición ajax  
		$.ajax({
			url: e.url + "cargaAnexosReporte", method: 'POST', dataType: 'json',
			data: formData,
			cache: false,
			contentType: false,
			processData: false,
			beforeSend: function(){

				$("#btn-file").prop('disabled', true);
				showMessage(message);
				
			},

			success: function(data){

				if( data.estatus === true ) registraArchivoBD( data );
				
				$(".formulario")[0].reset();
				message = $("<span style='font-size: 12px' class='label label-success'>"+ data.mensaje +"</span>");
				showMessage(message);
				$("#btn-file").prop('disabled', false);

			},
			//si ha ocurrido un error
			error: function(){
				message = $("<span class='error'>Ha ocurrido un error.</span>");
				showMessage(message);
			}
		});
		
	})


});