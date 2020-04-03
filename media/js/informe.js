$(function(){
	
	var e = {
		url: path + "index.php/informe/",
		objeto: null
	}

	$.datetimepicker.setLocale('es');
	$('#fecha_soporte').datetimepicker({
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

	var detalle = function(a){
		var total = a.length;
		$("#total").html(total);
	}

	var titulos = {
		'Liquidado' : 'Liquidados',
		'En Proceso' : 'En Proceso',
		'Promedio' : 'Tiempo promedio de atención (minutos)',
		'Cancelado' : 'Cancelados',
		'Gerencias': 'Gerencias'
	}

	var colores = {
		'TOTAL' : "",
		'CON AFECTACION' : "list-group-item-info",
		'SIN AFECTACION' : "list-group-item-info",
		'ESCALADO' : "",
		'ASESORIA / CONSULTA' : "list-group-item-success",
		'FALLA' : "list-group-item-success",
		'CTRL CAMBIOS / PROGRAMADO' : "list-group-item-success",
		'REFACCIÓN' : "list-group-item-success"
	}

	var conteo = function(a, filtro){
		$.each(a.data, function(ind, val){
			var div = $('<div>').addClass('col-sm-8 list-group');
			var titulo = titulos[ind];
			div.append('<a href="#" class="list-group-item text-center active"><strong>'+titulo+'</strong></a>');

			$.each(val, function(i, v){
				var badgeValue = ( Array.isArray(v) ) ? v.length : v;
				var texto = ( ind == 'Promedio' || ind == 'Gerencias' || ind == 'total' ) ? i : i.substring(2);
				var color = ( ind == 'Liquidado' || ind == 'En Proceso' ) ? colores[texto] : "";
				div.append('<a href="#" class="list-group-item item-conteo '+color+'" data-ref="'+ind+'" data-ind="'+i+'"><span class="badge">'+badgeValue+'</span>'+texto+'</a>');
			})
			
			ind = ( ind == "En Proceso" ) ? "Proceso" : ind;
			$("#contenido #"+ind).empty();
			$("#contenido #"+ind).append(div[0].outerHTML);
		})


		$("#tabla-conteo-gerencia thead, #tabla-conteo-gerencia tbody").empty();

		var thead = $("<tr>");
		thead.append("<th>Día</th>")
		$.each(a.data.conteo.head, function(a,  b){
			thead.append("<th>"+a+"</th>")
		})
		$("#tabla-conteo-gerencia thead").append( thead[0].outerHTML );

		$.each(a.data.conteo.body, function(a,  b){
			var trd = $("<tr>");
			trd.append("<td>"+a+"</td>");
			$.each(b, function(z, y){
				var cls = ( y.length > 0 ) ? "cont" : "";
				trd.append( "<td data-ref='"+z+"' data-day='"+a+"' data-cont='"+y.length+"' class='"+cls+"'>"+y.length+"</td>" )
			})
			$("#tabla-conteo-gerencia tbody").append( trd[0].outerHTML );
		})

	}

	$(document).on('click', '.cont', function(){
		var ref = $(this).attr('data-ref');
		var day = $(this).attr('data-day');
		var c = $(this).attr('data-cont');

		var serial = {
			dia: day,
			siglas: ref
		};

		getJson(e.url + "getInformeEventosDiaSiglas", serial, function(a){

			console.log(a);
			
			var divRemoto = $('<div>').addClass('col-sm-6 list-group');
			var divSitio = $('<div>').addClass('col-sm-6 list-group');

			divRemoto.append('<a href="#" class="list-group-item text-center active"><strong>Ingenieros Remoto</strong></a>');
			divSitio.append('<a href="#" class="list-group-item text-center active"><strong>Ingenieros Sitio</strong></a>');

			$.each(a.data, function(i, v){
				if( v.sitio == "Remoto" )
					divRemoto.append('<a href="#" class="list-group-item item-conteo"><span class="badge">'+v.total+'</span>'+v.nt+'</a>');
				else 
					divSitio.append('<a href="#" class="list-group-item item-conteo"><span class="badge">'+v.total+'</span>'+v.nt+'</a>');
			})

			$("#table-conteo-ingeniero").empty();
			$("#table-conteo-ingeniero").append(divRemoto[0].outerHTML);
			$("#table-conteo-ingeniero").append(divSitio[0].outerHTML);

		})

		
	})

	getJson(e.url + "getInforme", null, function(a){
		e.objeto = a;
		console.log(a);
		detalle(a.data.total);
		conteo(a, false);
	})

	$("#filtrar").click(function(){

		$("#detalle").empty();
		var serial = $("#form-filtro").serialize();
		getJson(e.url + "getInformeFiltrado", serial, function(a){
			console.log(a);
			e.objeto = a;
			detalle(a.data.total);
			if( a.data.total.length == 0 ) {
				$(".itm").empty();
			}
			else { 
				conteo(a, true);
			}
		})

		// getJson(e.url + "getPromedioFiltrado", serial, function(a){
		// 	promedios(a);
		// })

	})

	$("#limpiar").click(function(){
		$("#detalle").empty();
		getJson(e.url + "getInforme", null, function(a){
			detalle(a.data.total);
			conteo(a, false);
		})
	
		// getJson(e.url + "getPromedio", null, function(a){
		// 	promedios(a);
		// })
	})

	$(document).on('click', '#Proceso .item-conteo', function(){
		var ref = $(this).attr('data-ref');
		var ind = $(this).attr('data-ind');


		console.log(e.objeto.data[ref][ind]);

		var values = e.objeto.data[ref][ind];
		var table = $("<table>").addClass('table table-bordered');
		var thead = $("<thead>");
		var columnas = "<tr><th>Reporte</th><th>Evento</th><th>Descripción</th></tr>";
		thead.append(columnas);
		table.append(thead);

		values.forEach(element => {
			var tr = $("<tr>");
			tr.append( $("<td>").html(element.folio) );
			tr.append( $("<td>").html(element.evento) );
			tr.append( $("<td>").html(element.comentarios) );
			table.append(tr);
		});
		$("#detalle").empty();
		$("#detalle").append(table[0].outerHTML);
		$("#modalDetalle").modal('show');
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


});