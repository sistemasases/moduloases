// Standard license block omitted.
/*
 * @package    block_ases/instanceconfiguration_main
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
 
 /**
  * @module block_ases/instanceconfiguration_main
  */

define(['jquery', 'block_ases/bootstrap', 'block_ases/datatables','block_ases/sweetalert'], function($, bootstrap, datatables, sweetalert) {

  return {
        init: function() {

            var cohort_to_assign = $('#select_cohorts').val();
            var instance_id = get_id_instance();
            
            $(document).ready(function(){
                load_cohorts_assigned(instance_id);
            });

            $('#button_assign_cohort').on('click', function(){
                assign_cohort_instance(cohort_to_assign, instance_id);
                load_cohorts_assigned(instance_id);
            });


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
                    data: {
                           function: 'load_cohorts',
                           instance: instance_id},
                    url: "../managers/instance_management/instance_configuration_serverproc.php",
                    success: function(msg) {
                        if(msg.status == 0){
                            $('#div_cohorts_table').html("<center><span>La instancia no tiene cohortes asignadas</span></center>");
                        }else{
                            $('#div_cohorts_table').html("<table id='cohorts_table' class='col-sm-12' style='width:100%'></table>");
                            $('#cohorts_table').DataTable(msg.msg);
                        }
                    },
                    dataType: "json",
                    cache: "false",
                    error: function(msg) {
                        console.log(msg);
                    },
                });
            }
        
            function unassign_cohort(){

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
        }
    };

 

    

});