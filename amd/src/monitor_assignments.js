// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/dphpforms_form_builder
  */

 define(['jquery', 'block_ases/bootstrap', 'block_ases/sweetalert', 'block_ases/jqueryui','block_ases/select2'], function($, bootstrap, sweetalert, jqueryui, select2) {
    
    return {
        init: function() {
            
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
                }
                
            });

        }
    };
})