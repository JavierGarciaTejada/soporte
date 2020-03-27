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
		'FALLA' : "list-group-item-success",
		'PROGRAMADO' : "list-group-item-success",
		'REFACCIÓN' : "list-group-item-success"
	}

	var conteo = function(a){
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

	}

	// var promedios = function(a){
	// 	var prom = $('<div>').addClass('col-sm-6 list-group');
	// 	var titleProm = '<a href="#" class="list-group-item text-center active"><strong>Tiempo promedio de atención (horas)</strong></a>';
	// 	prom.append(titleProm);
	// 	$.each(a, function(i, v){
	// 		var itemProm = '<a href="#" class="list-group-item"><span class="badge">'+v.tiempo+'</span>'+v.evento+'</a>';
	// 		prom.append(itemProm);
	// 	})
	// 	$("#promedio").empty();
	// 	$("#promedio").append(prom[0].outerHTML);
	// }

	getJson(e.url + "getInforme", null, function(a){
		e.objeto = a;
		console.log(a);
		detalle(a.data.total);
		conteo(a);
	})

	/*getJson(e.url + "getPromedio", null, function(a){
		promedios(a);
	})*/


	$("#filtrar").click(function(){

		$("#detalle").empty();
		var serial = $("#form-filtro").serialize();
		getJson(e.url + "getInformeFiltrado", serial, function(a){
			e.objeto = a;
			detalle(a.data.total);
			if( a.data.total.length == 0 ) $(".itm").empty();
			else conteo(a);	
		})

		// getJson(e.url + "getPromedioFiltrado", serial, function(a){
		// 	promedios(a);
		// })

	})

	$("#limpiar").click(function(){
		$("#detalle").empty();
		getJson(e.url + "getInforme", null, function(a){
			detalle(a.data.total);
			conteo(a);
		})
	
		// getJson(e.url + "getPromedio", null, function(a){
		// 	promedios(a);
		// })
	})

	// $(document).on('click', '.item-conteo', function(){
	// 	var ref = $(this).attr('data-ref');
	// 	var ind = $(this).attr('data-ind');
	// 	console.log(ref);
	// 	console.log(ind);
	// 	console.log(e.objeto.data[ref][ind]);

	// 	var values = e.objeto.data[ref][ind];
	// 	var table = $("<table>").addClass('table table-bordered');
	// 	var thead = $("<thead>");
	// 	var columnas = "<tr><th>No Reporte</th><th>Descripción</th></tr>";
	// 	thead.append(columnas);
	// 	table.append(thead);

	// 	values.forEach(element => {
	// 		var tr = $("<tr>");
	// 		tr.append( $("<td>").html(element.folio) );
	// 		tr.append( $("<td>").html(element.comentarios) );
	// 		table.append(tr);
	// 	});
	// 	$("#detalle").empty();
	// 	$("#detalle").append(table[0].outerHTML);
	// })

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