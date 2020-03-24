$(function(){
	
	var e = {
		url: path + "index.php/informe/"
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
		var total = a.total.length;
		$("#total").html(total);
	}

	var conteo = function(a){
		var div6 = $('<div>').addClass('col-sm-8 list-group');
		$.each(a.data, function(ind, val){
			var title = '<a href="#" class="list-group-item text-center active"><strong>'+ind+'</strong></a>';
			div6.append(title);
			$.each(val, function(i, v){
				var item = '<a href="#" class="list-group-item"><span class="badge">'+v.length+'</span>'+i+'</a>';
				div6.append(item);
			})
		})
		$("#contenido").empty();
		$("#contenido").append(div6[0].outerHTML);
	}

	var promedios = function(a){
		var prom = $('<div>').addClass('col-sm-6 list-group');
		var titleProm = '<a href="#" class="list-group-item text-center active"><strong>Tiempo promedio de atención (horas)</strong></a>';
		prom.append(titleProm);
		$.each(a, function(i, v){
			var itemProm = '<a href="#" class="list-group-item"><span class="badge">'+v.tiempo+'</span>'+v.evento+'</a>';
			prom.append(itemProm);
		})
		$("#promedio").empty();
		$("#promedio").append(prom[0].outerHTML);
	}

	getJson(e.url + "getInforme", null, function(a){
		detalle(a.detalle);
		conteo(a);
	})

	getJson(e.url + "getPromedio", null, function(a){
		promedios(a);
	})


	$("#filtrar").click(function(){

		var serial = $("#form-filtro").serialize();
		getJson(e.url + "getInformeFiltrado", serial, function(a){
			detalle(a.detalle);
			conteo(a);
		})

		getJson(e.url + "getPromedioFiltrado", serial, function(a){
			promedios(a);
		})

	})

	$("#limpiar").click(function(){
		getJson(e.url + "getInforme", null, function(a){
			detalle(a.detalle);
			conteo(a);
		})
	
		getJson(e.url + "getPromedio", null, function(a){
			promedios(a);
		})
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