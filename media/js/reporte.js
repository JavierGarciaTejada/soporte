$(function(){
	
	var e = {
		url: path + "index.php/reporte/"
	}

	$.datetimepicker.setLocale('es');
	$('#fecha_soporte').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});
	$('#fecha_falla').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	$('#fecha_fin_falla').datetimepicker({
		dayOfWeekStart : 1,
		lang:'es',
		disabledDates:['1986/01/08','1986/01/09','1986/01/10'],
		startDate:	'NOW()'
	});

	getJson(e.url + "getIngenieros", null, function(a){
		setValuesSelect('nombre', a.data, 'nombre', 'nombre', 'id');
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
 	
	var tableReportes = $( '#table-reportes' ).DataTable( 
	{
		"processing" : true,
		"language" : lenguageTable,
		"scrollX" : true,
		"scrollY" : '62vh',
		"scrollCollapse" : true,
		"orderCellsTop": true,
        "fixedHeader": true,
		"ajax" : {
			"url" : e.url + "getReportes/",
			"type" : "GET"
		},
		lengthChange: false,
		dom: 'Bfrtip',
        buttons: [
	        'excel'
	    ],
		"columns" : [
			{
				"targets" : 0,
				"class" : "details-control",
				"orderable" : false,
				"data" : null,
				"defaultContent" : "",
				"searchable" : false,
				"createdCell" : function( td, data ) {

					var botones = [];

					botones.push( '<button class="btn btn-sm btn-info modificar-rep" id='+ data.id +' title="Modificar Reporte"><i class="fa fa-search" aria-hidden="true"></i></button>' );
					if( data.estado == "En Proceso" ){
						botones.push( '<button class="btn btn-sm btn-success finalizar" data-ref="finalizarReporte" data-ope="finalizar" id='+ data.id +' title="Finalizar Reporte"><i class="fa fa-check" aria-hidden="true"></i></button>' );
						botones.push( '<button class="btn btn-sm btn-danger estado" data-ref="cancelarReporte" data-ope="cancelar" id='+ data.id +' title="Cancelar Reporte"><i class="fa fa-times" aria-hidden="true"></i></button>' );
					}

					$( td ).html( '' + botones.join(' ') + '' );
				}
			},
			{ "data" : "folio"},
			{ "data" : "nombre"},
			{ "data" : "asunto"},
			{ "data" : "estado"},
			{ "data" : "impacto"},
			{ "data" : "fecha_falla"},
			{ "data" : "fecha_soporte"},
			{ "data" : "fecha_fin_falla"},
			{ "data" : "solucion"},
			{
				// "targets" : 0,
				"class" : "details-control",
				"orderable" : false,
				"data" : null,
				"defaultContent" : "",
				"searchable" : false,
				"createdCell" : function( td, data ) {
					var botones = [];
					botones.push( '<button class="btn btn-sm btn-warning archivos" id='+ data.id +' title="Archivos"><i class="fa fa-file" aria-hidden="true"></i></button>' );
					$( td ).html( '' + botones.join(' ') + '' );
				}
			}
			
		]
	} );
	tableReportes.buttons().container().appendTo( '#example_wrapper .col-sm-6:eq(0)' );
	$("button.dt-button").addClass('btn btn-primary btn-sm');



	$("#btn-guardar-reporte").click(function(){
		var validator = $('#form-reportes').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
			return false;

		var serial = $("#form-reportes").serialize();
		var captura = $( "#nombre option:selected" ).attr('data-ref');
		serial += "&captura=" + captura;

		var ope = "registraReporte";
		if( $("#id").val() != "" )
			ope = "modificarReporte";

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

			alertMessage(mensaje, clase);
		});

	})



	//BOTON EDITAR
	$(document).on('click', '.modificar-rep', function(){

    	$("#form-reportes").data('bootstrapValidator').resetForm();
		setValoresFormulario( $(this), "#form-reportes" );

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

				alertMessage(mensaje, clase);
			});

    	}

	})

	$(document).on('click', '.finalizar', function(){

		$("#form-finalizar")[0].reset();
		$("#id_rep_fin").val( $(this).attr('id') );
		$("#modal-finalizar").modal('show');

	})

	$("#btn-finalizar").click(function(){

		var validator = $('#form-finalizar').data('bootstrapValidator');
        validator.validate();
        if (!validator.isValid())
			return false;

		var serial = $("#form-finalizar").serialize();
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

			alertMessage(mensaje, clase);
		});

	})


	//BOTON EDITAR
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