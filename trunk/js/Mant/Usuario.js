/* Funciones para el mantenedor de usuarios */

$(function() {
	
	$( "#btRegUsub" ).button();
	$( "#btRegUsuGrabar" ).button();
	$( "#btRegUsue" ).button();
	$( "#btRegUsuLimpiar" ).button();

	$( "#FormRegUsuDesc" ).val('');
	
	/* Dialogo de confirmación para guardar */
	$( '#confirmG' ).dialog({
		autoOpen: false,
		width: 300,
		height: 260,
		modal: true,
		resizable: false,
		buttons : {
	        "Confirmar" : function() {
	           $.post("fuentes/GrabarUsuario.php", $('#FormRegUsu').serialize(),
					   function(data) {
					   	var obj = jQuery.parseJSON(data);
	
				   		$('#dMsg').html( obj.html );
				   		$('#FormIniSesErr').dialog( "open" );
				   		oTabUsu.fnReloadAjax();
					   });

			   $(this).dialog("close");
	        },
	        "Cancelar" : function() {
	          $(this).dialog("close");
        	}}
	});

	/* Dialogo de confirmación para Eliminar */
	$( '#confirmB' ).dialog({
		autoOpen: false,
		width: 300,
		height: 260,
		modal: true,
		resizable: false,
		buttons : {
	        "Confirmar" : function() {
	           
	           $.post("fuentes/EliminarUsuario.php", $('#FormRegUsu').serialize(),
					   function(data) {
					   	var obj = jQuery.parseJSON(data);

				   		$('#dMsg').html( obj.html );
				   		$('#FormIniSesErr').dialog( "open" );
				   		fRU.resetForm();
					 	$( "#FormRegUsuIDUsu" ).val("");
					 	$( "#FormRegUsuNomUsu" ).val("");
					 	$( "#FormRegUsuEmail" ).val("");
					 	$( "#FormRegUsuGrupo" ).val(0);
					 	$( "#FormRegUsuFecNac" ).val("");
					 	$( "#FormRegUsuActivo" ).attr('checked', false);
					 	$( "#FormRegUsuPass1" ).val("");
					 	$( "#FormRegUsuPass2" ).val("");
					 	$( "#FormRegUsuDesc" ).val("");
					 	oTabUsu.$('tr.row_selected').removeClass('row_selected');
				   		oTabUsu.fnReloadAjax();
					   });
			   $(this).dialog("close");
	        },
	        "Cancelar" : function() {
	          $(this).dialog("close");
        	}}
	});

	/* Validaciones del formulario */
	var fRU = $( '#FormRegUsu').validate({
                rules: {
                    txtNombreCorto: {required: true,
                    					 minlength: 1,
                    					 maxlength: 20},
                    txtNombreLargo: {required: true, 
										 minlength: 1,
                    					 maxlength: 150},
                    txtNombreBinario: {required: true, 
                    				   minlength: 1,
                    				   maxlength: 100},
                    txtNombreServidor: {required: true, 
                    				   minlength: 1,
                    				   maxlength: 100},
                    txtNombreRuta: {required: true, 
                    				   minlength: 1,
                    				   maxlength: 100},
                    txtNombreVersion: {required: true, 
                    				   minlength: 1,
                    				   maxlength: 100}
                },
                messages: {
                    txtNombreCorto: {required: "",
                    					 minlength: "",
                    					 maxlength: ""},
                    txtNombreLargo: {required: "",
                    					 minlength: "",
                    					 maxlength: ""},
                    txtNombreBinario: {required: "", 
                    				   minlength: "",
                    				   maxlength: ""},
                    txtNombreServidor: {required: "", 
                    				   minlength: "",
                    				   maxlength: ""},
                    txtNombreRuta: {required: "", 
                    				   minlength: "",
                    				   maxlength: ""},
                    txtNombreVersion: {required: "", 
                    				   minlength: "",
                    				   maxlength: ""}
                }
         });
	
    /* Inicializacion de la tabla */
	var oTabUsu = $('#table_id').dataTable({   
         bJQueryUI: true,
         sPaginationType: "full_numbers", //tipo de paginacion
         "bFilter": true, // muestra el cuadro de busqueda
         "iDisplayLength": 5, // cantidad de filas que muestra
         "bLengthChange": false, // cuadro que deja cambiar la cantidad de filas
         "oLanguage": { // mensajes y el idio,a
	            "sLengthMenu": "Mostrar _MENU_ registros",
	            "sZeroRecords": "No hay resultados",
	            "sInfo": "Resultados del _START_ al _END_ de _TOTAL_ registros",
	            "sInfoEmpty": "0 Resultados",
	            "sInfoFiltered": "(filtrado desde _MAX_ registros)",
	            "sInfoPostFix":    "",
			    "sSearch":         "Buscar:",
			    "sUrl":            "",
			    "sInfoThousands":  ",",
			    "sLoadingRecords": "Cargando...",
			    "oPaginate": {
			        "sFirst":    "Primero",
			        "sLast":     "Último",
			        "sNext":     "Siguiente",
			        "sPrevious": "Anterior"
			    }
	        },
	     "bProcessing": true, //para procesar desde servidor
	     "sServerMethod": "POST",
	     //"sAjaxSource": "./array.txt", // fuente del json
	     "sAjaxSource": './fuentes/Buscador.php', // fuente del json
	     "fnServerData": function ( sSource, aoData, fnCallback ) { // Para buscar con el boton
            $.ajax( {
                "dataType": 'json', 
                "type": "POST", 
                "url": sSource, 
                "data": $('#FormRegUsu').serialize(), 
                "success": fnCallback
            	} );
           }
	});

	/* Para cargar un elemento de la tabla */
	$("#table_id tbody").delegate("tr", "click", function() {
		
		/* parte donde cambiamos el css */
		if ( $(this).hasClass('row_selected') ) {
       	 $(this).removeClass('row_selected');
       	}
        else {
            oTabUsu.$('tr.row_selected').removeClass('row_selected');
            $(this).addClass('row_selected');
        }
		/* Parte donde cargamos los input */
		var iPos = oTabUsu.fnGetPosition( this );
		if(iPos!=null){
		    var aData = oTabUsu.fnGetData( iPos );//get data of the clicked row
		    //var iId = aData[1];//get column data of the row
		    //oTabUsu.fnDeleteRow(iPos);//delete row
		    $("#txtNombreCorto").val(aData[0]);
		    $("#txtNombreLargo").val(aData[1]);
		    $("#txtNombreBinario").val(aData[2]);
		    $("#txtNombreServidor").val(aData[3]);
		    $("#txtNombreRuta").val(aData[4]);
		    $("#txtNombreVersion").val(aData[5]);
		    $("#txtAtajos").val(aData[6]);

		}});
	
    /* Boton para limpiar */
    $("#btRegUsuLimpiar").button().click( function() {
    	fRU.resetForm();
	 	$( "#txtNombreCorto" ).val("");
	 	$( "#txtNombreLargo" ).val("");
	 	$( "#txtNombreBinario" ).val("");
	 	$( "#txtNombreServidor" ).val("");
	 	$( "#txtNombreRuta" ).val("");
	 	$( "#txtNombreVersion" ).val("");
	 	$( "#txtAtajos" ).val("");

	 	oTabUsu.$('tr.row_selected').removeClass('row_selected');
	 	oTabUsu.fnReloadAjax();
	});
	
	/* Boton para Buscar */
	$( "#btRegUsub" ).button().click( function() {
		oTabUsu.fnReloadAjax();
	});

});

$(function() {
  var oTable = $('#table_id').dataTable();
   
  // Hide the second column after initialisation
  oTable.fnSetColumnVis( 4, false );
  oTable.fnSetColumnVis( 6, false );
} );

