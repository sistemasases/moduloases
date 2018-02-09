// Standard license block omitted.
/*
 * @package    block_ases/instanceconfiguration_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/instanceconfiguration_main
  */

define(['jquery','block_ases/sweetalert','block_ases/datatables'], function($,sweetalert,datatables) {


  return {
        init: function() {

            var cohort_to_assign = $('#select_cohorts').val();
            var instance_id = get_id_instance();
            $('#button_assign_cohort').on('click', function(){
                assign_cohort_instance(cohort_to_assign, instance_id);
            });
        }
    };

    function assign_cohort_instance(cohort_id, instance_id){

        $.ajax({
            type: "POST",
            data: { function: 'insert_cohort',
                    cohort: cohort_id,
                    instance: instance_id},
            url: "../managers/instance_management/instance_configuration_serverproc.php",
            success: function(msg) {
                if(msg.status = 0){
                    var title = 'Error';
                    var type = 'error';
                }else{
                    var title = 'Éxito';
                    var type = 'success'
                }
                swal(
                    title,
                    msg.msg,
                    type
                );
            },

            dataType: "json",
            cache: "false",
            error: function(msg){
                console.log(msg);
            },
        });
    }

    function load_cohorts_assigned(instance_id){

        $.ajax({
            type: "POST",
            data: {cohort: cohort_id,
                   instance: instance_id},
            url: "../managers/instance_management/instance_configuration_serverproc.php",
            success: function(msg) {
                console.log(msg);
            },
            dataType: "json",
            cache: "false",
            error: function(msg) {
                console.log(msg);
            },
        });
    }


    function get_id_instance() {
        var urlParameters = location.search.split('&');

        for (x in urlParameters) {
            if (urlParameters[x].indexOf('instanceid') >= 0) {
                var intanceparameter = urlParameters[x].split('=');
                return intanceparameter[1];
            }
        }
        return 0;
    }

    

});