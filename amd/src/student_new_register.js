// Standard license block omitted.
/*
 * @package    block_ases
 * @copyright  ASES
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module block_ases/student_new_register
 */
 define(['jquery','block_ases/bootstrap','block_ases/tagging', 'block_ases/smart_wizard', 'block_ases/mustache', 'block_ases/sweetalert'],
 function($,bootstrap, tagging, smart_wizard, mustache){
     return {
         init: function() {
        $(document).ready(function() {

            var input_deportes;
            var input_actividades;
            var id_moodle = "";
            var id_ases = "";
            var data_ases = "";
            var id_economics_data ="";
            var cod_programa = "";
            //Banderas para las validaciones al guardar
            var mdl_user, ases_user, user_extended = false;
            var economics_data, academics_data = false;
            var healthCondition, healthService, discapacity = false;
            var cambios_s1, cambios_s2, cambios_s3, cambios_s4, cambios_s5, cambios_s6 = false;

            $("#step-1 :input").prop("disabled", true);
            $("#codigo_estudiantil").prop("disabled", false);
            $("#validar_codigo").prop("disabled", false);

            $('#pruebas').on('click', function () {
                $(":input").val("");
                $('#smartwizard').smartWizard("reset");
            });

            //Modal controls
            $('#mostrar').on('click', function () {
                //Secciòn 1
                loadSelector($('#id_ciudad_ini'), "get_ciudades");
                loadSelector($('#acompanamientos'), "get_otros_acompañamientos",);
                loadSelector($('#id_cond_excepcion'), "get_cond_excepcion",);
                loadSelector($('#id_estado_civil'), "get_estados_civiles");
                loadSelector($('#sexo'), 'get_sex_options');
                loadSelector($('#id_identidad_gen'), 'get_generos');
                loadSelector($('#id_act_simultanea'), 'get_act_simultaneas');
                loadSelector($('#id_etnia'), 'get_etnias');
                loadSelector($('#tipo_doc'), 'get_document_types');
                loadSelector($('#s_discapacidad'), 'get_discapacities');


                //Secciòn 2
                loadSelector($('#id_pais'), 'get_paises');
                loadSelector($('#barrio_res'), 'get_barrios');
                loadSelector($('#id_ciudad_res'), 'get_ciudades');
                setSelectPrograma();

                //Secciòn 3
                loadSelector($('#id_pais_res'), 'get_paises');
                loadSelector($('#barrio_ini'), 'get_barrios');
                loadSelector($('#tipo_doc_ini'), 'get_document_types');
                loadSelector($('#select_sede'), 'get_sedes');

                loadSelector($('#s_programa_1'), 'get_programas_academicos');
                loadSelector($('#s_programa_2'), 'get_programas_academicos');
                loadSelector($('#s_programa_3'), 'get_programas_academicos');
                loadSelector($('#s_programa_4'), 'get_programas_academicos');

                //controls radio
                hideAndShow("permanencia", "solvencia_econo");
                hideAndShow("set-desplazamiento", "ayuda_transporte");
                hideAndShow("set-certificado-discapacidad", "discapacidad");
                hideAndShow("set-apoyo", "apoyo_partic");
                hideAndShow("set-participacion", "participacion");
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

            function setSelectPrograma() {
                loadSelector($('#select_programa'), 'get_programas_academicos');
            }

            //Ocultar el modal
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


             //Funcion para obtener los datos del usuario al digitar el codigo
            $('#validar_codigo').on('click', function () {
                var codeUser = $("#codigo_estudiantil").val();
                if (codeUser === "" || codeUser === " ") {
                    $('#nombre').val("");
                    $('#apellido').val("");
                    $('#emailinstitucional').val("");
                    $("#step-1 :input").prop("disabled", true);
                    $("#codigo_estudiantil").prop("disabled", false);
                    $("#validar_codigo").prop("disabled", false);
                    setSelectPrograma();
                } else {
                    $.ajax({
                        async: false,
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_user',
                            "params": codeUser
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var fullUser = JSON.parse(msg);
                        validateStudent(fullUser);
                            
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
            });

            /* Funcion para validar si el la consulta del usuario en la tabla mdl_user existe
                En caso de no existir se determina si se creara un estudiante nuevo o se ingresara otro codigo*/
            function validateStudent(fullUser) {
                if (fullUser != false) {
                    $("#step-1 :input").prop("disabled", true);
                    $("#codigo_estudiantil").prop("disabled", false);
                    $("#validar_codigo").prop("disabled", false);
                    $("#tipo_doc_ini").prop("disabled", false); 
                    $("#num_doc_ini").prop("disabled", false); 
                    $("#select_sede").prop("disabled", false); 
                    $("#select_programa").prop("disabled", false); 
                    setUserData(fullUser);
                    ases_user = false;
                    mdl_user = false;
                    
               }else {
                   swal({
                       title: "Warning",
                       text: "Estudiante inexistente en la base de datos, ¿Esta seguro de registrar este usuario?.",
                       type: "warning",
                       showCancelButton: true,
                       confirmButtonClass: "btn-success",
                       confirmButtonText: "Confirmar",
                       cancelButtonText: "Cancelar",
                       closeOnConfirm: false,
                       closeOnCancel: false
                     },
                     function(isConfirm) {
                       if (isConfirm) {
                         swal("Confirmado","", "success");
                         mdl_user = true;
                         ases_user = false;
                         disableMdliputs();
                       } else {
                         swal("Cancelado", "Ingresa nuevamente el codigo del estudiante", "error");
                         $("#codigo_estudiantil").val("");
                       }
                     });
               }
                
            }
            
            /* Funcion para setear los datos del estudiante en caso de existir en la tabla mdl_user 
                y cargar el programa academico al que pertenece*/
            function setUserData(fullUser) {
                $('#nombre').val(fullUser.firstname);
                $('#apellido').val(fullUser.lastname);
                $('#emailinstitucional').val(fullUser.email);
                disableMdliputs();

                programs = fullUser.username.slice(fullUser.username.indexOf('-')+1);
                $('#select_programa').empty();
                loadSelector($('#select_programa'), 'get_programas_academicos_est', programs);
            }

            //Funcion para deshabilitar los campos o habilitar los campos de la seccion 1 del formulario
            function disableMdliputs() {
                if (!mdl_user) {
                    $('#nombre').prop("disabled",true);
                    $('#apellido').prop("disabled",true);
                    $('#emailinstitucional').prop("disabled",true);
                }else {
                    $("#step-1 :input").prop("disabled", false);
                }

                $('#edad').prop("disabled",true);
            }
            
            $( "#fecha_nac" ).blur(function() {
                var fecha = $('#fecha_nac').val();
                var hoy = new Date();
                var cumpleanos = new Date(fecha);
                var edad = hoy.getFullYear() - cumpleanos.getFullYear();
                var m = hoy.getMonth() - cumpleanos.getMonth();
            
                if (m < 0 || (m === 0 && hoy.getDate() < cumpleanos.getDate())) {
                    edad--;
                }
                $('#edad').val(edad);
            });

            /*Funciones para añadir campos a la tabla de familia y a la tabla de ingresos a 
                la universidad en las secciones 2 y 3 del formulario*/
                $(document).on('click', '.remove_fila', function () {
                        $(this).parent().parent().remove();
                    });

                $(document).on('click', '.remove_fila_ing', function () {
                    ing--;
                    $(this).parent().parent().remove();
                });

                $("#add_person_r").on('click', function(){
                    addTable($("#table_familia"));
                });

                $("#add_ingreso").on('click', function(){
                    ing++;
                    addTableIng($("#table_ingresos"));
                });

                //Funciòn para guardar la informaciòn de servicios de salud
                $(document).on('click', '#guardar_info', function () {

                    if (!healthService) {
                        save_health_service();
                        healthService = true;
                        cambios_s6 = false;
                        $(":input").val("");
                        $('#smartwizard').smartWizard("reset");
                    } else if(cambios_s6) {
                        cambios_s6 = false;
                    }
                });

            
                /*Controles para el registros de Familiares*/
                function addTable(table){
                    let nuevaFila = "";
                    nuevaFila += '<tr><td> <input  class="input_fields_general_tab"  type="text"/></td>';
                    nuevaFila += '<td> <input  class="input_fields_general_tab"  type="text" /></td>';
                    nuevaFila += '<td> <button class="btn btn-danger remove_fila" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                    table.find("tbody").append(nuevaFila);
                }

                var ing = 0;
                /*Controles para el registros de inresos a la universidad*/
                function addTableIng(table){
                    let nuevaFila = "";
                    nuevaFila += '<tr><td> <input id="anio_'+ing+'" class="input_fields_general_tab ingresos_u step3" name="anio_'+ing+'"  type="text"/></td>';
                    nuevaFila += '<td> <select id="s_programa_'+ing+'" class="custom-select select-academics-data step3"></select> <input  id="id_programa_'+ing+'" name="id_programa_'+ing+'" class="ingresos_u" type="number" hidden></td>';
                    nuevaFila += '<td><select class="custom-select select-academics-data step3"> <option value="1">Bajos académicos</option> <option value="2">Condición de salud</option> <option value="3">Fallecimiento</option> <option value="4">Condición económica</option> <option value="5">Condición de programa académico</option> <option value="6">Cambio de institución educativa</option> <option value="7">Cambio de ciudad</option> <option value="8">Retiro voluntario</option> <option value="9">Prefiero no decirlo</option> </select> <input  id="motivo_'+ing+'" name="motivo_'+ing+'" class="ingresos_u" type="number" hidden> </td>';
                    nuevaFila += '<td> <button class="btn btn-danger remove_fila_ing" type="button" title="Eliminar persona" name="btn_delete_person" style="visibility:visible;"> X </button></td></tr>';
                    table.find("tbody").append(nuevaFila);
                    loadSelector($('#s_programa_'+ing+''), 'get_programas_academicos');
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
                
                /*Funcion para determinar si el usuario existe en la tabla talentospilos_usuario
                    y talentospilos_user_extended mediante el numero de cedula*/
                $( "#num_doc_ini" ).blur(function() { 
                    getStudentAses($( "#num_doc_ini" ).val());
                    $("#step-1 :input").prop("disabled", false);
                    if (id_ases != null) {
                        getStudent($("#codigo_estudiantil").val());
                        getExistUserExtended(id_ases, id_moodle);
                        if (user_extended) {
                            disableMdliputs();
                            ases_user = true;
                            getStudentAses($( "#num_doc_ini" ).val(), 1);
                            setAsesData(data_ases)
                        } else {
                            disableMdliputs();
                            console.log("no esta en user extended");
                        }
                    }else{
                        console.log("No existe");
                        $("#step-1 :input").prop("disabled", false);
                        disableMdliputs();
                    }
                });

                function getExistUserExtended(id, id_moodle) {
                    $.ajax({
                        async: false,
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_exist_user_extended',
                            "params": [id, id_moodle]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                            
                            user_extended = JSON.parse(msg);
                            
                            
                            
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
                
                //Funciòn que obtiene un estudiante mediante la cedula
                //Nota: en este caso solo retorna el id
                function getStudentAses(code,data){
                    $.ajax({
                        async: false,
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_ases_user_id',
                            "params": code
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var options=JSON.parse(msg);
                            if (data == null) {
                                handleIdAses(options.id);
                            } else if(data == 1) {
                                handleDataAses(options);
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
                //Funciòn para poder retornar desde una peticòn AJAX
                function handleIdAses(data){
                   id_ases = data;
                   
                } 

                //Funciòn para poder retornar desde una peticòn AJAX
                function handleDataAses(data){
                   data_ases = data;
                } 

                function setAsesData(data) {
                    for(var key in data){
                        $('#'+key).val(data[key])
                    }
                }
                
                //Funciòn que obtiene un estudiante mediante el codigo
                //Nota: en este caso solo retorna el id
                function getStudent(code){
                    $.ajax({
                        async: false,
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_user',
                            "params": code
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var options=JSON.parse(msg);

                            handleData(options.id);
                            
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
                //Funciòn para poder retornar desde una peticòn AJAX
                function handleData(data){
                   id_moodle = data;
                } 

                //Funciòn que obtiene el programa academico mediante el id
                //Nota: en este caso solo retorna el codigo de programa
                function getAcademicProgram(id){
                    $.ajax({
                        async: false,
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'get_program',
                            "params": id
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                        var options=JSON.parse(msg);

                            handleProgram(options.cod_univalle);
                            
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
                //Funciòn para poder retornar desde una peticòn AJAX
                function handleProgram(data){
                   cod_programa = data;
                } 
                //Funciòn que carga los selectores, para el registro de un nuevo estudiante a acompañar
                //select, hace referencia al id del select dònde se cargarà la info
                //f, hace referencia al nombre de la funciòn definida en studentprofile_main.php, que recuperarà la info
                function loadSelector(select, f, prog_act){
                    var param = "1";
                    if (prog_act != null) {
                        param = prog_act;
                    }

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": f,
                            "params": param
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

                function getHealtService() {
                    var arr_service_disca = [];
                    var arr_insert = [];

                    buildJsonObject('regimen_salud', arr_insert);
                    buildJson('regimen_salud', arr_insert, arr_service_disca);
                    arr_insert = [];

                    buildJsonObject('data_sisben', arr_insert);
                    buildJsonTArea('observ_sisben', arr_insert);

                    buildJsonObject('serv_adicionales', arr_insert);
                    buildJsonObject('conclusion_jornada', arr_insert);
                    buildJsonTArea('conclusion', arr_insert);

                    buildJsonObject('json_detalle', arr_insert);
                    buildJson('datos_salud_adicionales', arr_insert, arr_service_disca);
                    arr_insert = [];

                    buildJsonObject('serv_usuario', arr_insert);
                    buildJson('servicios_usados', arr_insert, arr_service_disca);
                    arr_insert = [];



                    return arr_service_disca;
                }
                


                function getHealtData() {
                    var arr_discapacidad = [];
                    var arr_insert = [];

                    buildJsonObject('percepcion_discapacidad', arr_insert);
                    buildJsonTArea('desc_discap', arr_insert); 
                    buildJson('percepcion_disca', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('diagnosticos', arr_insert);
                    buildJson('diagnosticos', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('condicion_salud', arr_insert);
                    buildJsonTArea('rel_cond_salud', arr_insert);
                    buildJson('condicion_salud', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('medicamentos', arr_insert);
                    buildJson('medicamentos', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('cambio_tratamiento', arr_insert);
                    buildJsonTArea('obs_cambio', arr_insert);
                    buildJson('cambios_tratamiento', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('disca_por_municipio', arr_insert);
                    buildJsonTArea('doc_localizacion', arr_insert);
                    buildJson('discapacidad_municipio', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('certificado_disca', arr_insert);
                    buildJsonTArea('certi_disca', arr_insert);
                    buildJson('certificado_discapacidad', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('datos_invalidez', arr_insert);
                    buildJson('certificado_invalidez', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('cond_org_sist', arr_insert);
                    buildJson('condicion_organos', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('dif_permanente', arr_insert);
                    buildJson('dificultad_permanente', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('set-participacion', arr_insert);
                    buildJsonTArea('text_participacion', arr_insert);
                    buildJsonObject('set-apoyo', arr_insert);
                    buildJsonTArea('text_apoyo_partic', arr_insert);
                    buildJson('participacion_estudiantil', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('asoc_disca', arr_insert);
                    buildJson('organizacion_disca', arr_insert, arr_discapacidad);
                    arr_insert = [];

                    buildJsonObject('apoyo_recibido', arr_insert);
                    buildJson('apoyo_dicapacidad', arr_insert, arr_discapacidad);
                    arr_insert = [];



                    return arr_discapacidad;
                }

                // Funciòn que recolecta los datos acadèmicos y los empaqueta en un arr
                function getAcademicsData(){

                    var json_insert = {};

                    //Recolectar valores de los selects
                    var selects=$(".select-academics-data").map(function(){return $(this).attr("id");}).get()
                    var inputs=$(".select-academics-data").map(function(){return $(this).next().attr("id")}).get()
                    for(var i=0; i < inputs.length; i++){
                        
                        $("#"+inputs[i]).val($("#"+selects[i]+" option:selected").val());
                        
                    }

                    var arr_academic = [];
                    var arr_estudios_realizados = [];

                    var arr_dificultades = [];
                    buildJsonObject('dif_educ_media', arr_dificultades);
                    buildJsonObject('dif_educ_superior', arr_dificultades);
                    buildJsonObject('dif_educ_paralelo', arr_dificultades);
                    json_insert.key_input = "dificultades"
                    json_insert.val_input = arr_dificultades;
                    arr_dificultades =  [];
                    arr_dificultades.push(json_insert);
                    json_insert = {};

                    var datos_adicionales = [];
                    var add_data = $(".ingresos_u").serializeArray();
                    buildJsonObject('jornada', add_data);
                    buildJsonObject('p_ingreso', add_data);
                    buildJsonObject('sede', add_data);
                    buildJson('datos_academicos_adicionales', add_data, datos_adicionales);


                    buildJsonObject('div_tipo_institucion', arr_estudios_realizados);
                    buildJsonObject('div_tipo_institucion_superior', arr_estudios_realizados);
                    buildJsonObject('div_tipo_institucion_paralelo', arr_estudios_realizados);
                    json_insert.key_input = "otras_instituciones"
                    json_insert.val_input = arr_estudios_realizados;
                    arr_estudios_realizados =  [];
                    arr_estudios_realizados.push(json_insert);
                    json_insert = {};


                    arr_academic.push(datos_adicionales);
                    arr_academic.push(arr_estudios_realizados);
                    arr_academic.push(arr_dificultades);
                    return arr_academic;
                }
                // Funciòn que recolecta los datos socio-econòmicos y los empaqueta en un arr
                function getEconomicsData(){
                    var arr = [];
                    var arr_add = [];
                    var nivel_educ_padres = [];
                    var ocupacion_padres =[];
                    var situa_laboral_padres = [];
                    var arr_proyecto_vida = [];
                    var arr_dat_adicionales = [];

                    var json_proyecto = {};
                    var json_educ_padres = {};
                    var json_ocupacion_padres = {};
                    var json_laboral_padres = {};

                    buildJsonObject('set-sostenimiento', arr_dat_adicionales);
                    buildJsonObject('otro_ingreso', arr_dat_adicionales);
                    buildJson('datos_economicos_adicionales', arr_dat_adicionales, arr);

                    buildJsonObject('set-permanencia', arr);
                    buildJsonObject('set-beca', arr);

                    buildJsonObject('set-materiales', arr_add);
                    buildJsonObject('set-valor-materiales', arr_add);
                    arr.push(arr_add);
                    arr_add = [];

                    buildJsonObject('set-desplazamiento', arr_add);
                    buildJsonObject('set-valor-desplazamiento', arr_add);
                    arr.push(arr_add);
                    
                    buildJsonObject('row_madre_academic', nivel_educ_padres);
                    buildJsonObject('row_padre_academic', nivel_educ_padres);
                    buildJsonObject('row_otro_academic', nivel_educ_padres);

                    buildJsonObject('row_madre_ocupacion',ocupacion_padres);
                    buildJsonObject('row_padre_ocupacion',ocupacion_padres);
                    buildJsonObject('row_otro_ocupacion',ocupacion_padres);

                    buildJsonObject('row_madre_laboral', situa_laboral_padres);
                    buildJsonObject('row_padre_laboral', situa_laboral_padres);
                    buildJsonObject('row_otro_laboral', situa_laboral_padres);

                    buildJsonTArea('motivo_ingreso', arr_proyecto_vida);
                    buildJsonTArea('expecativas_carrera', arr_proyecto_vida);
                    buildJsonTArea('expectativas_graduarse', arr_proyecto_vida);

                    json_proyecto.key_input = "expectativas_laborales";
                    json_proyecto.val_input = arr_proyecto_vida;

                    json_ocupacion_padres.key_input = "ocupacion_padres";
                    json_ocupacion_padres.val_input = ocupacion_padres;

                    json_educ_padres.key_input = "nivel_educ_padres";
                    json_educ_padres.val_input = nivel_educ_padres;

                    json_laboral_padres.key_input = "situa_laboral_padres";
                    json_laboral_padres.val_input = situa_laboral_padres;

                    arr.push(json_educ_padres);
                    arr.push(json_ocupacion_padres);
                    arr.push(json_laboral_padres);
                    arr.push(json_proyecto);

                    return arr;
                   
                }

                //Funcion para crear el json a partir de los TextArea
                function buildJsonTArea(id_text, arr) {
                    json_insert = {};
                    
                    if ($("#"+id_text).val().length >= 3) {
                        
                        json_insert.key_input_text = $("#"+id_text).attr("id");
                        json_insert.val_input_text = $("#"+id_text).val();
                        arr.push(json_insert);
                        json_insert = {};
                    }
                        
                
                }

                //Funcion para crear un json haciendo uso de dos arrays, el value el cual sera insertado en el json 
                // y el arr el cual es el arreglo general al cual se le hara push
                function buildJson(key, value, arr) {
                    json_insert = {};
                    json_insert.key_input = key;
                    json_insert.val_input = value;
                    arr.push(json_insert);
                    json_insert = {};
                }

                //Funciòn que obtiene todos los id y valores de un conjunto de inputs, crea un objeto y lo añade a un arreglo
                //id_container, hace referencia al contenedor donde estan los inputs
                //arr, hace referencia al arreglo donde se alamacenaràn los objetos
                function buildJsonObject(id_container, arr){
                    $("#"+id_container).find('input').each(function(){
                        json_object={};
                        var type =$(this).attr('type');

                        switch (type) {
                            case 'text':
                                if($(this).hasClass("otros") || $(this).val() == ""){
                                    break;
                                }else{
                                json_object.key_input_text = $(this).attr("id");
                                json_object.val_input_text = $(this).val();
                                arr.push(json_object);

                                }
                                break;
                            
                            case 'number':
                                if($(this).hasClass("otros") || $(this).val() == ""){
                                    break;
                                }else{
                                json_object.key_input_number = $(this).attr("id");
                                json_object.val_input_number = $(this).val();
                                arr.push(json_object);
                                break;
                                }

                            case 'date':
                                if($(this).hasClass("otros") || $(this).val() == ""){
                                    break;
                                }else{
                                json_object.key_input_date = $(this).attr("id");
                                json_object.val_input_date = $(this).val();
                                arr.push(json_object);
                                break;
                                }
                            case 'radio':
                                var o = getValue($(this));
                                if(o.id !== ""){
                                    if (o.id1 != "") {
                                        json_object.key_input = $(this).attr("name");
                                        json_object.val_input = $(this).val();
                                        if(o.type == "text"){
                                            json_object.key_input_text = o.id;
                                            json_object.val_input_text = o.val;
                                            json_object.key_input_number = o.id1;
                                            json_object.val_input_number = o.val1;
                                        }
                                        arr.push(json_object);
                                    }else{
                                        json_object.key_input = $(this).attr("name");
                                        json_object.val_input = $(this).val();
                                        if(o.type == "text"){
                                            json_object.key_input_text = o.id;
                                            json_object.val_input_text = o.val;
                                        }else if(o.type == "number"){
                                            json_object.key_input_number = o.id;
                                            json_object.val_input_number = o.val;
                                        }
                                        arr.push(json_object);
                                    }

                              }else{
                                break;
                              }
                                break;
                            case 'checkbox':
                            var o = getValue($(this));
                            if(o.id !== ""){
                                json_object.key_input = $(this).attr("id");
                                json_object.val_input = $(this).val();
                                if(o.type == "text"){
                                    json_object.key_input_text = o.id;
                                    json_object.val_input_text = o.val;
                                }else if(o.type == "number"){
                                    json_object.key_input_number = o.id;
                                    json_object.val_input_number = o.val;
                                }
                                arr.push(json_object);

                              }else{
                                break;
                              }
                                break;
                               
                        
                            default:
                                json_object.key_input = 0;
                                json_object.val_input = 0;
                                break;
                  
                        }
                     
                    })
                }
            

                //Funciòn que retorna el valor, tipo y el id o valor y id o 0, del radio checked de un c input de tipo radio,
                //input, hace referencia al input d el cuàl se desea obtener su valor, id y tipo  
                function getValue(input){
                    var o = {};
                    if(input.is(":checked")){
                        if(input.hasClass("otro")){
                            var d=input.parent().next().find('input');
                            var d1 =input.parent().next().children('input:last');
                            o.id = d.attr('id');
                            o.val = d.val();
                            o.type = d.attr("type");
                            if (d1.attr('id') != o.id) {
                                o.id1 = d1.attr('id');
                                o.val1 = d1.val();
                                o.type1 = d1.attr("type");
                            }
                            return o;
                        }else{
                            o.id =input.attr("name");
                            o.val = input.val();
                            return o;
                        }
                    }else{
                        o.id = "";
                        o.val = "";
                      return o;
                    }    
                    }

                //Funciòn que permite ocultar y desocultar un input, si el radio que esta check tiene la clase otro
                //setOptions, hace referencia al id del contenedor donde estan los input de tipo radio
                //nameRadio, hace referencia al atributo name del conjunto de radios 
                function hideAndShow(setOptions, nameRadio){
                    $("#"+setOptions).find("[name="+nameRadio+"]").on('click', function(){
                    
                        var s=$("#"+setOptions + " .otro").parent().next();
                        
                        if($(this).hasClass("otro")){
                            s.attr('hidden', false);
                            s.removeClass("otros");
                        }else{
                            s.attr('hidden', true);
                            s.addClass("otros");
                        }
                    })
                }

                //Validador si hay cambios en la seccion 1 del formulario, para actualizar en la BD   
                $('.step1').each(function() {
                    var elem = $(this);
                 
                    // Save current value of element
                    elem.data('oldVal', elem.val());
                 
                    // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event){
                           // If value has changed...
                           if (elem.data('oldVal') != elem.val()) {
                            // Updated stored value
                            elem.data('oldVal', elem.val());
                     
                            // Do action
                            cambios_s1 = true;
                          }
                        });
                  });

                //Validador si hay cambios en la seccion 2 del formulario, para actualizar en la BD   
                $('.step2').each(function() {
                    var elem = $(this);
                 
                    // Save current value of element
                    elem.data('oldVal', elem.val());
                 
                    // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event){
                           // If value has changed...
                           if (elem.data('oldVal') != elem.val()) {
                            // Updated stored value
                            elem.data('oldVal', elem.val());
                     
                            // Do action
                            cambios_s2 = true;
                          }
                        });
                  });
                
                //Validador si hay cambios en la seccion 3 del formulario, para actualizar en la BD   
                $('.step3').each(function() {
                    var elem = $(this);
                 
                    // Save current value of element
                    elem.data('oldVal', elem.val());
                 
                    // Look for changes in the value
                        elem.bind("propertychange change input paste", function(event){
                           // If value has changed...
                           if (elem.data('oldVal') != elem.val()) {
                            // Updated stored value
                            elem.data('oldVal', elem.val());
                     
                            // Do action
                            cambios_s3 = true;
                          }
                        });
                  });
                  
                //Validar si hay cambios en los radio buttons para actualizar en la BD
                $('input[type="radio"]').on('change', function () {
                    if ($(this).hasClass("step1")) {
                        cambios_s1 = true;
                    } else if ($(this).hasClass("step2")) {
                        cambios_s2 = true;
                    } else if ($(this).hasClass("step3")) {
                        cambios_s3 = true;
                    } else if ($(this).hasClass("step4")) {
                        cambios_s4 = true;
                        console.log("cambio s4");
                    } else if ($(this).hasClass("step5")) {
                        cambios_s5 = true;
                        console.log("cambio s5");
                    } else if ($(this).hasClass("step6")) {
                        cambios_s6 = true;
                        console.log("cambio s6");
                    } else{
                        
                    }
                   
                });
                  
                ///Funcion para validar los campos requeridos en el step1
                function validarCamposS1(step) {
                  var emptyS = false;
                  $('.'+step+'[required]:visible').each(function() {
                    var elem = $(this);
                    if (elem.val() == "") {
                        emptyS = true;
                    }
                  });

                  return emptyS;
                }

                ///Funcion para validar los campos requeridos en el step2 y step3
                function validarCamposS2_3(step) {
                    var emptyS = false;
                    $('.'+step+'[required]').each(function() {
                        var elem = $(this);
                        if (elem.attr("type") == "radio") {
                            var name = elem.attr("name");
                            if (!$('input:radio[name="'+name+'"]').is(":checked")) {
                                emptyS = true;
                            }
                        } else {
                            if (elem.val() == "") {
                                emptyS = true;
                            }
                        }
                    });
  
                    return emptyS;
                  }
                    
                /*
                Funcion que determina el cambio entre paginas
                */    
                $("#smartwizard").on("leaveStep", function(e, anchorObject, currentStepIndex, stepDirection, nextStepIndex) {
                   if (stepDirection === "forward") {
                        getSelectValues();
                        switch (currentStepIndex) {
                            case 0:
                                if (validarCamposS1("step1")) {
                                    swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                    return false;
                                  }else {
                                      //Validacion para la determinar la creacion en mdl_user
                                      if (mdl_user) {
                                          getAcademicProgram($('#programa').val());
                                          save_mdl_user();
                                          mdl_user = false;
                                      }
      
                                      // Validacion para determinar si es un insert o update
                                        //  en la tabla talentospilos_usuario
                                      if (!ases_user) {
                                          getStudent($("#codigo_estudiantil").val());
                                          save_data();
                                          ases_user = true;
                                          cambios_s1 = false;
                                      }else if(cambios_s1){
                                          console.log("Actualizo");
                                          cambios_s1 = false;
                                      }
                                  }
                            break;
                            case 1:
                                if (validarCamposS2_3("step2")) {
                                    swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                    return false;
                                  }else {
                                      if(!economics_data){
                                          save_data_user_step2();
                                          save_economics_data();
                                          economics_data =true;
                                          cambios_s2 = false;
                                      }else if(cambios_s2){
                                          console.log("Actualizo economics data");
                                          cambios_s2 = false;
                                      }
                                  }
                            break;
                            case 2:
                                if (validarCamposS2_3("step3")) {
                                    swal("Oops!", "Rellena todos los campos necesarios para poder avanzar", "warning")
                                    return false;
                                  }else {
                                      if(!academics_data){
                                          save_data_user_step3();
                                          save_academics_data();
                                          academics_data =true;
                                          cambios_s3 = false;
                                      }else if(cambios_s3){
                                          console.log("Actualizo academics data");
                                          cambios_s3 = false;
                                      }
                                  }
                                
                            break;
                            case 3:
                                if (!discapacity) {
                                    save_data_user_step4();
                                    discapacity = true;
                                    cambios_s4 = false;
                                } else if(cambios_s4) {
                                    cambios_s4 = false;
                                }
                            break;
                            case 4:
                                if (!healthCondition) {
                                    save_discapacity_data();
                                    healthCondition = true;
                                    cambios_s5 = false
                                } else if(cambios_s5) {
                                    cambios_s5 = false;
                                }
                            break;
                            default:
                            break;
                        }
                    }
                 }); 


                //Funcion para recolectar valores de los selects
                function getSelectValues(){
                    $('#tipo_doc').val($("#tipo_doc_ini option:selected").val())
                    var selects=$(".select-registro").map(function(){return $(this).attr("id");}).get()
                    var inputs=$(".select-registro").map(function(){return $(this).next().attr("id")}).get()
                    for(var i=0; i < inputs.length; i++){
                        
                        $("#"+inputs[i]).val($("#"+selects[i]+" option:selected").val());
                        
                    }
                } 

                //Funcion para la creacion de usuario en mdl_user, en caso de no existir
                function save_mdl_user() {
                    var codigo = $("#codigo_estudiantil").val();
                    var nombre = $("#nombre").val();
                    var apellido = $("#apellido").val();
                    var emailI = $("#emailinstitucional").val();
                    var username = ''+codigo+"-"+cod_programa;
                    var pass = nombre.charAt(0)+codigo+apellido.charAt(0);
                    pass = pass.toUpperCase();
                    
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_mdl_user',
                            "params": [username, nombre, apellido, emailI, pass]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                            id_moodle = msg;
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

                //Funciòn que hace una peticiòn asìncrona a la Api para el guardado, aquì se debe empaquetar en variables
                //todos las variables a guardar del formulario
                function save_data(){

                    $('#num_doc').val($("#num_doc_ini").val())

                    var deportes = [];
                    deportes.push({name:"deportes", value:input_deportes.getArr()});
                    deportes.push({name:"actividades", value:input_actividades.getArr()});
                    deportes = JSON.stringify(deportes);

                    var a=$(".talentospilos_usuario").serializeArray();
                    var est = $("#div_tipo_institucion ").find("[name=estamento]:checked").val();
                    a.push({name:"estamento", value:est});
                    a.push({name:"id_ciudad_nac", value:$("#id_ciudad_ini").val()});

                    var familia = buildArr($("#table_familia"));
                    var programa = $("#programa").val();

                    var json_detalle = [];
                    buildJsonObject('otra_identidad', json_detalle);
                    buildJsonObject('otra_orientacion', json_detalle);
                    buildJsonObject('contacto_2', json_detalle);
                    buildJsonObject('otros_acomp', json_detalle);
                    json_detalle = JSON.stringify(json_detalle);

                    
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data',
                            "params": [a, deportes, familia, programa,id_moodle, json_detalle]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

                /*
                    Metodo para almacenar datos de la tabla talentospilos_usuario que se encuentran en la 
                    seccion 2 del formulario (familia, hijos, estrato)
                */
                function save_data_user_step2() {
                    var vive = buildArr($("#table_familia"));
                    buildJsonObject('set-vive', vive);
                    var familia = JSON.stringify(vive);
                    var hijos =  $("#hijos").val();
                    var estrato = $("#estrato").val();
                    getStudentAses($("#num_doc_ini").val());
                    
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data_user_step2',
                            "params": [id_ases, estrato, hijos, familia]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

                function save_economics_data(){
                    var economics_data = getEconomicsData();
                    var estrato = $("#estrato").val();

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'insert_economics_data',
                            "params": [economics_data, estrato, id_ases]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                            id_economics_data = msg
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

                function save_academics_data(){
                    var academics_data = getAcademicsData();
                    getStudentAses($("#num_doc_ini").val());
                    var programa = $("#programa").val();
                    var titulo = $("#titulo_1").val();
                    var observaciones = $("#observaciones").val();
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'insert_academics_data',
                            "params": [academics_data, programa, titulo, observaciones, id_ases]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

                function save_discapacity_data(){
                    var datos_discapacidad = getHealtData();
                    getStudentAses($("#num_doc_ini").val());
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'insert_disapacity_data',
                            "params": [datos_discapacidad, id_ases]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

                function save_health_service(){
                    var healtService_data = getHealtService();
                    getStudentAses($("#num_doc_ini").val());
                    var eps = $("#EPS").val();
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'insert_health_service',
                            "params": [healtService_data, eps, id_ases]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
                             var result = msg;
                             if (!isNaN(result)) {
                                swal("Registro completado exitosamente!", "", "success")
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

                function save_data_user_step3() {
                    var icfes =  $("#icfes").val();
                    var anio_ingreso =  $("#anio_ingreso").val();
                    var colegio =  $("#colegio").val();
                    getStudentAses($("#num_doc_ini").val());
                    
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data_user_step3',
                            "params": [id_ases, icfes, anio_ingreso, colegio, id_economics_data]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

                function save_data_user_step4() {
                    var id_discapacidad =  $("#id_discapacidad").val();
                    //var ayuda_disc =  $("#ayuda_disc").val();
                    getStudentAses($("#num_doc_ini").val());

                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data_user_step4',
                            "params": [id_ases, id_discapacidad]
                        }),
                        url: "../managers/student_profile/studentprofile_api.php",
                        success: function(msg){
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

