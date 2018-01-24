<?php 
 
    require_once(dirname(__FILE__). '/../../../../config.php');

    global $DB;

    $FORM_ID = $_GET['id']; 
    $ROL = $_GET['rol'];
    $RECORD_ID = $_GET['reg']; // Identificador del registro del formulario diligenciado

    if(!$RECORD_ID){
        echo "Error: variable reg ausente.";
        die();
    }
    
    $sql = '
    
        SELECT * FROM {talentospilos_df_tipo_campo} AS TC 
        INNER JOIN (
            SELECT * FROM {talentospilos_df_preguntas} AS P 
            INNER JOIN (
                SELECT *, F.id AS mod_id_formulario, FP.id AS mod_id_formulario_pregunta FROM {talentospilos_df_formularios} AS F
                INNER JOIN {talentospilos_df_form_preg} AS FP
                ON F.id = FP.id_formulario WHERE F.id = '.$FORM_ID.'
                ) AS AA ON P.id = AA.id_pregunta
            ) AS AAA
        ON TC.id = AAA.tipo_campo
        ORDER BY posicion
    
    ';

    $result = $DB->get_records_sql($sql);
    $result = (array) $result;
    $result = array_values($result);

    $sql_disparadores_permisos = '
    
        SELECT * 
        FROM {talentospilos_df_disp_fordil} 
        WHERE id_formulario = '.$FORM_ID.'

    ';

    $result_disparadores = $DB->get_record_sql($sql_disparadores_permisos);
    $triggers_permissions = $result_disparadores;
    
    if($triggers_permissions->disparadores !== "null"){
        $triggers_permissions = json_decode($triggers_permissions->disparadores);
    }else{
        $triggers_permissions = null;
    }

    // Construcción de todo el sitio en HTML para pruebas de bootstrap

    /*echo '
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Renderizador de formularios</title>
        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
        
            .danger{
                border: 1px solid red;
            }
            .ok{
                border: 1px solid green;
            }
        
        </style>
    </head>
    <body>
        <div class="container">
            <div class="row">
    ';*/

    // Fin de construcción

    $row = $result[0];
    $form_name = $row->{'nombre'};
    $form_name_formatted = strtolower($row->{'nombre'});
    $form_name_formatted = str_replace(" ", "_", $form_name_formatted);
    $form_name_formatted = str_replace("   ", "_", $form_name_formatted);
    $form_name_formatted = str_replace(' ', "_", $form_name_formatted);
    $form_name_formatted = str_replace("á", "a", $form_name_formatted);
    $form_name_formatted = str_replace("é", "e", $form_name_formatted);
    $form_name_formatted = str_replace("í", "i", $form_name_formatted);
    $form_name_formatted = str_replace("ó", "o", $form_name_formatted);
    $form_name_formatted = str_replace("ú", "u", $form_name_formatted);
    $form_name_formatted = str_replace("ü", "u", $form_name_formatted);
    $form_name_formatted = str_replace("ñ", "n", $form_name_formatted);
    $form_name_formatted = utf8_encode($form_name_formatted);
    $form_name_formatted = $form_name_formatted . "_" . $row->{'mod_id_formulario'};


    echo '<form id="'. $form_name_formatted .'" method="'. $row->{'method'} .'" action="'. $row->{'action'} .'" class="col-xs-12 col-sm-8 col-md-8 col-lg-6 col-sm col-sm-offset-2 col-md-offset-2 col-lg-offset-3" style="margin-bottom:0.7em">' ;
    echo '<h1>'.$form_name.'</h1><hr style="border-color:red;">';
    echo '<input name="id" value="'.$row->{'mod_id_formulario'}.'" style="display:none;">';
    echo '<input name="id_monitor" value="5245" style="display:none;">';
    echo '<input name="id_estudiante" value="6548" style="display:none;">';

    echo '<input name="id_registro" value="'.$RECORD_ID.'" style="display:none;">';


    $sql_respuestas = '
    
        SELECT * 
        FROM {talentospilos_df_respuestas} AS R 
        INNER JOIN 
            (
                SELECT * 
                FROM {talentospilos_df_form_resp} AS FR 
                INNER JOIN {talentospilos_df_form_solu} AS FS 
                ON FR.id = FS.id_formulario_respuestas 
                WHERE FR.id = '.$RECORD_ID.'
            ) AS FRS 
        ON FRS.id_respuesta = R.id;
    
    ';

    $respuestas = array();
    $result_respuestas = $DB->get_records_sql($sql_respuestas);
    $result_respuestas = array_values($result_respuestas);

    for($i = 0; $i < count($result_respuestas); $i++){
        $row_respuesta = $result_respuestas[$i];
        $tmp = array(
            'id_pregunta_formulario' => $row_respuesta->id_pregunta,
            'respuesta' => $row_respuesta->respuesta
        );
        array_push($respuestas, $tmp);
    }
    
    $global_respuestas = array();
    $checkboxes_scripts = null;

    for($i = 0; $i < count($result); $i++){
        //while($row){
        $row = null;
        $row = $result[$i];
    
        $campo = $row->{'campo'};
        $id_campo = $row->{'mod_id_formulario_pregunta'};
        $enunciado = $row->{'enunciado'};
        $atributos = json_decode($row->{'atributos_campo'});

        //Consulta de permisos
        $sql_permisos = '
            SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '.$row->{'id_pregunta'}.'
        ';
        $result_permisos = $DB->get_record_sql($sql_permisos);

        $permisos = $result_permisos;
        $permisos_JSON = json_decode($permisos->permisos);

        //Consulta del valor registrado
        $valor = null;
        foreach ($respuestas as $key => $value) {
            $id_campo_DB = (string) $value['id_pregunta_formulario'];
            $id_campo_DB_form = (string) $id_campo;
            
            if($id_campo_DB == $id_campo_DB_form){
                $valor = $value['respuesta'];
                echo 'VALOOOOOOOOOOOR:['.$id_campo_DB.']['.$id_campo_DB_form.']' . $valor;
                $tmpPregunta = array(
                    'idP' => $id_campo_DB,
                    'valor' => $value['respuesta'],
                    'tipoCampo' => $campo
                );
                array_push($global_respuestas, $tmpPregunta);
                break;
            }
        }

        foreach ($permisos_JSON as $key => $rol) {
            if($rol->{'rol'} == $ROL){

                $lectura = false;
                $escritura = false;

                foreach ($rol->{'permisos'} as $key2 => $value) {
                    if($value == "lectura"){
                        $lectura = true;
                    }
                    if($value == "escritura"){
                        $escritura = true;
                    }
                }

                if($lectura){

                    $enabled = null;
                    if(!$escritura){
                        $enabled = "disabled";
                    }

                    if($campo == 'TEXTFIELD'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="'.$atributos->{'type'}.'" placeholder="'.$atributos->{'placeholder'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'TEXTAREA'){
                        echo $enunciado . ':<br>';
                        echo ' <textarea id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" name="'. $row->{'mod_id_formulario_pregunta'} .'" '.$enabled.'>'.$valor.'</textarea><br>' . "\n";
                    }

                    if($campo == 'DATE'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="date" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.'><br>' . "\n";
                    }
                    
                    if($campo == 'DATETIME'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="datetime-local" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'TIME'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="time" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'RADIOBUTTON'){
                        $opciones = json_decode($row->{'opciones_campo'});
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);

                        echo '
                        <label id="'.$row->{'mod_id_formulario_pregunta'}.'">'.$enunciado.'</label>';
                        echo '<div class="opcionesRadio" style="margin-bottom:0.4em">
                        <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'"  class="'.$row->{'mod_id_formulario_pregunta'}.'" value="-#$%-" '.$enabled.'>';
                        
                        
                        for($i = 0; $i < $number_opciones; $i++){
                            $opcion = (array) $array_opciones[$i];
                            $checked = null;
                            if($valor === $opcion['valor']){
                                $checked = 'checked';
                            }
                            echo '
                                <div class="radio">
                                    <label><input type="radio" class="'.$row->{'mod_id_formulario_pregunta'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" name="optradio" '.$enabled.'  '.$checked.'>'.$opcion['enunciado'].'</label>
                                </div>
                            
                            ' . "\n";
                        }
                        
                        echo '<a href="javascript:void(0);" class="'.$row->{'mod_id_formulario_pregunta'}.' limpiar btn btn-xs btn-default" >Limpiar</a>
                            </div>
                        ' . "\n";
                    }

                    if($campo == 'CHECKBOX'){
                        $opciones = json_decode($row->{'opciones_campo'});
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);
                        $checked = null;
                        $valor_marcado = null;
                        if($valor === "0"){
                            $checked = 'checked';
                            $valor_marcado = "-1";
                        }
                        if($valor === "-1"){
                            $checked = '';
                            $valor_marcado = "-1";
                        }
                        echo '
                        <label id="'.$row->{'mod_id_formulario_pregunta'}.'">'.$enunciado.'</label>';
                        
                        for($i = 0; $i < $number_opciones; $i++){
                            $opcion = (array) $array_opciones[$i];
                            echo '
                            <div id="'.$row->{'mod_id_formulario_pregunta'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" class="checkbox">
                                <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor_marcado.'" '.$enabled.'>
                                <label><input id="'.$row->{'mod_id_formulario_pregunta'}.'" type="checkbox" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" '.$enabled.' '.$checked.'>'.$opcion['enunciado'].'</label>
                                
                            </div>
                            ' . "\n";

                           
                        }

                        
                    }

                }

                break;

            }
        }


        //$row = pg_fetch_row($result);
    }
    echo ' <hr style="border-color:red"><button type="submit" class="btn btn-sm btn-default">Registrar</button>' . "\n";
    echo ' </form>' . "\n";


    // Construcción de todo el sitio en HTML para pruebas de bootstrap

    //Escritura de reglas en JAVASCRIPT
    //Reglas

    /*
    $script_reglas = null;
    $sql = '
        SELECT 
            * 
        FROM 
            reglas, 
            reglas_formulario_preguntas
        WHERE 
            reglas_formulario_preguntas.id_regla = reglas.id
        AND
            reglas_formulario_preguntas.id_formulario = '.$FORM_ID.'

    ';

    $reglas = pg_query($db_connection, $sql);
    $row_reglas = pg_fetch_row($reglas);
    while($row_reglas){
        $regla = $row_reglas[1];
        $campoA = $row_reglas[5];
        $campoB = $row_reglas[6];
        if($regla == 'DIFFERENT'){
            $script_reglas = $script_reglas . '
            
                $(document).on("keyup", "#'.$campoA.'" , function() {
                    if(($("#'.$campoA.'").val() == $("#'.$campoB.'").val())&&($("'.$campoA.'").val() != "")){
                        $("#'.$campoA.'").addClass("danger");
                        $("#'.$campoB.'").addClass("danger");
                    }else{
                        $("#'.$campoA.'").removeClass("danger");
                        $("#'.$campoB.'").removeClass("danger");
                    }
                });

                $(document).on("keyup", "#'.$campoB.'" , function() {
                    if(($("#'.$campoB.'").val() == $("#'.$campoA.'").val())&&($("#'.$campoB.'").val() != "")){
                        $("#'.$campoB.'").addClass("danger");
                        $("#'.$campoA.'").addClass("danger");
                    }else{
                        $("#'.$campoB.'").removeClass("danger");
                        $("#'.$campoA.'").removeClass("danger");
                    }
                });
            
            ';
        }
        $row_reglas = pg_fetch_row($reglas);
    };*/

   
    function dphpforms_generate_permits_scripts($behaviors, $ROL){
        
        $script = null;
            
        $behavior_field = $behaviors->{'campo'};
        $behavioral_permissions = $behaviors->{'permisos'};

        foreach ($behavioral_permissions as $keyPC => $PC) {
            
            if($PC->{'rol'} == $ROL){

                $flagLectura = false;
                $flagEscritura = false;

                foreach ($PC->{'permisos'} as $keyPC => $permissions_field) {
                    if($permissions_field == "lectura"){
                        $flagLectura = true;
                    }
                    if($permissions_field == "escritura"){
                        $flagEscritura = true;
                    }
                }

                if($flagEscritura){

                    $disabled = "false";
                    $script = $script.   '  $("#'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                    $script = $script.   '  $(".'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";

                }else{
                    $disabled = "true";
                    $script = $script.   '  $("#'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                    $script = $script.   '  $(".'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                    $script = $script.   '  $(".'.$behavior_field.'.limpiar ").remove();  ' . "\n";  
                }

                      
            
                if(!$flagLectura){
                    $script = $script.   '  $("#'.$behavior_field.'").remove();  ' . "\n";
                    $script = $script.   '  $(".'.$behavior_field.'").remove();  ' . "\n";
                }
                break;
            }
        }
        return $script;
    }

    $permissions_script = null;
    
    foreach ($triggers_permissions as $keyPermiso => $permission_trigger) {

        $trigger = $permission_trigger->{'disparador'};
        $conditionns = $permission_trigger->{'condiciones'};
        foreach($conditionns as $keyCondicion => $condition){
            $respuesta_trigger = null;

            //echo 'Disparador: ' . $trigger . ' Condición: ' . $condition->{'condicion'};
            foreach ($global_respuestas as $key => $g_respuesta) {
                if($g_respuesta['idP'] == $trigger ){
                    $respuesta_trigger = $g_respuesta;
                    break;
                }
            }

            //print_r($respuesta_trigger);
            $flag_satisfy = false;

            if(
                    (
                        ($respuesta_trigger['tipoCampo'] == 'TEXTFIELD') ||
                        ($respuesta_trigger['tipoCampo'] == 'TEXTAREA') ||
                        ($respuesta_trigger['tipoCampo'] == 'DATE') ||
                        ($respuesta_trigger['tipoCampo'] == 'TIME') ||
                        ($respuesta_trigger['tipoCampo'] == 'DATETIME')

                    ) 
                        && 
                    (
                        ($condition->{'condicion'} == 'vacio')||
                        ( $condition->{'condicion'} == 'no_vacio')
                    )
                ){

                    //echo 'CONDICION PARA TEXTO';
                    if(($condition->{'condicion'} == 'no_vacio') && ($respuesta_trigger['valor'] !== null )){
                        //echo "Se cumple no_vacio y con resultado";
                        $flag_satisfy = true;
                    }else{
                        //echo "No se cumple no_vacio y con resultado";
                    }

                    if(($condition->{'condicion'} == 'vacio') && ($respuesta_trigger['valor'] === null )){
                        //echo "Se cumple vacio y sin resultado";
                        $flag_satisfy = true;
                    }else{
                        //echo "No se cumple vacio y sin resultado";
                    }
            }

            if(
                (
                    ($respuesta_trigger['tipoCampo'] == 'RADIOBUTTON') || 
                    ($respuesta_trigger['tipoCampo'] == 'CHECKBOX')
                ) 
                    && 
                (
                    ($condition->{'condicion'} == 'marcado') || ($condition->{'condicion'} == 'no_marcado')
                )
            ){

                    //echo ' CONDICION PARA RADIO/CHECK ';
                    if(($condition->{'condicion'} == 'marcado') && (($respuesta_trigger['valor'] !== "-1" )&&($respuesta_trigger['valor'] !== "-#$%-" ))){
                        //echo "Se cumple marcado y con resultado";
                        $flag_satisfy = true;
                        echo $respuesta_trigger['valor'];
                        echo "CUMPLIÓ";
                        print_r($respuesta_trigger);
                    }else{
                        //echo "No se cumple marcado y con resultado";
                    }

                    if(($condition->{'condicion'} == 'no_marcado') && (($respuesta_trigger['valor'] === "-1" )||($respuesta_trigger['valor'] == "-#$%-" ))){
                        //echo "Se cumple no_marcado y sin resultado";
                        $flag_satisfy = true;
                        echo $respuesta_trigger['valor'];
                        echo "NO CUMPLIO";
                        print_r($respuesta_trigger);
                    }else{
                        //echo "No se cumple no_marcado y sin resultado";
                    }
            }

            $behavioral_condition_satisfied  = $condition->{'comportamiento_condicion_cumplida'};
            $behavioral_condition_not_satisfied  = $condition->{'comportamiento_condicion_no_cumplida'};
            if($flag_satisfy){
                foreach ($behavioral_condition_satisfied  as $keyCCC => $behaviors) {
                    $permissions_script = $permissions_script . dphpforms_generate_permits_scripts($behaviors, $ROL);
                }
            }else{
                foreach ($behavioral_condition_not_satisfied  as $keyCCNC => $comportamiento) {
                    $permissions_script = $permissions_script . dphpforms_generate_permits_scripts($behaviors, $ROL);
                }
            }
        }
    }

    /*echo '
    
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function(){
            '
            .$permissions_script.
            '
        });
        $(".limpiar").click(function(){
            $(this).parent().find("div").each(function(){
                $(this).find("label").find("input").prop("checked", false);
            });
        });


    </script>
    <script>
        '.$script_reglas.'
    </script>
    </body>
</html>
    
    ';*/

    // Fin de construcción
   
?>