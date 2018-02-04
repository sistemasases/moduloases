<?php 
 
    require_once(dirname(__FILE__). '/../../../../config.php');

    //$id_completed_form -> form_id
    function dphpforms_generate_html_updater($id_completed_form, $rol_, $record_id_){

        global $DB;

        $html = null;

        $FORM_ID = $id_completed_form; 
        $ROL = $rol_;
        $RECORD_ID = $record_id_;

        if(!$RECORD_ID){
            $html = $html .  "Error: variable reg ausente.";
            return $html;
        }

        if(!is_numeric($id_completed_form)){
            $sql_alias = "SELECT id FROM {talentospilos_df_formularios} WHERE alias = '$id_completed_form' AND estado = 1";
            $form_record = $DB->get_record_sql($sql_alias);
            if($form_record){
                $FORM_ID = $form_record->id;
            }
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


        $html = $html .  '<form id="'. $form_name_formatted .'" method="'. $row->{'method'} .'" action="'. $row->{'action'} .'" class="dphpforms dphpforms-record col-xs-12 col-sm-12 col-md-12 col-lg-12" style="margin-bottom:0.7em">' ;
        $html = $html .  '<h1>'.$form_name.'</h1><hr style="border-color:red;">';
        $html = $html .  '<input name="id" value="'.$row->{'mod_id_formulario'}.'" style="display:none;">';
        $html = $html .  '<input name="id_monitor_x_obsolete" value="" style="display:none;">';//Pendientes para eliminación
        $html = $html .  '<input name="id_estudiante_x_obsolete" value="" style="display:none;">';//Pendientes para eliminación
        //Dispara la actualización
        $html = $html .  '<input id="dphpforms_record_id" name="id_registro" value="'.$RECORD_ID.'" style="display:none;">';
        //Fin del disparador de actualización

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

                        $field_attr_class = '';
                        $field_attr_type = '';
                        $field_attr_placeholder = '';
                        $field_attr_maxlength = '';
                        $field_attr_inputclass = '';
                        $field_attr_required = '';
                        $field_attr_local_alias = '';

                        if(property_exists($atributos, 'class')){
                            $field_attr_class = $atributos->{'class'};
                        }

                        if(property_exists($atributos, 'type')){
                            $field_attr_type = $atributos->{'type'};
                        }

                        if(property_exists($atributos, 'placeholder')){
                            $field_attr_placeholder = $atributos->{'placeholder'};
                        }

                        if(property_exists($atributos, 'maxlength')){
                            $field_attr_maxlength = $atributos->{'maxlength'};
                        }

                        if(property_exists($atributos, 'inputclass')){
                            $field_attr_inputclass = $atributos->{'inputclass'};
                        }

                        if(property_exists($atributos, 'required')){
                            $field_attr_required = $atributos->{'required'};
                            if($field_attr_required == 'true'){
                                $field_attr_required = 'required';
                            }elseif($field_attr_required == 'false'){
                                $field_attr_required = '';
                            }
                        }

                        if(property_exists($atributos, 'local_alias')){
                            $field_attr_local_alias = $atributos->{'local_alias'};
                        }

                        if($campo == 'TEXTFIELD'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" type="'.$field_attr_type.'" placeholder="'.$field_attr_placeholder.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" maxlength="'.$field_attr_maxlength.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'TEXTAREA'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <textarea id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" name="'. $row->{'mod_id_formulario_pregunta'} .'" '.$enabled.' maxlength="'.$field_attr_maxlength.'" '.$field_attr_required.'>'.$valor.'</textarea><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'DATE'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" type="date" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }
                        
                        if($campo == 'DATETIME'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" type="datetime-local" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'TIME'){
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >' . $enunciado . ':<br>';
                            $html = $html .  ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control ' . $field_attr_inputclass . '" type="time" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor.'" '.$enabled.' '.$field_attr_required.'><br>' . "\n";
                            $html = $html .  '</div>';
                        }

                        if($campo == 'RADIOBUTTON'){
                            $opciones = json_decode($row->{'opciones_campo'});
                            $array_opciones = (array)$opciones;
                            $number_opciones = count($array_opciones);
                            
                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                            if($enunciado){
                                $html = $html . '<label>'.$enunciado.'</label>';
                            }

                            $field_attr_radioclass = '';
                            if(property_exists($atributos, 'radioclass')){
                                $field_attr_radioclass = $atributos->{'radioclass'};
                            }

                            $html = $html .  '<div class="opcionesRadio" style="margin-bottom:0.4em">
                            <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'"  class="'.$row->{'mod_id_formulario_pregunta'}.'" value="-#$%-" '.$enabled.'>';
                            
                            for($x = 0; $x < $number_opciones; $x++){
                                $opcion = (array) $array_opciones[$x];
                                $checked = null;
                                if($valor === $opcion['valor']){
                                    $checked = 'checked';
                                }
                                $html = $html .  '
                                    <div class="radio ' . $field_attr_radioclass . '">
                                        <label><input type="radio" class="'.$row->{'mod_id_formulario_pregunta'}.' ' . $field_attr_inputclass . '" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" name="optradio" '.$enabled.'  '.$checked.'>'.$opcion['enunciado'].'</label>
                                    </div>' . "\n";
                            }
                            
                            $html = $html .  '</div>
                                        <a href="javascript:void(0);" class="'.$row->{'mod_id_formulario_pregunta'}.' limpiar btn btn-xs btn-default" >Limpiar</a>
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

                            $html = $html .  '<div class="div-'.$row->{'mod_id_formulario_pregunta'}.' '.$field_attr_class.' '.$field_attr_local_alias.'" >';
                            if($enunciado){
                                $html = $html . '<label>'.$enunciado.'</label>';
                            }

                            $field_attr_checkclass = '';
                            if(property_exists($atributos, 'checkclass')){
                                $field_attr_checkclass = $atributos->{'checkclass'};
                            }
                            
                            for($x = 0; $x < $number_opciones; $x++){
                                $opcion = (array) $array_opciones[$x];
                                $html = $html .  '
                                    <div id="'.$row->{'mod_id_formulario_pregunta'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" class="checkbox ' . $field_attr_checkclass . '">
                                        <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$valor_marcado.'" '.$enabled.'>
                                        <label><input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="' . $field_attr_inputclass . '" type="checkbox" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" '.$enabled.' '.$checked.'>'.$opcion['enunciado'].'</label>
                                        
                                    </div>
                                ' . "\n";
                            }
                            $html = $html . '</div>';

                        }

                    }

                    break;

                }
            }

        }
        $html = $html .  ' <hr style="border-color:red"><button type="submit" class="btn btn-sm btn-danger btn-dphpforms-univalle">Actualizar</button> <a href="javascript:void(0);" data-record-id="'.$RECORD_ID.'" class="btn btn-sm btn-danger btn-dphpforms-univalle btn-dphpforms-delete-record">Eliminar</a>' . "\n";
        $html = $html .  ' </form>' . "\n";

        //Manejo de disparadores
        function dphpforms_generate_permits_information($behaviors, $ROL){
            
            //Este campo hace referencia a el identificador de la pregunta.
            $behavior_field = $behaviors->{'campo'};
            $behavioral_permissions = $behaviors->{'permisos'};

            $all_behaviors_rol = array();

            foreach ($behavioral_permissions as &$PC) {

                $behaviors_rol = array();

                $json_behaviors_accessibility = array();
                $json_behaviors_fields_to_remove = array();
                $json_limpiar_to_eliminate = array();

                if($PC->{'rol'} == $ROL){

                    $flagLectura = false;
                    $flagEscritura = false;

                    foreach ($PC->{'permisos'} as &$permissions_field) {
                        if($permissions_field == "lectura"){
                            $flagLectura = true;
                        }
                        if($permissions_field == "escritura"){
                            $flagEscritura = true;
                        }
                    }


                    if($flagEscritura){

                        $disabled = "false";
                        //$json_behaviors = $json_behaviors.   '  $("#'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                        //$json_behaviors = $json_behaviors.   '  $(".'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";

                        $tmp_accessibility = array(
                            'class' => $behavior_field,
                            'id' => $behavior_field,
                            'disabled' => $disabled
                        );

                        array_push($json_behaviors_accessibility, $tmp_accessibility);

                    }else{
                        $disabled = "true";
                        //$json_behaviors = $json_behaviors.   '  $("#'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                        //$json_behaviors = $json_behaviors.   '  $(".'.$behavior_field.'").prop( "disabled", '.$disabled.' );  ' . "\n";
                        //$json_behaviors = $json_behaviors.   '  $(".'.$behavior_field.'.limpiar ").remove();  ' . "\n";  

                        $tmp_accessibility = array(
                            'class' => $behavior_field,
                            'id' => $behavior_field,
                            'disabled' => $disabled
                        );

                        $tmp_limpiar_to_eliminate = array(
                            'class' => $behavior_field
                        );

                        array_push($json_behaviors_accessibility, $tmp_accessibility);
                        array_push($json_limpiar_to_eliminate, $tmp_limpiar_to_eliminate);
                    }

                    if(!$flagLectura){
                        //$json_behaviors = $json_behaviors.   '  $("#'.$behavior_field.'").remove();  ' . "\n";
                        //$json_behaviors = $json_behaviors.   '  $(".'.$behavior_field.'").remove();  ' . "\n";

                        $tmp_field_to_remove = array(
                            'class' => $behavior_field,
                            'id' => $behavior_field
                        );

                        array_push($json_behaviors_fields_to_remove, $tmp_field_to_remove);
                    }

                    $behaviors_rol = array(
                        'behaviors_accessibility' => $json_behaviors_accessibility,
                        'behaviors_fields_to_remove' => $json_behaviors_fields_to_remove,
                        'limpiar_to_eliminate' => $json_limpiar_to_eliminate
                    );

                    array_push($all_behaviors_rol, $behaviors_rol);
                    break;
                }
            }
            

            $_behaviors = array(
                    'behaviors' => $all_behaviors_rol
            );

            return $_behaviors;
        }

        $permissions_to_script = array();

        if($triggers_permissions != 'null'){

            foreach ($triggers_permissions as $keyPermiso => $permission_trigger) {

                $trigger = $permission_trigger->{'disparador'};
                $conditionns = $permission_trigger->{'condiciones'};
                foreach($conditionns as &$condition){
                    $respuesta_trigger = null;
    
                    foreach ($global_respuestas as $key => $g_respuesta) {
                        if($g_respuesta['idP'] == $trigger ){
                            $respuesta_trigger = $g_respuesta;
                            break;
                        }
                    }
    
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
    
                            //$html = $html .  'CONDICION PARA TEXTO';
                            if(($condition->{'condicion'} == 'no_vacio') && ($respuesta_trigger['valor'] !== null )){
                                //$html = $html .  "Se cumple no_vacio y con resultado";
                                $flag_satisfy = true;
                            }else{
                                //$html = $html .  "No se cumple no_vacio y con resultado";
                            }
    
                            if(($condition->{'condicion'} == 'vacio') && ($respuesta_trigger['valor'] === null )){
                                //$html = $html .  "Se cumple vacio y sin resultado";
                                $flag_satisfy = true;
                            }else{
                                //$html = $html .  "No se cumple vacio y sin resultado";
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
    
                            //$html = $html .  ' CONDICION PARA RADIO/CHECK ';
                            if(($condition->{'condicion'} == 'marcado') && (($respuesta_trigger['valor'] !== "-1" )&&($respuesta_trigger['valor'] !== "-#$%-" ))){
                                //$html = $html .  "Se cumple marcado y con resultado";
                                $flag_satisfy = true;
                                //$html = $html .  $respuesta_trigger['valor'];
                                //$html = $html .  "CUMPLIÓ";
                                //print_r($respuesta_trigger);
                            }else{
                                //$html = $html .  "No se cumple marcado y con resultado";
                            }
    
                            if(($condition->{'condicion'} == 'no_marcado') && (($respuesta_trigger['valor'] === "-1" )||($respuesta_trigger['valor'] == "-#$%-" ))){
                                //$html = $html .  "Se cumple no_marcado y sin resultado";
                                $flag_satisfy = true;
                                //$html = $html .  $respuesta_trigger['valor'];
                                //$html = $html .  "NO CUMPLIO";
                                //print_r($respuesta_trigger);
                            }else{
                                //$html = $html .  "No se cumple no_marcado y sin resultado";
                            }
                    }
    
                    $behavioral_condition_satisfied  = $condition->{'comportamiento_condicion_cumplida'};
                    $behavioral_condition_not_satisfied  = $condition->{'comportamiento_condicion_no_cumplida'};

                    
                    if($flag_satisfy){
                        foreach ($behavioral_condition_satisfied  as $keyCCC => $behaviors) {
                            //$permissions_to_script = $permissions_to_script . dphpforms_generate_permits_information($behaviors, $ROL);
                            array_push( $permissions_to_script, dphpforms_generate_permits_information($behaviors, $ROL) );
                        }
                    }else{
                        foreach ($behavioral_condition_not_satisfied  as $keyCCNC => $behaviors) {
                            //$permissions_to_script = $permissions_to_script . dphpforms_generate_permits_information($behaviors, $ROL);
                            array_push( $permissions_to_script, dphpforms_generate_permits_information($behaviors, $ROL) );
                        }
                    }
                }
            }
        };

        $permissions_behaviors_to_script = array(
            'behaviors_permissions' => $permissions_to_script
        );

        $html = $html . '<div id="permissions_information" style="display:none;">' . json_encode($permissions_behaviors_to_script) . '</div>';

        

        return $html;

    }
   
?>