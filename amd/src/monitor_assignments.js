// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/monitor_assignments
  */

 define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {

            /**
             * data_id => id_monitor
            */
            function load_assigned_students( instance_id, data_id ){


                $("#student_assigned").addClass("items_assigned_empty");
                $("#student_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".student_item").removeClass("oculto-asignado");

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_monitors_students_relationship_by_instance", "params": [ instance_id ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code == 0 ){

                            var monitor_assignments_monitor_students_relationship = data.data_response;
                            $(".monitor_item").removeClass("active");
                            $(this).addClass("active");
                            $(".monitor_item[data-id='" + data_id + "']").addClass("active");
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");
                            $("#student_assigned").removeClass("items_assigned_empty");
                            $("#student_assigned").text("");
                            $('#student_column').animate({
                                scrollTop: $('#student_column').scrollTop() + $('#student_assigned').position().top
                            }, 500);
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_monitor_students_relationship.length; i++ ){
                                if( monitor_assignments_monitor_students_relationship[i].id_monitor == data_id ){

                                    if( !elements ){
                                        elements = true;
                                        $("#student_assigned").removeClass("items_assigned_empty");
                                    }
                                    
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").removeClass("not-assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("assigned");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").clone().appendTo("#student_assigned");

                                }else{
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".item-right-button.add").addClass("oculto-asignar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").find(".item-right-button.delete").addClass("oculto-eliminar");
                                    $(".student_item[data-id='" + monitor_assignments_monitor_students_relationship[i].id_estudiante + "']").addClass("oculto-asignado");
                                }
                            }

                            // Error cuando de carga con el filtro.
                            //$("#student_assigned").find(".student_item").removeClass("item-general-list");
                            $("#student_assigned").find(".student_item").find(".item-right-button.add").addClass("oculto-asignar");
                            $("#student_assigned").find(".student_item").find(".item-right-button.delete").removeClass("oculto-eliminar");

                            if( !elements ){
                                $("#student_assigned").addClass("items_assigned_empty");
                                $("#student_assigned").text("No tiene estudiantes asignados.");
                            }
                            
                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            };

            var monitor_assignments_professional_practicant;
            
            $(document).ready(function(){
                monitor_assignments_professional_practicant = JSON.parse( $("#monitor_assignments_professional_practicant").text() );
            });

            $(document).on('click', '.monitor_item', function() {

                var object_selected = $(this);
                load_assigned_students( 450299 ,object_selected.attr("data-id")  );
                
            });

            $(document).on( 'click', '.add', function() {

                var current_item = $(this);

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "monitor_assignments_create_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                }else if( item_type == "monitor" ){
                    api_function = ""; // Undefined
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ 450299, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación registrada correctamente.',
                                    type: 'success'},
                                    function(){}
                                );
                            }, 0);
                        }else if( data.status_code === -5 ){

                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'La asignación ya existe en el periodo actual, si tiene problemas con esto, puede probar de nuevo recargando la pestaña.',
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
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on( 'click', '.delete', function() {

                var current_item = $(this);

                var item = $(this).parent();
                var data_item_0 = -1; // monitor_id or practicant_id
                var data_item_1 = item.attr("data-id");
                var item_type = item.attr("data-item");

                var api_function = "";

                if( item_type == "student" ){
                    api_function = "monitor_assignments_delete_monitor_student_relationship";
                    data_item_0 =  $(".monitor_item.active").attr("data-id");
                }else if( item_type == "monitor" ){
                    api_function = ""; // Undefined
                    data_item_0 =  $(".practicant_item.active").attr("data-id");
                }

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": api_function, "params": [ 450299, data_item_0 ,data_item_1 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code === 0 ){
                            setTimeout(function(){
                                swal(
                                    {title:'Información',
                                    text: 'Asignación eliminada correctamente',
                                    type: 'success'},
                                    function(){}
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
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });

            });

            $(document).on('click', '.practicant_item', function() {

                var object_selected = $(this);
                $("#monitor_assigned").addClass("items_assigned_empty");
                $("#monitor_assigned").html("Consultando <span>.</span><span>.</span><span>.</span>");
                $(".monitor_item").removeClass("oculto-asignado");

                $.ajax({
                    type: "POST",
                    url: "../managers/monitor_assignments/monitor_assignments_api.php",
                    data: JSON.stringify({ "function": "get_practicant_monitor_relationship_by_instance", "params": [ 450299 ] }),
                    contentType: "application/json; charset=utf-8",
                    dataType: "json",
                    success: function(data){
                        if( data.status_code == 0 ){
                            var monitor_assignments_practicant_monitor_relationship = data.data_response;
                            var data_id = object_selected.attr("data-id"); // id_practicante
                            $(".practicant_item").removeClass("active");
                            object_selected.addClass("active");
                            $(".monitor_item").removeClass("assigned");
                            $(".monitor_item").removeClass("not-assigned");
                            $(".monitor_item").addClass("not-assigned");
                            $(".student_item").removeClass("assigned");
                            $(".student_item").removeClass("not-assigned");
                            $(".student_item").addClass("not-assigned");
                            $("#student_assigned").text("No ha seleccionado un monitor.");
                            $("#student_assigned").addClass("items_assigned_empty");
                            $("#monitor_assigned").text("");
                            $('#monitor_column').animate({
                                scrollTop: $('#monitor_column').scrollTop() + $('#monitor_assigned').position().top
                            }, 500);
                            var elements = false;
                            for( var i = 0; i < monitor_assignments_practicant_monitor_relationship.length; i++ ){
                                if( monitor_assignments_practicant_monitor_relationship[i].id_practicante == data_id ){
                                    
                                    if( !elements ){
                                        elements = true;
                                        $("#monitor_assigned").removeClass("items_assigned_empty");
                                    }

                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").removeClass("not-assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("assigned");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").clone().appendTo("#monitor_assigned");
                                }else{
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".item-right-button.add").addClass("oculto-asignar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").find(".item-right-button.delete").addClass("oculto-eliminar");
                                    $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("oculto-asignado");
                                }
                            }

                            //$("#monitor_assigned").find(".monitor_item").removeClass("item-general-list");
                            $("#monitor_assigned").find(".monitor_item").find(".item-right-button.add").addClass("oculto-asignar");
                            $("#monitor_assigned").find(".monitor_item").find(".item-right-button.delete").removeClass("oculto-eliminar");

                            if( !elements ){
                                $("#monitor_assigned").addClass("items_assigned_empty");
                                $("#monitor_assigned").text("No tiene monitores asignados.");
                            }

                        }else{
                            console.log( data );
                        }
                    },
                    failure: function(errMsg) {
                        console.log(errMsg);
                    }
                });
            });

            $(document).on('click', '.student_item', function(){
                var data_id = $(this).attr("data-id"); // student_id
                $(".student_item").removeClass("active");
                $(this).addClass("active");
                $(".student_item[data-id='" + data_id + "']").addClass("active");
            });

            
            $("select").change(function(){

                var user_type = $(this).attr("data-id").split("_")[0]; // i.e monitor_faculty => monitor
                var filter_type = $(this).attr("data-id").split("_")[1]; // i.e monitor_faculty => faculty

                if( (user_type == "monitor") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(".item-general-list.monitor_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "monitor") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                        $(".item-general-list.monitor_item").not(".item-general-list.monitor_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(".item-general-list.monitor_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "student") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(".item-general-list.student_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "student") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                        $(".item-general-list.student_item").not(".item-general-list.student_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(".item-general-list.student_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "professional") ){
                    var boss_id = $(this).find(":selected").attr("data-id");
                    if( boss_id != "-1" ){
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                        $(".item-general-list.practicant_item").not(".item-general-list.practicant_item[data-id-jefe='" + boss_id + "']").addClass("oculto-jefe");
                    }else{
                        $(".item-general-list.practicant_item").removeClass("oculto-jefe");
                    }
                }
            });
        }
    };
});