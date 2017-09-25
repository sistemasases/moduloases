<<<<<<< HEAD
<<<<<<< HEAD
=======
>>>>>>> db_management
<?php
require_once('query.php');

/**
 * Retorna un elemento html fieldset con los campos encontrados en la consulta  
 * realizada por el metodo getRiskList()
 *  <fieldset id="riesgo">
        <legend>Riesgo</legend>
        <input type="checkbox" name="chk_risk[]" value="academic_risk">Académico<br>
        <input type="checkbox" name="chk_risk[]" value="social_risk">Socioeducativo<br>
        .
        .
        .
    </fieldset>
    @author Edgar Mauricio Ceron
 */
$array = getRiskList();
$html = '<fieldset id="riesgo">
                 <legend>Riesgo</legend>';
foreach($array as $riesgo){
        $value = $riesgo->nombre;
        $label = $riesgo->descripcion;
        $input = '<input type="checkbox" name="chk_risk[]" value="'.$value.'">'.$label.'<br>';
        $html = $html.$input;
}
$html = $html."</fieldset>";
echo $html;
<<<<<<< HEAD
=======
<?php
require_once('query.php');

/**
 * Retorna un elemento html fieldset con los campos encontrados en la consulta  
 * realizada por el metodo getRiskList()
 *  <fieldset id="riesgo">
        <legend>Riesgo</legend>
        <input type="checkbox" name="chk_risk[]" value="academic_risk">Académico<br>
        <input type="checkbox" name="chk_risk[]" value="social_risk">Socioeducativo<br>
        .
        .
        .
    </fieldset>
    @author Edgar Mauricio Ceron
 */
$array = getRiskList();
$html = '<fieldset id="riesgo">
                 <legend>Riesgo</legend>';
foreach($array as $riesgo){
        $value = $riesgo->nombre;
        $label = $riesgo->descripcion;
        $input = '<input type="checkbox" name="chk_risk[]" value="'.$value.'">'.$label.'<br>';
        $html = $html.$input;
}
$html = $html."</fieldset>";
echo $html;
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
=======
>>>>>>> db_management
//echo json_encode($html);