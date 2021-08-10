// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/student_new_register
 */
 define(['jquery','block_ases/bootstrap','block_ases/tagging', 'block_ases/smart_wizard', 'block_ases/mustache'],
 function($,bootstrap, tagging, smart_wizard, mustache){
     return {
         init: function() {
            $(document).ready(function() {

            var input_deportes;
            var input_actividades;
            var id_moodle;

            //Modal controls
            $('#mostrar').on('click', function () {
                var selects=$(".select-registro").map(function(){return $(this).attr("id");}).get()

                //Secciòn 1
                loadSelector($('#select_ciudad_nacimiento'), "get_ciudades");
                loadSelector($('#select_acompañamientos'), "get_otros_acompañamientos",);
                loadSelector($('#s_excepcion'), "get_cond_excepcion",);
                loadSelector($('#select_estado_civil'), "get_estados_civiles");
                loadSelector($('#select_sexo'), 'get_sex_options');
                loadSelector($('#select_genero'), 'get_generos');
                loadSelector($('#s_act_simultanea'), 'get_act_simultaneas');
                loadSelector($('#select_etnia'), 'get_etnias');
                loadSelector($('#s_tipo_documento'), 'get_document_types');
                loadSelector($('#discapacidad'), 'get_discapacities');


                //Secciòn 2
                loadSelector($('#select_pais_nacimiento'), 'get_paises');
                loadSelector($('#select_barrio'), 'get_barrios');
                loadSelector($('#select_ciudad'), 'get_ciudades');
                loadSelector($('#select_programa'), 'get_programas_academicos');

                //Secciòn 3
                loadSelector($('#select_pais_res'), 'get_paises');
                loadSelector($('#select_barrio_procedencia'), 'get_barrios');
                loadSelector($('#select_ciudad_procedencia'), 'get_ciudades');
                loadSelector($('#s_tipo_documento_ingreso'), 'get_document_types');
                loadSelector($('#select_sede'), 'get_sedes');

                loadSelector($('#s_programa_1'), 'get_programas_academicos');
                loadSelector($('#s_programa_2'), 'get_programas_academicos');
                loadSelector($('#s_programa_3'), 'get_programas_academicos');
                loadSelector($('#s_programa_4'), 'get_programas_academicos');

                //controls radio
                hideAndShow("set-permanencia", "option");
                hideAndShow("set-desplazamiento", "option");
                hideAndShow("set-certificado-discapacidad", "option");
                hideAndShow("set-apoyo", "option");
                hideAndShow("set-participacion", "option");
                hideAndShow("set-salud", "option");
                hideAndShow('set-orientacion', "orientacion_sexual");
                hideAndShow('set-sexo', "sexo");
                hideAndShow('set-identidad-gen', "identidad_genero");
                hideAndShow('set-beca', 'beca')

                //Habilitar tagging 
                input_deportes =  new Tagging($("#deportes_tag"), $("#tags_deportes"),1,3);
                input_deportes.createTag();
                
                input_actividades =  new Tagging($("#tiempo_libre"), $("#tags_tiempo_libre"));
                input_actividades.createTag();
                $('#modalExample').show();
            });

            
            $('.closer').on('click', function () {
                $('#modalExample').hide();
            });


            //Inicializar el modal mediante steps con el tema dots

            $('#smartwizard').smartWizard({
                selected: 0,
                theme: 'dots',
                autoAdjustHeight:true,
                showStepURLhash: false, 
                lang:{
                    next: 'Siguiente',
                    previous: 'Anterior'
                }
                });


            $(document).on('click', '.remove_fila', function () {
                    $(this).parent().parent().remove();
                });

                $("#add_person_r").on('click', function(){
                    addTable($("#table_familia"));
                });

                $(document).on('click', '#guardar_info', function () {

                    //Recolectar valores de los selects
                    var selects=$(".select-registro").map(function(){return $(this).attr("id");}).get()
                    var inputs=$(".select-registro").map(function(){return $(this).next().attr("id")}).get()
                    for(var i=0; i < selects.length; i++){
                        $("#"+inputs[i]).val($("#"+selects[i]+" option:selected").val());
                    }

                    getStudent("code"+$("#codigo_estudiantil").val());

                save_data();
                });

                /*Controles para el registros de Familiares*/
                function addTable(table){
                    let nuevaFila = "";
                    nuevaFila += '<tr><td> <input  class="input_fields_general_tab"  type="text"/></td>';
                    nuevaFila += '<td> <input  class="input_fields_general_tab"  type="text" /></td>';
                    nuevaFila += '<td> <button class="btn btn-danger remove_fila" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                    table.find("tbody").append(nuevaFila);
                }

                //Generar un arreglo apartir de la tabla-familia
                function buildArr(table){
                    var arr=[];
                    var values = table.find("tbody input").map(function(){return $(this).val();}).get();
                    for(var i=0; i < values.length; i=i+2){
                    var f= {
                        nombre : values[i],
                        rol : values[i+1]
                    }
                    arr.push(f)
                    }
                    return arr;
                }
                
                //Funciòn que obtiene un estudiante mediante el codigo
                function getStudent(code){
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_user',
                            "params": code
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var options=JSON.parse(msg);
                            id_moodle = options.id;
                        },
                        error: function (msg) {
                            swal(
                                "Error",
                                "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                "error"
                            );
                        },

                    });
                }
            

                //Funciòn que carga los selectores, para el registro de un nuevo estudiante a acompañar
                function loadSelector(select, f){

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": f,
                            "params": "1"
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var options=JSON.parse(msg);
                        for (var o in options){
                        select.append(
                            "<option value='"+options[o].id+"'>"+Object.values(options[o])[1]+"</option>"
                            )
                        }
                        
                        },
                        error: function (msg) {
                            swal(
                                "Error",
                                "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                "error"
                            );
                        },

                    });
                }

                function hideAndShow(setOptions, nameRadio){
                    $("#"+setOptions).find("[name="+nameRadio+"]").on('click', function(){
                    
                    var s=$("#"+setOptions + " .otro").parent().next();
                    
                    if($(this).hasClass("otro")){
                        s.attr('hidden', false);
                    }else{
                        s.attr('hidden', true);
                    }
                    })
                    }

                function save_data(){
                    var deportes = input_deportes.getArr();
                    var a=$("#modalExample .modal-body :input").serializeArray();
                    var familia = buildArr($("#table_familia"));
                    var programa = $("#programa").val();
                    //console.log(a)
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data',
                            "params": [a, deportes, familia, programa,id_moodle]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        // console.log(msg);
                        
                        },
                        
                
                        error: function (msg) {
                            swal(
                                "Error",
                                "Error al comunicarse con el servidor, por favor inténtelo nuevamente.",
                                "error"
                            );
                        },

                    });
                }

            })
         }
     }
 }
 
 );

