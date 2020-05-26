$(function(){

	var e = {
		url: path + "index.php/inventario/",
		area: null,
		collapse: null
	};

	var setValoresFormulario = function(dataRow, formId){
		$(formId)[0].reset();
    	$.each(dataRow, function(i, v){
    		$(formId +" #"+i).val(v);
    	});
	}

	//Define variables for input elements
	var fieldEl = document.getElementById("filter-field");
	var typeEl = document.getElementById("filter-type");
	var valueEl = document.getElementById("filter-value");

	//Custom filter example
	function customFilter(data){
		return data.car && data.rating < 3;
	}

	//Trigger setFilter function with correct parameters
	function updateFilter(){
		var filterVal = fieldEl.options[fieldEl.selectedIndex].value;
		var typeVal = typeEl.options[typeEl.selectedIndex].value;

		var filter = filterVal == "function" ? customFilter : filterVal;

		if(filterVal == "function" ){
			typeEl.disabled = true;
			valueEl.disabled = true;
		}else{
			typeEl.disabled = false;
			valueEl.disabled = false;
		}

		if(filterVal){
			table.setFilter(filter,typeVal, valueEl.value);
		}
	}

	//Update filters on value change
	document.getElementById("filter-field").addEventListener("change", updateFilter);
	document.getElementById("filter-type").addEventListener("change", updateFilter);
	document.getElementById("filter-value").addEventListener("keyup", updateFilter);

	//Clear filters on "Clear Filters" button click
	document.getElementById("filter-clear").addEventListener("click", function(){
		fieldEl.value = "";
		typeEl.value = "=";
		valueEl.value = "";
		table.clearFilter();
	});

	var table = new Tabulator("#inventario-table", {
		height:"490px",
	    layout:"fitColumns",
	    ajaxURL: e.url + "getInventario",
	    responsiveLayout:"hide",  //hide columns that dont fit on the table
		tooltips:true,            //show tool tips on cells
		addRowPos:"top",          //when adding a new row, add it to the top of the table
		history:true,             //allow undo and redo actions on the table
		pagination:"local",       //paginate the data
		paginationSize:17,         //allow 7 rows per page of data
		movableColumns:true,      //allow column order to be changed
		resizableRows:true,       //allow row order to be changed
	    placeholder:"Sin datos",
	    //autoColumns:true,
		columns:[                 //define the table columns
			//{title:"ID", field:"id"},
			{title:"Gerencia", field:"gerencia"},
			{title:"Red", field:"red"},
			{title:"Tipo", field:"tipo_ref"},
			{title:"Código", field:"codigo_ref"},
			{title:"Modelo Refacción", field:"modelo_ref"},
			{title:"Cantidad", field:"cantidad"},
			{title:"Tecnologia", field:"tecnologia"},
			{title:"Equipo donde se implementa", field:"equipo_imp"},
			{title:"Modelo Equipo", field:"modelo_imp"},
			{title:"Versión Equipo", field:"version_equipo_imp"},
			{title:"Proveedor", field:"proveedor_equipo"},
			{title:"Almacen", field:"almacen"},
			{title:"Ciudad Atención", field:"ciudad_atencion"},
			{title:"División", field:"division"},
			//{title:"Responsable", field:"responsable"},
		],
		rowDblClick:function(e, row){
			setValoresFormulario(row.getData(), "#form-refaccion");
	    	$("#modal-refaccion").modal('show');
	    },
	    tableBuilt:function(a){
    	}
	});

	(function(){

		let columns = table.getColumnDefinitions();
		let option = [];
		let item = "<option></option>";
		columns.forEach(column => {
			item += "<option value='"+column.field+"'>"+column.title+"</option>";
		});
		fieldEl.innerHTML = item;

	})();

	$("#reactivity-add").click(function(){
		$("#form-refaccion")[0].reset();
		$("#modal-refaccion").modal('show');
	});
	$("#reactivity-xlsx").click(function(){
		//table.download("csv", "Refacciones.csv", {delimiter:"|"});
		table.download("xlsx", "Refacciones.xlsx", {sheetName:"Inventario"});
	});

	$("#btn-save").click(function(){
		var serial = $("#form-refaccion").serialize();
		var ope = "registro";
		if( $("#id").val() != "" )
			ope = "cambio";

		setPost(e.url + ope, serial, function(response){
			if( response.message === true ){
				mensaje = "Se realizó correctamente.";
				clase = "alertify-success";
				$("#modal-refaccion").modal('hide');
				table.replaceData();
			}else{
				mensaje = "Ocurrio un error.";
				clase = "alertify-danger";
			}
			alertMessage(mensaje, clase);
		});

	})

	// document.getElementById("reactivity-update").addEventListener("click", function(){
	//     table.replaceData();
	// });

	


});