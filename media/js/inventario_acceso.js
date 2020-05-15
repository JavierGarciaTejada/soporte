$(function(){

	var e = {
		url: path + "index.php/acceso/",
		area: null,
		collapse: null
	};

	$('.collapse').on('show.bs.collapse', function () {
	    $('.collapse.in').collapse('hide');
	});

	var tablaDinamica = function(data){
		console.log(data);
		e.area = data.area;
		$.each(data.division, function(i, v){
			var item = renglonDivision(i, v);
			$("#table-division tbody").append( item.modelo );
			$("#table-division tbody").append( item.cantidad );
		});
	};

	var renglonDivision = function(division, detalle){

		var trModelo = $("<tr>").addClass('text-center');
		var trCantidad = $("<tr>").addClass('text-center');
		trModelo.append( "<td class='bg-thead-tmx' rowspan='2'>"+division+"</td>" );

		$.each(detalle, function(mod, det){
			trModelo.append( "<td class='bg-skies'><strong>"+mod+"</strong></td>" );
			var cant = ( Array.isArray(det) && det.length > 0 ) ? det.length : 0;
			trCantidad.append("<td class='item-collapse' data-toggle='collapse' data-target='#tr_"+division+"' data-div='"+division+"' data-mod='"+mod+"' >"+cant+"</td>");
		});

		var item = { modelo: trModelo[0].outerHTML, cantidad: trCantidad[0].outerHTML};
		return item;
	}

	var renglonArea = function(division, detalle, index, parent){
		$("tr#collapse_"+division).empty();
		$(".generated").remove();

		var rowsDetail = [];

		var itemColspan = "";
		for( var i = 0; i <= index; i++ )
			itemColspan += "<td style='border:none;' class='hiddenRow'><div class='collapse' id='tr_'>&nbsp;</div></td>";

		$.each( detalle, function(ind, val){
			var trArea = $("<tr>").addClass('generated');
			trArea.append(itemColspan);
			trArea.append( "<td class='hiddenRow text-12'><div class='col-sm-6 collapse' id='tr_"+division+"'>"+ind+"</div> <div class='col-sm-6 text-right'>"+val.length+"</div></td>" );
			rowsDetail.push(trArea[0].outerHTML);
		});

		parent.after(rowsDetail.join());
	}

	
	$(document).on('click', '.item-collapse', function(){

		if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.collapse').collapse('hide');
            $(".generated").remove();
            return 0;
        }
        
        $(".item-collapse").removeClass('active');
        $(this).addClass('active');
        var a = $(this).attr('data-div');
		var m = $(this).attr('data-mod');
		renglonArea(a, e.area[a][m], $(this).index(), $(this).parents('tr'));
		$('.collapse').collapse('show');
		
	})


	getJson(e.url + "getInventario", null, function(a){
		tablaDinamica(a);
	});

});