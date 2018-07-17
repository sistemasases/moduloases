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

            var monitor_assignments_monitor_students;
            var monitor_assignments_professional_practicant;
            var monitor_assignments_practicant_monitor_relationship;

            $(document).ready(function(){
                monitor_assignments_monitor_students = JSON.parse( $("#monitor_assignments_monitor_students").text() );
                monitor_assignments_professional_practicant = JSON.parse( $("#monitor_assignments_professional_practicant").text() );
                monitor_assignments_practicant_monitor_relationship = JSON.parse( $("#monitor_assignments_practicant_monitor_relationship").text() );
            });

            // Loader of monitor_assignments_monitor_students
            $(".monitor_item").click(function(){
                var data_id = $(this).attr("data-id"); // id_monitor
                $(".student_item").removeClass("assigned");
                $(".student_item").removeClass("not-assigned");
                $(".student_item").addClass("not-assigned");
                $("#student_assigned").removeClass("items_assigned_empty");
                $("#student_assigned").text("");
                $('#student_column').animate({
                    scrollTop: $('#student_column').scrollTop() + $('#student_assigned').position().top
                }, 500);

                for( var i = 0; i < monitor_assignments_monitor_students.length; i++ ){
                    if( monitor_assignments_monitor_students[i].id_monitor == data_id ){
                        
                        $(".student_item[data-id='" + monitor_assignments_monitor_students[i].id_estudiante + "']").removeClass("not-assigned");
                        $(".student_item[data-id='" + monitor_assignments_monitor_students[i].id_estudiante + "']").addClass("assigned");
                        $(".student_item[data-id='" + monitor_assignments_monitor_students[i].id_estudiante + "']").clone().appendTo("#student_assigned");
                    }
                }
            });

            // Loader of monitor_assignments_practicant_monitor_relationship
            $(".practicant_item").click(function(){
                var data_id = $(this).attr("data-id"); // id_professional
                $(".monitor_item").removeClass("assigned");
                $(".monitor_item").removeClass("not-assigned");
                $(".monitor_item").addClass("not-assigned");
                $("#monitor_assigned").removeClass("items_assigned_empty");
                $("#monitor_assigned").text("");
                $('#monitor_column').animate({
                    scrollTop: $('#monitor_column').scrollTop() + $('#monitor_assigned').position().top
                }, 500);

                for( var i = 0; i < monitor_assignments_practicant_monitor_relationship.length; i++ ){
                    if( monitor_assignments_practicant_monitor_relationship[i].id_practicante == data_id ){
                        
                        $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").removeClass("not-assigned");
                        $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").addClass("assigned");
                        $(".monitor_item[data-id='" + monitor_assignments_practicant_monitor_relationship[i].id_monitor + "']").clone().appendTo("#monitor_assigned");
                    }
                }
            });
            
            $("select").change(function(){

                var user_type = $(this).attr("data-id").split("_")[0]; // i.e monitor_faculty => monitor
                var filter_type = $(this).attr("data-id").split("_")[1]; // i.e monitor_faculty => faculty

                if( (user_type == "monitor") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(".monitor_item").removeClass("oculto-facultad");
                        $(".monitor_item").not(".monitor_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(".monitor_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "monitor") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(".monitor_item").removeClass("oculto-programa");
                        $(".monitor_item").not(".monitor_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(".monitor_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "student") && (filter_type == "faculty") ){
                    var faculty_id = $(this).find(":selected").attr("data-id-facultad");
                    if( faculty_id != "-1" ){
                        $(".student_item").removeClass("oculto-facultad");
                        $(".student_item").not(".student_item[data-id-facultad='" + faculty_id + "']").addClass("oculto-facultad");
                    }else{
                        $(".student_item").removeClass("oculto-facultad");
                    }
                }else if( (user_type == "student") && (filter_type == "program") ){
                    var program_id = $(this).find(":selected").attr("data-cod-programa");
                    if( program_id != "-1" ){
                        $(".student_item").removeClass("oculto-programa");
                        $(".student_item").not(".student_item[data-cod-programa='" + program_id + "']").addClass("oculto-programa");
                    }else{
                        $(".student_item").removeClass("oculto-programa");
                    }
                }else if( (user_type == "professional") ){
                    var boss_id = $(this).find(":selected").attr("data-id");
                    if( boss_id != "-1" ){
                        $(".practicant_item").removeClass("oculto-jefe");
                        $(".practicant_item").not(".practicant_item[data-id-jefe='" + boss_id + "']").addClass("oculto-jefe");
                    }else{
                        $(".practicant_item").removeClass("oculto-jefe");
                    }
                }

            });
        }
    };
});