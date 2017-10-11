// Standard license block omitted.
/*
 * @package    block_ases/permissionsmanagement
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/permissionsmanagement_main
  */

define(['jquery','block_ases/bootstrap','block_ases/datatables.net','block_ases/datatables.net-buttons','block_ases/buttons.flash','block_ases/jszip','block_ases/pdfmake','block_ases/buttons.html5','block_ases/buttons.print','block_ases/sweetalert','block_ases/select2'], function($,bootstrap,datatablesnet,datatablesnetbuttons,buttonsflash,jszip,pdfmake,buttonshtml5,buttonsprint,sweetalert,select2) {


  return {
      init: function() {
    
$("#profiles_user").select2({ 
  
  language: {

    noResults: function() {

      return "No hay resultado";        
    },
    searching: function() {

      return "Buscando..";
    }
  },
  width: '36%',
  dropdownAutoWidth : true,
});
$("#profiles_prof").select2({ 
  
  language: {

    noResults: function() {

      return "No hay resultado";        
    },
    searching: function() {

      return "Buscando..";
    }
  },
  width: '40%',
  dropdownAutoWidth : true,
});

$("#actions").select2({   
    dropdownAutoWidth : true,
   width: '100%',
  multiple: true,
  language: {

    noResults: function() {

      return "No hay resultado";        
    },
    searching: function() {

      return "Buscando..";
    }
  },
  dropdownAutoWidth : true,
});


$(document).ready(function() {
    //Cargar los datos de los roles creados en el select de rol-cmb
    var informacionUrl = window.location.search.split("&");
    for (var i = 0; i < informacionUrl.length; i++) {
            var elemento = informacionUrl[i].split("=");
            if (elemento[0] == "?instanceid" || elemento[0] == "instanceid") {
                var instance = elemento[1];
            }
        }

    
    $("#add_accion").on('click',function(){
        crearAccion();
    });

    $("#add_function").on('click',function(){
       crearFuncion(); 
    });

    $("#add_profile").on('click',function(){
        crearPerfil();
    });

    $("#assign_action_profile").on('click',function(){
       asignarAccionPerfil();
    });

    $("#assign_user_profile").on('click',function(){
       asignarUsuarioPerfil(instance); 
    });


});

/**
 * Crea un ajax para llamar la accion que guarda la nueva accion en la base de datos
 * @param Nombre 
 * @param Descripcion
 * @author Edgar Mauricio Ceron
 */ 

function crearAccion(){
    var nombre = $("#nombre").val().trim();
    var descripcion = $("#descripcion").val().trim();
    var msj = "";
    
    if(nombre.length == 0){
        msj = "Nombre no puede ser nulo"; 
    }
    if(descripcion.length == 0){
        msj = msj + "\nDescripcion no puede ser nulo";
    }
    
    if(nombre.length == 0 || descripcion.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre: nombre, descripcion: descripcion},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                $("#formAction")[0].reset();
                alert(msg);
            }
        });
    }
}

/**
 * Crea un ajax para llamar la accion que guarda la nueva función en la base de datos
 * @param Nombre 
 * @param Descripcion
 */ 

function crearFuncion(){
    var nombre = $("#nombre_funcionalidad").val().trim();
    var descripcion = $("#descripcion_funcionalidad").val().trim();
    var msj = "";
    
    if(nombre.length == 0){
        msj = "Nombre no puede ser nulo"; 
    }
    if(descripcion.length == 0){
        msj = msj + "\nDescripcion no puede ser nulo";
    }
    
    if(nombre.length == 0 || descripcion.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre_funcionalidad: nombre, descripcion_funcionalidad: descripcion},
            url: "../managers/ActionCreateAction.php",
            success: function(msg){
                alert("pass");
                $("#formAction")[0].reset();
                swal({
                 title: msg.title,
                 type: msg.type,
                 text: msg.text,
                });
            },
                dataType: "json",
                cache: "false",
        });
    }
}



/**
 * Crea un ajax para llamar la accion que guarda el nuevo perfil en la base de datos
 * @param Nombre 
 * @param Descripcion
 */ 

function crearPerfil(){
    var nombre= $("#nombre_perfil").val().trim();
    var descripcion = $("#descripcion_perfil").val().trim();
    var msj = "";
    
    if(nombre.length == 0){
        msj = "Nombre del perfil no puede ser nulo"; 
    }
    if(descripcion.length == 0){ 
        msj = msj + "\nDescripcion del perfil no puede ser nulo";
    }
    
    if(nombre.length == 0 || descripcion.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {nombre_perfil: nombre, descripcion_perfil: descripcion},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                $("#formPerfil")[0].reset();
                alert(msg);
            }
        });
    }
}

/**
 * Crea un ajax que asigna determinadas acciones a un pefil en la base de datos
 * @param perfil 
 * @param array acciones
 */ 
function asignarAccionPerfil(){
    var id_profile = $("#profiles_prof").val();
    var id_actions = $("#actions").val();
    var msj ="";

    if(id_profile.length == 0){
        msj = "Escoger un perfil"; 
    }
    if(id_actions.length == 0){ 
        msj = msj + "\n Escoger acciones";
    }
    
    if(id_profile.length == 0 || id_actions.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {profile: id_profile, actions: id_actions},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                alert(msg);
            }
        });
    }
 }

/**
 * Crea un ajax que asigna un usuario a un pefil en la base de datos
 * @param perfil 
 * @param array acciones
 */ 
function asignarUsuarioPerfil(instance){
    var id_profile = $("#profiles_user").val();
    var id_user = $("#users").val();
    var msj ="";

    if(id_profile.length == 0){
        msj = "Escoger un perfil"; 
    }
    if(id_user.length == 0){ 
        msj = msj + "\n Escoger usuario";
    }
    
    if(id_profile.length == 0 || id_user.length == 0){
        alert(msj);
    }
    else{
        $.ajax({
            type: "POST",
            data: {profiles_user: id_profile, users: id_user,instance: instance},
            url: "../managers/ActionCreateAction.php",
            async: false,
            success: function(msg){
                alert(msg);
            }
        });
    }
}

     $('#div_user_profile').on('click','#delete_user_profile',function(){
        
        var table = $("#div_user_profile #tableUserProfile").DataTable();
        var td =$(this).parent();
        var childrenid = $(this).children('span').attr('id');
        var colIndex = table.cell(td).index().column;
        
        var nombre =  table.cell(table.row(td).index(),0).data();
        var descripcion = table.cell(table.row(td).index(),1).data();
        swal(
            {  
                title: "Estas seguro/a?",   
                text: "La relación usuario perfil <strong>"+nombre+"</strong> se eliminará",   
                type: "warning",
                html: true,
                showCancelButton: true,   
                confirmButtonColor: "#d51b23",   
                confirmButtonText: "Si!",
                cancelButtonText: "No", 
                closeOnConfirm : true, 
            }, 
            function(isConfirm){
                if(isConfirm){

                    delete_record(childrenid,"usuario_perfil");
                }
            }
        );

    
    });

     $('#div_actions').on('click','#delete_action',function(){
        
        var table = $("#div_actions #tableActions").DataTable();
        var td =$(this).parent();
        var childrenid = $(this).children('span').attr('id');
        var colIndex = table.cell(td).index().column;
        
        var nombre =  table.cell(table.row(td).index(),0).data();
        var descripcion = table.cell(table.row(td).index(),1).data();
        swal(
            {  
                title: "Estas seguro/a?",   
                text: "La acción <strong>"+nombre+"</strong> se eliminará",   
                type: "warning",
                html: true,
                showCancelButton: true,   
                confirmButtonColor: "#d51b23",   
                confirmButtonText: "Si!",
                cancelButtonText: "No", 
                closeOnConfirm : true, 
            }, 
            function(isConfirm){
                if(isConfirm){

                    delete_record(childrenid,"accion");
                }
            }
        );

    
    });


     function delete_record(id,source){

        $.ajax({
        type: "POST",
        data: {
                id: id,
                source: source
            },
        url: "../managers/permissions_management/permissions_report.php",
        success: function(msg){
        swal({
            title: msg.title,
            html: true,
            text: msg.text,
            type: msg.type,
            confirmButtonColor: "#d51b23"
            });
           
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            alert("Error al cargar acciones")
        },
    });


    }




    $.ajax({
        type: "POST",
        url: "../managers/permissions_management/load_actions.php",
        success: function(msg){
            $("#div_actions").empty(); 
            $("#div_actions").append('<table id="tableActions"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
            var table = $("#tableActions").DataTable(msg);
            $('#div_actions #delete_action').css('cursor','pointer');
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            alert("Error al cargar acciones")
        },
    })


        $.ajax({
        type: "POST",
        url: "../managers/permissions_management/load_profiles.php",
        success: function(msg){
            $("#div_profiles").empty(); 
            $("#div_profiles").append('<table id="tableProfiles"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
            var table = $("#tableProfiles").DataTable(msg);
            $('#div_profiles #delete_profiles').css('cursor','pointer');
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            alert("Error al cargar acciones")
        },
    })


    $.ajax({
        type: "POST",
        url: "../managers/permissions_management/load_user_profile.php",
        success: function(msg){
            $("#div_user_profile").empty(); 
            $("#div_user_profile").append('<table id="tableUserProfile"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
            var table = $("#tableUserProfile").DataTable(msg);
            $('#div_user_profile #delete_user_profile').css('cursor','pointer');
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            alert("Error al cargar perfiles de usuarios")
        },
    })

    $.ajax({
        type: "POST",
        url: "../managers/permissions_management/load_action_profile.php",
        success: function(msg){
            $("#div_action_profile").empty(); 
            $("#div_actions_profile").append('<table id="tableActionProfile"  class="display" cellspacing="0" width="100%" ><thead><thead></table>');
            var table = $("#tableActionProfile").DataTable(msg);
            $('#div_action_profile #delete_').css('cursor','pointer');
        },
        dataType: "json",
        cache: "false",
        error: function(msg){
            alert("Error al cargar acciones")
        },
    })





    }
  };
});