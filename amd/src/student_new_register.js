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

            //Modal controls
            $('#mostrar').on('click', function () {
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
                loadSelector($('#s_discapacidad'), 'get_discapacities');


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
                    for(var i=0; i < inputs.length; i++){
                        
                        $("#"+inputs[i]).val($("#"+selects[i]+" option:selected").val());
                        
                    }
                   
                    getStudent("code"+$("#codigo_estudiantil").val());

               
                    getEconomicsData();
             
                    //Funciòn para guardar la informaciòn de registro(en desarrollo)
                   // save_data();
                });

               
                /*
                let array = [
                {id:'set-permanencia', name='option'},{id:'set-beca', name="beca"}, {id:'set-materiales', name='materiales'},
                {id:'set-valor-materiales', name="valor_materiales"}, {id:'set-desplazamiento', name:'option'}, {id:'set-sostenimiento', name:'sostenimiento'}
             ]

             */
            
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
                //Funciòn que carga los selectores, para el registro de un nuevo estudiante a acompañar
                //select, hace referencia al id del select dònde se cargarà la info
                //f, hace referencia al nombre de la funciòn definida en studentprofile_main.php, que recuperarà la info
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

                // Funciòn que recolecta los datos acadèmicos y los empaqueta en un arr
                function getAcademidcData(){
                    var arr_academic = [];
                    $("#sede").val();
                }
                // Funciòn que recolecta los datos socio-econòmicos y los empaqueta en un arr
                function getEconomicsData(){
                    var arr = [];
                    var arr_madre = [];
                    var arr_padre =[];
                    var arr_otro = [];
                    var arr_proyecto_vida = [];
                    var e =$("#estrato").val();
                    var json_proyecto = {};

                    buildJsonObject('set-permanencia', arr);
                    buildJsonObject('set-beca', arr);
                    buildJsonObject('row_materiales', arr);
                    buildJsonObject('set-vive', arr);

                    buildJsonObject('row_madre_academic', arr_madre);
                    buildJsonObject('row_padre_academic', arr_padre);
                    buildJsonObject('row_otro_academic', arr_otro);

                    json_proyecto.key_input_text = $("#motivo_ingreso").attr("id");
                    json_proyecto.val_input_text = $("#motivo_ingreso").val();
                    arr_proyecto_vida.push(json_proyecto);
                    json_proyecto = {};

                    json_proyecto.key_input_text = $("#expecativas_carrera").attr("id");
                    json_proyecto.val_input_text = $("#expecativas_carrera").val();
                    arr_proyecto_vida.push(json_proyecto);
                    json_proyecto = {};

                    json_proyecto.key_input_text = $("#expectativas_graduarse").attr("id");
                    json_proyecto.val_input_text = $("#expectativas_graduarse").val();
                    arr_proyecto_vida.push(json_proyecto);

                    arr.push(e);
                    arr.push(arr_madre);
                    arr.push(arr_padre);
                    arr.push(arr_otro);
                    arr.push(arr_proyecto_vida);

                    return arr;
                   
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
                                if($(this).is(':hidden') || $(this).val() == ""){
                                    break;
                                }else{
                                json_object.key_input_text = $(this).attr("id");
                                json_object.val_input_text = $(this).val();
                                arr.push(json_object);

                                }
                                break;
                            
                            case 'number':
                                if($(this).is(':hidden') || $(this).val() == ""){
                                    break;
                                }else{
                                json_object.key_input_number = $(this).attr("id");
                                json_object.val_input_number = $(this).val();
                                arr.push(json_object);
                                break;
                                }
                  
                            case 'radio':
                                var o = getValue($(this));
                                if(o.id !== ""){
                                    if(o.type == "text"){
                                        json_object.key_input_text = o.id;
                                        json_object.val_input_text = o.val;
                                    }else if(o.type == "number"){
                                        json_object.key_input_number = o.id;
                                        json_object.val_input_number = o.val;
                                    }else{
                                        json_object.key_input = o.id;
                                        json_object.val_input = o.val;
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
                            o.id = d.attr('id');
                            o.val = d.val();
                            o.type = d.attr("type");
                            return o;
                        }else{
                            o.id =input.attr("id");
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
                    }else{
                        s.attr('hidden', true);
                    }
                    })
                    }



                //Funciòn que hace una peticiòn asìncrona a la Api para el guardado, aquì se debe empaquetar en variables
                //todos las variables a guardar del formulario
                function save_data(){
                    var deportes = input_deportes.getArr();

                    var a=$(".talentospilos_usuario").serializeArray();
                    var est = $("#div_tipo_institucion ").find("[name=estamento]:checked").val();
                    a.push({name:"estamento", value:est});
                    a.push({name:"id_ciudad_nac", value:$("#id_ciudad_ini").val()});

                    var familia = buildArr($("#table_familia"));
                    var programa = $("#programa").val();

                    
                    $.ajax({
                        type: "POST",
                        data: JSON.stringify({
                            "function": 'save_data',
                            "params": [a, deportes, familia, programa,id_moodle]
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

