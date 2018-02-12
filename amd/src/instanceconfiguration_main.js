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

            var instance_id = get_id_instance();

            $(document).ready(function(){
                load_cohorts_assigned(instance_id);
            });

            $('#button_assign_cohort').on('click', function(){
                var cohort_to_assign = $('#select_cohorts').val();
                assign_cohort_instance(cohort_to_assign, instance_id);
                load_cohorts_assigned(instance_id);
                get_cohorts_without_assignment(instance_id);
            });


            function assign_cohort_instance(cohort_id, instance_id){

                $.ajax({
                    type: "POST",
                    data: { function: 'insert_cohort',
                            cohort: cohort_id,
                            instance: instance_id},
                    url: "../managers/instance_management/instance_configuration_serverproc.php",
                    success: function(msg) {
                        if(msg.status == 0){
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
                    cache: false,
                    async: false,
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
                            $('#div_cohorts_table').html("<h4>Cohortes asignadas a la instancia</h4><hr/><table id='cohorts_table' class='col-sm-12' style='width:100%'></table>");
                            $('#cohorts_table').DataTable(msg.msg);
                        }
                    },
                    dataType: "json",
                    cache: false,
                    async: false,
                    error: function(msg) {
                        console.log(msg);
                    },
                });
            }
        
            function unassign_cohort(id_cohort){

                console.log('Unassigned');

            }

            function get_cohorts_without_assignment(instance_id){

                $.ajax({
                    type: "POST",
                    data: {
                           function: 'load_cohorts_without_assignment',
                           instance: instance_id},
                    url: "../managers/instance_management/instance_configuration_serverproc.php",
                    success: function(msg) {
                        if(msg.status == 0){
                            swal(
                                'Error',
                                msg.msg,
                                'error'
                            );
                        }else if(msg.status == 1){

                            var options = "";
                            var cohorts_array = msg.msg;

                            if(cohorts_array.length == 0){
                                options += "<option>No hay cohortes disponibles para asignar</option>";
                            }else{
                                $.each(cohorts_array, function(key){
                                    options += "<option value='"+cohorts_array[key].id+"'>";
                                    options += cohorts_array[key].idnumber+" "+cohorts_array[key].name+"</option>";
                                });
                            }
                            
                            $('#select_cohorts').html(options);

                        }else{
                            swal(
                                'Error',
                                'Error al cargar las cohortes no asignadas. Por favor recargue la página. Si el problema persiste contacte al área de sistemas.',
                                'error'
                            );
                        }
                    },
                    dataType: "json",
                    cache: false,
                    async: false,
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
        }
    };

 

    

});