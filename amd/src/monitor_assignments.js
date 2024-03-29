// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @author Jeison Cardona Gómez <jeison.cardona@correounivalle.edu.co>
  * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
  * @module block_ases/monitor_assignments
  */

 define([
     'jquery',
     'core/config',
     'block_ases/loading_indicator',
     'block_ases/csv',
     'block_ases/bootstrap',
     'block_ases/sweetalert',
     'block_ases/jqueryui',
     'block_ases/select2',
     'block_ases/jquery.dataTables'
 ], function($, CFG,  loading_indicator, csv, bootstrap, sweetalert, jqueryui, select2) {
     
    var asignation_counter = []; 
     
    var BUTTON_GET_COMPLETE_REPORT_NAME_SELECTOR = '#button-get-complete-report';
    var GET_COMPLETE_REPORT_NAME_FUNCTION_NAME = 'get_monitor_practicing_and_students_report';
    var BASE_API_URL = '/blocks/ases/managers/monitor_assignments/monitor_assignments_api.php';
    var DOWNLOAD_FILE_NAME = "asignaciones_monitor_con_practicantes.csv";
    
    var api_url = function () {
      return  CFG.wwwroot+BASE_API_URL;
    };
     /**
      * Return a Promise with a string, than have the report in CSV format
      * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
      * @param instance_id
      * @param semester_name
      * @returns Promise<string>
      */
    var get_report_monitors_particants_and_students = function (instance_id, semester_name) {
        var data = {

            function: GET_COMPLETE_REPORT_NAME_FUNCTION_NAME,
            params: {
                instance_id: instance_id,
                semester_name: semester_name
            }
        };
      var data_stringfy = JSON.stringify(data);
      return $.ajax({
          url: api_url(),
          contentType: "application/csv",
          method: 'POST',
          data: data_stringfy
          }
      );
    };
    var get_instance_id = function () {
        return $("#monitor_assignments_instance_id").data("instance-id");
    } ;
     var get_semester_name = function () {
         return $("#monitor_assignments_instance_id").data("semester-name");
     } ;
     /**
      * Download the report using tricky dom properties.
      * The download occurs in the same window, not in a emergent window.
      * @author Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
      * @param instance_id
      * @param semester_name
      * @returns Promise<string>
      */
    var download_report_monitors_practicants_and_students = function (instance_id, semester_name) {
        loading_indicator.show();
         get_report_monitors_particants_and_students(instance_id, semester_name)
             .then( report => {
                 csv.csv_string_to_file_for_download(report, DOWNLOAD_FILE_NAME);
                 loading_indicator.hide();
             })
             .catch(error => {
                 loading_indicator.hide();
                 console.log(error);
         });

    };
    return {
        init: function() {
            $(BUTTON_GET_COMPLETE_REPORT_NAME_SELECTOR).click(function () {
                download_report_monitors_practicants_and_students(get_instance_id(), get_semester_name() );
            });

            function load_reverse_assignation( role, instance_id, data_id ){
              if( role === "monitor" ){

                loading_indicator.show();
                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_current_practicant_by_monitor", "params": [ instance_id, data_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        loading_indicator.hide();
                        if( data.status_code == 0 ){

                            if( data.data_response ){
                              load_assigned_monitors( instance_id, data.data_response, true );
                            }else{
                              setTimeout(function(){
                                swal(
                                  {
                                    title:'Información',
                                    text: 'El monitor seleccionado no tiene asignado un practicante.',
                                    type: 'info'
                                  },
                                  function(){}
                                );
                              }, 0);
                            }

                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                      loading_indicator.hide();
                      console.log(errMsg);
                    }
                });


              }else if( role === "student" ){

                loading_indicator.show();

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_current_monitor_by_student", "params": [ instance_id, data_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        loading_indicator.hide();
                        if( data.status_code == 0 ){

                            if( data.data_response ){
                              load_reverse_assignation( "monitor", instance_id, data.data_response );
                              load_assigned_students( instance_id, data.data_response );
                            }else{
                              setTimeout(function(){
                                swal(
                                  {
                                    title:'Información',
                                    text: 'El estudiante seleccionado no tiene asignado un monitor.',
                                    type: 'info'
                                  },
                                  function(){}
                                );
                              }, 0);
                            }
                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                      loading_indicator.hide();
                      console.log(errMsg);
                    }
                });

              }
            };


            /**
             * 
             * @param {Number} instance_id 
             * @param {Number} data_id monitor identificator
             */
            function load_assigned_students( instance_id, data_id ){
                $("#student_assigned").addClass("items_assigned_empty");
                $("#student_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".student_item").removeClass("oculto-asignado");
                $('#student_column').animate({
                    scrollTop: $('#student_column').scrollTop() + $('#student_assigned').position().top
                }, 0);
                loading_indicator.show();
                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_monitors_students_relationship_by_instance", "params": [ instance_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        loading_indicator.hide();
                        if( data.status_code == 0 ){

                            var monitor_assignments_monitor_students_relationship = data.data_response;
                            $(".monitor_item").removeClass("active");
                            $(".monitor_item[data-id='" + data_id + "']").addClass("active");
                            $(".student_item").find(".add").removeClass("oculto-asignar")
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");
                            $("#student_assigned").removeClass("items_assigned_empty");
                            $("#student_assigned").text("");
                            
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_monitor_students_relationship.length; i++ ){
                                if( monitor_assignments_monitor_students_relationship[i].id_monitor == data_id ){

                                    if( !elements ){
                                        elements = true;
                                        $("#student_assigned").removeClass("items_assigned_empty");
                                    }
                                    
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").removeClass("not-assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".add").addClass("oculto-asignar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").clone().appendTo("#student_assigned");

                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").not("#student_assigned .student_item").addClass("oculto-asignado");

                                }else{
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".add").addClass("oculto-asignar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".delete").addClass("oculto-eliminar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("oculto-asignado");
                                }
                            }

                            $("#student_assigned").find(".student_item").find(".add").addClass("oculto-asignar");
                            $("#student_assigned").find(".student_item").find(".delete").removeClass("oculto-eliminar");

                            if( !elements ){
                                $("#student_assigned").addClass("items_assigned_empty");
                                $("#student_assigned").text("No tiene estudiantes asignados.");
                            }
                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        loading_indicator.hide();
                        console.log(errMsg);
                    }
                });

            };

            /**
             * 
             * @param {Number} instance_id 
             * @param {Number} data_id practicant identificator
             */
            function load_assigned_monitors( instance_id, data_id, reversed = false ){
                loading_indicator.show();
                $("#monitor_assigned").addClass("items_assigned_empty");
                $("#monitor_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".monitor_item").removeClass("oculto-asignado");
                $('#monitor_column').animate({
                    scrollTop: $('#monitor_column').scrollTop() + $('#monitor_assigned').position().top
                }, 0);

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_practicant_monitor_relationship_by_instance", "params": [ instance_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        loading_indicator.hide();
                        if( data.status_code == 0 ){
                            var monitor_assignments_practicant_monitor_relationship = data.data_response;
                            $(".practicant_item").removeClass("active");
                            $(".practicant_item[data-id='" + data_id + "']").addClass("active");
                            $(".monitor_item").find(".add").removeClass("oculto-asignar")
                            $(".monitor_item").find(".transfer").removeClass("oculto-tranferir");
                            $(".monitor_item").removeClass("assigned");
                            $(".monitor_item").removeClass("not-assigned");
                            $(".monitor_item").addClass("not-assigned");
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");

                            if( !reversed ){
                              $("#student_assigned").text("No ha seleccionado un monitor.");
                              $("#student_assigned").addClass("items_assigned_empty");
                            }

                            $("#monitor_assigned").text("");
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_practicant_monitor_relationship.length; i++ ){
                                if( monitor_assignments_practicant_monitor_relationship[i].id_practicante == data_id ){
                                    
                                    if( !elements ){
                                        elements = true;
                                        $("#monitor_assigned").removeClass("items_assigned_empty");
                                    }

                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").removeClass("not-assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".add").addClass("oculto-asignar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").clone().appendTo("#monitor_assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").not("#monitor_assigned .monitor_item").addClass("oculto-asignado");
                                }else{
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".add").addClass("oculto-asignar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".delete").addClass("oculto-eliminar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("oculto-asignado");
                                }
                            }

                            $("#monitor_assigned").find(".monitor_item").find(".add").addClass("oculto-asignar");
                            $("#monitor_assigned").find(".monitor_item").find(".delete").removeClass("oculto-eliminar");
                            $(".monitor_item").not("#monitor_assigned .monitor_item").find(".transfer").addClass("oculto-tranferir");

                            if( !elements ){
                                $("#monitor_assigned").addClass("items_assigned_empty");
                                $("#monitor_assigned").text("No tiene monitores asignados.");
                            }

                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        loading_indicator.hide();
                        console.log(errMsg);
                    }
                });
            };

            $( "#student-name-filter" ).keyup(function() {
                $('.student_item').removeClass("oculto-filtro-nombre");
                var filter_value = $(this).val();
                if (filter_value != ""){
                    if(/\d/.test(filter_value)){
                        $('.student_item').not('.item-general-list.student_item[data-username*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    }else{
                        $('.student_item').not('.item-general-list.student_item[data-name*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    }
                    $(this).addClass("filter-active");
                }else{
                    $(this).removeClass("filter-active");
                }
            });

            $( "#monitor-name-filter" ).keyup(function() {
                $('.monitor_item').removeClass("oculto-filtro-nombre");
                var filter_value = $(this).val();
                if (filter_value != ""){
                    if(/\d/.test(filter_value)){
                        $('.monitor_item').not('.item-general-list.monitor_item[data-username*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    }else{
                        $('.monitor_item').not('.item-general-list.monitor_item[data-name*="' + filter_value.toUpperCase() + '"]').addClass("oculto-filtro-nombre");
                    }
                    $(this).addClass("filter-active");
                }else{
                    $(this).removeClass("filter-active");
                }
            });

            $("#btn-student-name-filter").click(function(){
                $("#student-name-filter").val("");
                $('.student_item').removeClass("oculto-filtro-nombre");
                $("#student-name-filter").removeClass("filter-active");
            });

            $("#btn-monitor-name-filter").click(function(){
                $("#monitor-name-filter").val("");
                $('.monitor_item').removeClass("oculto-filtro-nombre");
                $("#monitor-name-filter").removeClass("filter-active");
            });

            
            $(document).ready(function(){
                load_counters();
                $(".asign-select-filter").trigger( "change" );
            });

            $(document).on( 'click', '.practicant_item', function() {
                $(".monitor_item").removeClass("active");
                load_assigned_monitors( $("#monitor_assignments_instance_id").data("instance-id"), $(this).attr("data-id") );

            });

            $(document).on( 'click', '.monitor_item', function() {
                let instance_id = $("#monitor_assignments_instance_id").data("instance-id");
                let data_id = $(this).attr("data-id");

                load_assigned_students( instance_id , data_id  );
                loading_indicator.show();
                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_current_practicant_by_monitor", "params": [ instance_id, data_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        loading_indicator.hide();
                        if( data.status_code == 0 ){

                            if( data.data_response ){
                              load_assigned_monitors( instance_id, data.data_response, true );
                            }else{
                              
                              setTimeout(function(){
                                swal(
                                  {
                                    title:'Información',
                                    text: 'El monitor seleccionado no tiene asignado un practicante.',
                                    type: 'info'
                                  },
                                  function(){}
                                );
                              }, 0);
                            }

                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                      loading_indicator.hide();
                      console.log(errMsg);
                    }
                });


            });

            $(document).on( 'click', '.student_item', function(){
                var data_id = $(this).attr("data-id"); // student_id
                $(".student_item").removeClass("active");
                $(this).addClass("active");
                $(".student_item[data-id='" + data_id + "']").addClass("active");
                load_reverse_assignation( "student", $("#monitor_assignments_instance_id").data("instance-id") , data_id );
            });

            $(document).on( 'click', '.add', function(e) {

                e.stopImmediatePropagation();

                var current_item = $(this);
                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "create_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                    if( data_item_0 == null ){
                      setTimeout(function(){
                        swal(
                            {title:'Error',
                            text: 'Debe seleccionar primero a un monitor.',
                            type: 'error'},
                            function(){}
                        );
                      }, 0);
                      return;
                    }
                }else if( item_type == "monitor" ){
                    api_function = "create_practicant_monitor_relationship";
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                    if( data_item_0 == null ){
                      setTimeout(function(){
                        swal(
                            {title:'Error',
                            text: 'Debe seleccionar primero a un practicante.',
                            type: 'error'},
                            function(){}
                        );
                      }, 0);
                      return;
                    }
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación registrada correctamente.',
                                    type: 'success'},
                                    function(){
                                        if( item_type == "student" ){
                                            load_assigned_students( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }else if( item_type == "monitor" ){
                                            load_assigned_monitors( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === -5 ){

                            if( item_type == "student" ){
                                setTimeout(function(){
                                    swal(
                                        {title:'Información',
                                        text: 'La asignación ya existe en el periodo actual, si tiene problemas con esto, puede probar de nuevo recargando la pestaña.',
                                        type: 'info'},
                                        function(){}
                                    );
                                }, 0);
                            }else if( item_type == "monitor" ){
                                setTimeout(function(){
                                    swal(
                                        {title:'Información',
                                        text: 'Este monitor ya se encuentra asignado.',
                                        type: 'info'},
                                        function(){}
                                    );
                                }, 0);
                            }

                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                            }, 0);
                        }
                        
                        load_counters();
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on( 'click', '.delete', function(e) {

                e.stopImmediatePropagation();

                var current_item = $(this);
                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "delete_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                }else if( item_type == "monitor" ){
                    api_function = "delete_practicant_monitor_relationship";
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación eliminada correctamente',
                                    type: 'success'},
                                    function(){
                                        if( item_type == "student" ){
                                            load_assigned_students( instance_id , data_item_0 );
                                            item.find(".add").removeClass("oculto-asignar");
                                        }else if( item_type == "monitor" ){
                                            load_assigned_monitors( instance_id , data_item_0 );
                                            item.find(".add").addClass("oculto-asignar");
                                        }
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === 1 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Está intentando eliminar una asignación que ya no existe, si tiene problemas con esto, puede probar de nuevo recargando la pestaña.',
                                    type: 'info'},
                                    function(){}
                                );
                            }, 0);
                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                            }, 0);
                            console.log( data );
                        }
                        
                        load_counters();
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on( 'click', '.transfer', function(e) {

                e.stopImmediatePropagation();

                var name_original_monitor = $(this).parent().data("name");
                var id_old_monitor = $(this).parent().data("id");

                // Este metodo no funciona cuando se importa JQuery dos
                // veces en un archivo, no fue posible llegar al fondo
                // de esta doble importación, por ende el modal se muestra usando
                // data-toggle y data-target en monitor_assigments.mustache.
                // 2021/08/24
                //$('#modalTransfer').modal('show');
                $("#old_monitor_name").text(name_original_monitor);
                
                var options = '<option value="" disabled selected>Seleccione un monitor</option>\n';
                $("#monitor_assigned > .monitor_item").each(function(){
                    var name = $(this).data("name");
                    var id_new_monitor = $(this).data("id");
                    options += '<option data-old="' + id_old_monitor + '" data-new="' + id_new_monitor + '">' + name + '</option>\n';
                });

                $("#transfer-monitor-list").html("");
                $("#transfer-monitor-list").append( options );
                
            });

            $(document).on( 'click', '#btn-execute-transfer', function(e){

                $('#modalTransfer').modal('hide');

                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");
                var api_function = "transfer";
                var id_old_monitor = $("#transfer-monitor-list").find(":selected").data("old");
                var id_new_monitor = $("#transfer-monitor-list").find(":selected").data("new");

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id, id_old_monitor ,id_new_monitor ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Estudiantes transferidos correctamente.',
                                    type: 'success'},
                                    function(){
                                        load_assigned_students( instance_id, id_new_monitor );
                                    }
                                );
                            }, 0);
                        }else if( data.status_code === 1 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'El monitor no tiene estudiantes para transferir.',
                                    type: 'info'},
                                    function(){}
                                );
                            }, 0);
                        }else{
                            setTimeout(function(){
                                swal(
                                    {title:'Error',
                                    text: 'Reporte este error.',
                                    type: 'error'},
                                    function(){}
                                );
                                swal.close();
                            }, 0);
                            console.log( data );
                        }
                        
                        load_counters();
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });
                
                
            });

            $(".asign-select-filter").change(function(){

                var user_type = $(this).attr("data-id").split("_")[0]; // i.e monitor_faculty => monitor
                var filter_type = $(this).attr("data-id").split("_")[1]; // i.e monitor_faculty => faculty

                if( (user_type == "monitor") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "monitor") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "student") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "student") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "professional") ){
                    var boss_id = $(this).find(":selected").attr("data-id");
                    if( boss_id != "-1" ){
                        $(this).addClass("filter-active");
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                        $(".item-general-list.practicant_item").not(".item-general-list.practicant_item[data-id-jefe='" + boss_id + "']").addClass("oculto-jefe");
                    }else{
                        $(this).removeClass("filter-active");
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                    }
                    
                }
            });
            
            function load_counters(){
                
                asignation_counter = [];
                
                var instance_id = $("#monitor_assignments_instance_id").data("instance-id");
                var api_function = "get_practicants_monitors_and_students";
                var semester_name = get_semester_name();
                
                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ instance_id , semester_name ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        
                        total = 0;
                        
                        data.data_response.forEach( (item) => {
                            
                           if( asignation_counter[item.nombre_usuario_moodle__profesional] == undefined ){ 
                               asignation_counter[item.nombre_usuario_moodle__profesional] = 1; 
                           }else{
                               asignation_counter[item.nombre_usuario_moodle__profesional]++;
                           }
                           if( asignation_counter[item.codigo_practicante] == undefined ){ 
                               asignation_counter[item.codigo_practicante] = 1; 
                           }else{
                               asignation_counter[item.codigo_practicante]++;
                           }
                           if( asignation_counter[item.codigo_monitor] == undefined ){ 
                               asignation_counter[item.codigo_monitor] = 1; 
                           }else{
                               asignation_counter[item.codigo_monitor]++;
                           }
                           if( asignation_counter["TODOS"] == undefined ){ 
                               asignation_counter["TODOS"] = 1; 
                           }else{
                               asignation_counter["TODOS"]++;
                           }
                           
                        });
                        
                        $(".item-general-list").each( function(key) {
                            let index_id = $(this).data( "username" );
                            $(this).find( ".item-text" ).find( ".asignation_counter" ).text( asignation_counter[index_id] );
                        });
                        
                        $(".total_counter").text( asignation_counter["TODOS"] );
                        
                        var index_id = $("#select-professional").find(":selected").attr("data-username");
                        $(".asignation_counter_prof").text( asignation_counter[index_id] );
                        
                        $("#select-professional").find("option").each( function(key) {
                            let index_id = $(this).data( "username" );
                            let fullname = $(this).data( "fullname" );
                            $(this).text( fullname + " - " + "( " +  asignation_counter[index_id] + " )" );
                        });
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });
                
            }
            
        }
    };
});
