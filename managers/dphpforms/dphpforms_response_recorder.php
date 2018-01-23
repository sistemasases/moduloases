<?php 
    require_once(dirname(__FILE__). '/../../../../config.php');

    global $DB;

    $FORM_ID = $_GET['id'];
    $ROL = $_GET['rol'];
    
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

    echo '
    
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
    ';

    

    $result = array_values($result);
    print_r($result);
    $row = $result[0];
    $form_name = $row->{'nombre'};
    $form_name_formatted = strtolower($form_name);
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
    while($row){
        $campo = $row->{'campo'} ;
        $enunciado = $row->{'enunciado'};
        $atributos = json_decode($row->{'atributos_campo'});

        //Consulta de permisos
        $sql_permisos = '
            SELECT * FROM {talentospilos_df_per_form_pr} WHERE id_formulario_pregunta = '.$row->{'mod_id_formulario_pregunta'}.'
        ';

        $result_permisos = $DB->get_record_sql($sql);
        print_r($result_permisos);
        $permisos = pg_fetch_row($result_permisos);
        $permisos_JSON = json_decode($permisos[2]);

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
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="'.$atributos->{'type'}.'" placeholder="'.$atributos->{'placeholder'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'TEXTAREA'){
                        echo $enunciado . ':<br>';
                        echo ' <textarea id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" name="'. $row->{'mod_id_formulario_pregunta'} .'" '.$enabled.'></textarea><br>' . "\n";
                    }

                    if($campo == 'DATE'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="date" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.'><br>' . "\n";
                    }
                    
                    if($campo == 'DATETIME'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="datetime-local" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'TIME'){
                        echo $enunciado . ':<br>';
                        echo ' <input id="'.$row->{'mod_id_formulario_pregunta'}.'" class="form-control" type="time" name="'.$row->{'mod_id_formulario_pregunta'}.'" '.$enabled.'><br>' . "\n";
                    }

                    if($campo == 'RADIOBUTTON'){
                        $opciones = json_decode($row->{'opciones_campo'});
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);
                        echo '
                        <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="-#$%-">
                        <label id="'.$row->{'mod_id_formulario_pregunta'}.'">'.$enunciado.'</label>';
                        echo '<div class="opcionesRadio" style="margin-bottom:0.4em">';
                        for($i = 0; $i < $number_opciones; $i++){
                            $opcion = (array) $array_opciones[$i];
                            echo '
                                <div id="'.$row->{'mod_id_formulario_pregunta'}.'" name="'.$row->{'mod_id_formulario_pregunta'}.'" class="radio">
                                    <label><input type="radio" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" name="optradio" '.$enabled.'>'.$opcion['enunciado'].'</label>
                                </div>
                            
                            ' . "\n";
                        }
                        echo '<a href="javascript:void(0);" class="limpiar btn btn-xs btn-default" >Limpiar</a>
                            </div>
                        ' . "\n";
                    }

                    if($campo == 'CHECKBOX'){
                        $opciones = json_decode($row->{'opciones_campo'});
                        $array_opciones = (array)$opciones;
                        $number_opciones = count($array_opciones);
                        echo '
                        <label id="'.$row->{'mod_id_formulario_pregunta'}.'">'.$enunciado.'</label>';
                        for($i = 0; $i < $number_opciones; $i++){
                            $opcion = (array) $array_opciones[$i];
                            echo '
                            <div class="checkbox">
                                <input type="hidden" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="-1">
                                <label><input type="checkbox" name="'.$row->{'mod_id_formulario_pregunta'}.'" value="'.$opcion['valor'].'" '.$enabled.'>'.$opcion['enunciado'].'</label>
                            </div>
                            ' . "\n";
                        }

                        
                    }

                }

                break;

            }
        }


        $row = pg_fetch_row($result);
    }
    echo ' <hr style="border-color:red"><button type="submit" class="btn btn-sm btn-default">Registrar</button>' . "\n";
    echo ' </form>' . "\n";


    //Escritura de reglas en JAVASCRIPT (ALPHA)
    //Reglas
    /*
    $scriptReglas = null;
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
            $scriptReglas = $scriptReglas . '
            
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

    echo '
    
        </div>
    </div>
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        $(".limpiar").click(function(){
            $(this).parent().find("div").each(function(){
                $(this).find("label").find("input").prop("checked", false);
            });
        });
    </script>
    <script>
        '.$scriptReglas.'
    </script>
    </body>
</html>
    
    ';

    // Fin de construcción
   
?>