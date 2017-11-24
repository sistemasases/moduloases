<?php
require_once('pilos_tracking_lib.php');

/*
 * Funciones del módulo seguimientos pilos que se utilizarán en la view. 
 */
//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************
//MÉTODOS PARA EL SEPARACIÓN POR SEMESTRES
//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************

/**
 * Función que evalua si existen seguimientos
 * @see has_tracking($seguimientos)
 * @param $seguimientos ---> string del html
 * @return string
 **/
function has_tracking($seguimientos){
    $table="";
    if($seguimientos==""){
     $table.="<p class='text-center'><strong>No existen seguimientos en el periodo seleccionado</strong></p>";
    }else{
     $table.=$seguimientos;
    }
    return $table;
}

/**
 * Función que obtiene el select organizado de los periodos existentes
 * @see get_period_select($periods)
 * @param $periods ---> periodos existentes
 * @return Array
 **/
function get_period_select($periods){
    $table ="";
    $table.='<div class="container"><form class="form-inline">';
    $table.='<div class="form-group"><label for="persona">Periodo</label><select class="form-control" id="periodos">';
    foreach($periods as $period){
        $table.='<option value="'.$period->id.'">'.$period->nombre.'</option>';
     }
    $table.='</select></div>';
    return $table;
}

/**
 * Función que obtiene el select organizado de las personas con rol _ps 
 * @see get_people_select($periods)
 * @param $periods ---> periodos existentes
 * @return Array
 **/
function get_people_select($people){
 $table="";
 $table.='<div class="form-group"><label for="persona">Persona</label><select class="form-control" id="personas">';
    foreach($people as $person){
            $table.='<option value="'.$person->id_usuario.'">'.$person->username." - ".$person->firstname." ".$person->lastname.'</option>';
     }
    $table.='</select></div>';
    $table.='<span class="btn btn-info" id="consultar_persona" type="button">Consultar</span></form></div>';
    return $table;
}


//funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
//se usara para la creacion del toogle
    function transformarConsultaSemestreArray($pares,$grupal,$arregloSemestres,$instanceid,$role) {

        $arregloSemestreYPersonas = [];


         foreach ($arregloSemestres as $semestre) {
             $arregloAuxiliar = [];

            array_push($arregloAuxiliar,$semestre->id);
            array_push($arregloAuxiliar,$semestre->nombre);
            array_push($arregloAuxiliar,$semestre->fecha_inicio);
            array_push($arregloAuxiliar,$semestre->fecha_fin);

            //se asigna a esta posicion un texto html correspondiente a la informacion del profesional
            array_push($arregloAuxiliar,profesionalUser($pares,$grupal,$arregloPracticantes[$practicante][0],$instanceid,$role));
            array_push($arregloSemestreYPersonas,$arregloAuxiliar);
        }
         return $arregloSemestreYPersonas;

    }


//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************
//MÉTODOS PARA EL PROFESIONAL
//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************

function get_conteo_profesional($professionalpracticants){
    $revisado_profesional=0;
    $no_revisado_profesional=0;
    $total=0;
    $enunciado ='';


    for($profesional=0;$profesional<count($professionalpracticants);$profesional++){

        $revisado_profesional+=$professionalpracticants[$profesional][2];
        $no_revisado_profesional+=$professionalpracticants[$profesional][3];
        $total+=$professionalpracticants[$profesional][4];

    }


    //$enunciado = '<div class="row"><div class="col-sm-6"><h2>Información profesionales </h2><div class="panel panel-default"> <div class="panel-body"><h4 class="panel-title"><span class="pull-left"> Revisados  : <b>'.$revisado_profesional.'</b> - NO Revisados : <b>'.$no_revisado_profesional.'</b> - Total  : <b>'.$total.'</b> </span></h4></div></div></div></div>';

    return $enunciado;
}


function profesionalUser(&$pares,&$grupal,$id_prof,$instanceid,$rol,$semester,$sistemas=false){
    
    $arregloPracticanteYMonitor = [];
    $fechas = [];
    $fechas[0] = $semester[0];
    $fechas[1] = $semester[1];
    $fechas[2] = $semester[2];
    $professionalpracticants= get_practicantes_profesional($id_prof,$instanceid,$semester[2]);
    $conteo_profesional = get_conteo_profesional($professionalpracticants);
    $arregloPracticanteYMonitor=transformarConsultaProfesionalArray($pares,$grupal,$professionalpracticants,$instanceid,$rol,$fechas,$sistemas);

    return crearTablaYToggleProfesional($arregloPracticanteYMonitor,$conteo_profesional);

}



//funcion que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
//se usara para la creacion del toogle
    function transformarConsultaProfesionalArray($pares,$grupal,$arregloPracticantes,$instanceid,$role,$fechas_epoch,$sistemas) {

        $arregloPracticanteYMonitor = [];

        for ($practicante=0;$practicante<count($arregloPracticantes);$practicante++) {
            $arregloAuxiliar = [];

            array_push($arregloAuxiliar,$arregloPracticantes[$practicante][0]);
            array_push($arregloAuxiliar,$arregloPracticantes[$practicante][1]);

            //se asigna a esta posicion un texto html correspondiente a la informacion del practicante
            array_push($arregloAuxiliar,practicanteUser($pares,$grupal,$arregloPracticantes[$practicante][0], $instanceid,$role,$fechas_epoch,$sistemas));
            array_push($arregloAuxiliar,$arregloPracticantes[$practicante][2]);
            array_push($arregloAuxiliar,$arregloPracticantes[$practicante][3]);
            array_push($arregloAuxiliar,$arregloPracticantes[$practicante][4]);
            array_push($arregloPracticanteYMonitor,$arregloAuxiliar);
        }
        return $arregloPracticanteYMonitor;

    }

    //se crea el toogle del profesional el cual tiene cada uno de los practicantesr asignados al profesional
    function crearTablaYToggleProfesional($arregloPracticanteYMonitor,$conteo_profesional) {
        $stringRetornar = "";
        $stringRetornar .=$conteo_profesional; 
        for ($practicante=0;$practicante<count($arregloPracticanteYMonitor);$practicante++) {
            $stringRetornar .= '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading profesional" style="background-color: #938B8B;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse'.$arregloPracticanteYMonitor[$practicante][0].'">'.$arregloPracticanteYMonitor[$practicante][1].'</a><span>R.P  : <b><label for="revisado_practicante_'.$arregloPracticanteYMonitor[$practicante][0].'">0</label></b> - NO R.P : <b><label for="norevisado_practicante_'.$arregloPracticanteYMonitor[$practicante][0].'">0</label></b> - Total  : <b><label for="total_practicante_'.$arregloPracticanteYMonitor[$practicante][0].'">0</label></b> </span></h4></div>';
            $stringRetornar .= '<div id="collapse'.$arregloPracticanteYMonitor[$practicante][0].'" class="panel-collapse collapse"><div class="panel-body">';


            //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
            $stringRetornar .= $arregloPracticanteYMonitor[$practicante][2];
            $stringRetornar .= '</div></div></div></div>';
        }

        return $stringRetornar;
    }


//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************
//MÉTODOS PARA EL PRACTICANTE
//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************

function practicanteUser(&$pares,&$grupal,$id_pract,$instanceid,$rol,$semester,$sistemas=false){
    
    $arregloMonitorYEstudiantes = [];
    $fechas = [];
    $fechas[0] = $semester[0];
    $fechas[1] = $semester[1];
    $fechas[2] = $semester[2];

    $practicantmonitors= get_monitores_practicante($id_pract,$instanceid,$semester[2]);
    $arregloMonitorYEstudiantes=transformarConsultaPracticanteArray($pares,$grupal,$practicantmonitors,$instanceid,$rol,$id_pract,$fechas,$sistemas);
    return crearTablaYTogglePracticante($arregloMonitorYEstudiantes);

}

/*Función que transforma el arreglo retornado por la peticion en un arreglo que posteriormente
se usara para la creacion del toogle.*/

 function transformarConsultaPracticanteArray($pares,$grupal,$arregloMonitores, $instanceid,$role,$id_pract,$fechas_epoch,$sistemas=false) {

        $arregloMonitorYEstudiantes = [];
        $fechas = [];



        for ($monitor=0;$monitor<count($arregloMonitores);$monitor++) {
            $arregloAuxiliar = [];
            $cantidad = 0;

            array_push($arregloAuxiliar,$arregloMonitores[$monitor][0]);
            array_push($arregloAuxiliar,$arregloMonitores[$monitor][1]);
            array_push($arregloAuxiliar,monitorUser($pares,$grupal,$arregloMonitores[$monitor][0], $monitor, $instanceid,$role,$fechas_epoch,$sistemas,$id_pract));



            $cantidades =get_cantidad_seguimientos_monitor($arregloMonitores[$monitor][0],$instanceid);

            $revisado_profesional=$cantidades[0]->count;
            $no_revisado_profesional = $cantidades[1]->count;
            $total_registros=$cantidades[2]->count;

            array_push($arregloAuxiliar,$revisado_profesional);
            array_push($arregloAuxiliar,$no_revisado_profesional);
            array_push($arregloAuxiliar,$total_registros);

            array_push($arregloMonitorYEstudiantes,$arregloAuxiliar);


        }
     return $arregloMonitorYEstudiantes;

}

/*Se crea el toogle del practicante el cual tiene cada uno de los monitores asignados al practicante*/

    function crearTablaYTogglePracticante($arregloMonitorYEstudiantes) {
        $stringRetornar = "";

        for ($monitor=0;$monitor<count($arregloMonitorYEstudiantes);$monitor++) {
            $stringRetornar .= '<div class="panel-group"><div class="panel panel-default" ><div class="panel-heading practicante" style="background-color: #AEA3A3;"><h4 class="panel-title"><a data-toggle="collapse"  href="#collapse'.$arregloMonitorYEstudiantes[$monitor][0].'">'.$arregloMonitorYEstudiantes[$monitor][1] .'</a><span> R.P  : <b><label for="revisado_monitor_'.$arregloMonitorYEstudiantes[$monitor][0].'">0</label></b> - NO R.P : <b><label for="norevisado_monitor_'.$arregloMonitorYEstudiantes[$monitor][0].'">0</label></b> - Total  : <b><label for="total_monitor_'.$arregloMonitorYEstudiantes[$monitor][0].'">0</label></b> </span></h4></div>';
            $stringRetornar .= '<div id="collapse'.$arregloMonitorYEstudiantes[$monitor][0].'" class="panel-collapse collapse"><div class="panel-body">';
            //en la tercer posicion del arreglo se encuentra un texto html con un formato especifico
            $stringRetornar .= $arregloMonitorYEstudiantes[$monitor][2];
            $stringRetornar .= '</div></div></div></div>';
        }
        return $stringRetornar;
    }




//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************
//MÉTODOS PARA EL MONITOR
//******************************************************************************************************
//******************************************************************************************************
//******************************************************************************************************


/*Realiza toda la gestión para tener un arreglo final ordenado deacuerdo a los estudiantes de un monitor*/


function monitorUser($pares,$grupal,$codigoMonitor, $noMonitor, $instanceid,$role,$fechas,$sistemas=false,$codigoPracticante=null){

    $fecha_epoch = [];
    $fecha_epoch[0] =strtotime($fechas[0]);
    $fecha_epoch[1] =strtotime($fechas[1]);
    $semestre_periodo =get_current_semester_byinterval($fechas[0],$fechas[1]);

    $monitorstudents=get_seguimientos_monitor($codigoMonitor,$instanceid,$fecha_epoch,$semestre_periodo);

    transformarConsultaMonitorArray($monitorstudents,$pares,$grupal,$codigoMonitor,$noMonitor,$instanceid,$role);
    
        //metodo que agrupa informacion de los seguimientos de pares por el codigo
        $arregloImprimirPares = agrupar_informacion($pares, 20);

        //metodo que agrupa informacion de los seguimientos grupales por los codigos
        $arregloImprimirGrupos = agrupar_informacion($grupal, 12);

        //metodo que deja un solo registro grupal con el mismo codigo y concatena nombres y codigos de los estudiantes
        $arregloImprimirGrupos = agrupar_Seguimientos_grupales($arregloImprimirGrupos);

        //se ordena los seguimientos de cada estudiante segun la fecha
        for ($grupo=0;$grupo<count($arregloImprimirPares);$grupo++) {
            ordenaPorColumna($arregloImprimirPares[$grupo], 19);
        }



         //se retorna la informacion del toogle creado desde el punto del monitor
        return crearTablaYToggle($arregloImprimirPares, $noMonitor, $arregloImprimirGrupos, $codigoMonitor, $codigoPracticante,$role,$sistemas);

}

/*Transforma la consulta realizada en un array que será utilizado para construir el Toogle.*/
function transformarConsultaMonitorArray($array,&$pares,&$grupal,$codigoMonitor, $noMonitor, $instanceid,$role,$codigoPracticante=null){

    foreach($array as $seguimiento){
        if ($seguimiento->tipo == "PARES") {

        $array_auxiliar = [];
        $fecha = gmdate('M/d/Y', ($seguimiento->fecha));
        $fecha_calendario = new DateTime("@$seguimiento->fecha");  // convert UNIX timestamp to PHP DateTime

        $nombre= $seguimiento->nombre_estudiante;
        $apellido=$seguimiento->apellido_estudiante;
        $profesion=$seguimiento->profesional;
        $practicante=$seguimiento->practicantee;

        $nombre_enviar = "";
          if ($apellido == "" || strlen($apellido) == 0) {
                    $nombre_enviar = $nombre;
                }
          else {
                   $nombre_enviar = $nombre." ".$apellido;
         }
         $nombrem = $seguimiento->nombre_monitor_creo;
         $apellidom = $seguimiento->apellido_monitor_creo;
         $nombremon_enviar = "";

          if ($apellidom == "" || strlen($apellidom) == 0) {
                    $nombremon_enviar = $nombrem;
                }
                else {
                    $nombremon_enviar = $nombrem." ".$apellidom;
                }
            array_push($array_auxiliar,$nombre_enviar); //0
            array_push($array_auxiliar,$fecha); //1    
            array_push($array_auxiliar,$seguimiento->hora_ini); //2
            array_push($array_auxiliar,$seguimiento->hora_fin); //3
            array_push($array_auxiliar,$seguimiento->lugar); //4
            array_push($array_auxiliar,$seguimiento->tema); //5
            array_push($array_auxiliar,$seguimiento->actividades); //6
            array_push($array_auxiliar,$seguimiento->individual); //7
            array_push($array_auxiliar,$seguimiento->individual_riesgo); //8
            array_push($array_auxiliar,$seguimiento->familiar_desc); //9
            array_push($array_auxiliar,$seguimiento->familiar_riesgo); //10
            array_push($array_auxiliar,$seguimiento->academico); //11
            array_push($array_auxiliar,$seguimiento->academico_riesgo); //12
            array_push($array_auxiliar,$seguimiento->economico); //13
            array_push($array_auxiliar,$seguimiento->economico_riesgo); //14
            array_push($array_auxiliar,$seguimiento->vida_uni); //15
            array_push($array_auxiliar,$seguimiento->vida_uni_riesgo); //16
            array_push($array_auxiliar,$seguimiento->observaciones); //17
            array_push($array_auxiliar,"saltar"); //18 borra
            array_push($array_auxiliar,$seguimiento->fecha); // 19
            array_push($array_auxiliar,$seguimiento->id_estudiante); // 20 idtalentos
            array_push($array_auxiliar,$nombremon_enviar); // 21
            array_push($array_auxiliar,$seguimiento->objetivos); // 22
            array_push($array_auxiliar,$seguimiento->id_seguimiento); // 23
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_revisados); // 24
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_norevisados); // 25
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_total); // 26
            array_push($array_auxiliar,$seguimiento->profesional); // 27
            array_push($array_auxiliar,$seguimiento->practicante); // 28
            array_push($array_auxiliar,$fecha_calendario->format('Y-m-d')); //29 formato fecha para el calendario
            array_push($array_auxiliar,$seguimiento->individual_riesgo); //30 riesgo individual

            array_push($pares,$array_auxiliar);


       }elseif($seguimiento->tipo == "GRUPAL"){
        $array_auxiliar = [];


               // $fecha = transformarFecha(consulta[registro]["fecha"]);
                $nombre = $seguimiento->nombre_estudiante;
                $apellido = $seguimiento->apellido_estudiante;
                $nombre_enviar = "";
                if ($apellido == "" || strlen($apellido) == 0) {
                    $nombre_enviar = $nombre;
                }
                else {

                    $nombre_enviar = $nombre." ".$apellido;
                }

                $nombrem = $seguimiento->nombre_monitor_creo;
                $apellidom = $seguimiento->apellido_monitor_creo;
                $nombremon_enviar = "";

                if ($apellidom == "" || strlen($apellidom)== 0) {
                    $nombremon_enviar = $nombrem;
                }
                else {
                    $nombremon_enviar = $nombrem." ".$apellidom;
                }
            array_push($array_auxiliar,$nombre_enviar);
            array_push($array_auxiliar,$fecha);    
            array_push($array_auxiliar,$seguimiento->hora_ini); 
            array_push($array_auxiliar,$seguimiento->hora_fin); 
            array_push($array_auxiliar,$seguimiento->lugar); 
            array_push($array_auxiliar,$seguimiento->tema); 
            array_push($array_auxiliar,$seguimiento->actividades); 
            array_push($array_auxiliar,$seguimiento->objetivos); 
            array_push($array_auxiliar,$seguimiento->observaciones);
            array_push($array_auxiliar,"saltar"); //9 borrar
            array_push($array_auxiliar,$seguimiento->fecha); // 10
            array_push($array_auxiliar,$seguimiento->id_estudiante); // 11
            array_push($array_auxiliar,$seguimiento->id_seguimiento); // 12
            array_push($array_auxiliar,$nombre_enviar); // 13
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_revisados_grupal); // 14
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_norevisados_grupal); // 15
            array_push($array_auxiliar,$seguimiento->registros_estudiantes_total_grupal); // 16
            
            array_push($grupal,$array_auxiliar);

       }
    }
        
}

    //Función que ordena un arreglo segun la columna definida de menos valor a mayor
    function ordenaPorColumna(&$arreglo, $col) {

        $aux;

        // Recorro la columna selecciona
        for ($i = 0; $i < count($arreglo); $i++) {
            for ($j = ($i+1); $j < count($arreglo); $j++) {
                // Verifico si el elemento en la posición [i][col] es mayor que el de la posición [j][col]
               
                if (intval($arreglo[$i][$col]) < intval($arreglo[$j][$col])) {
                    // Recorro las filas seleccionadas (i, j) e intercambio los elementos
                    // Declaro la variable k para controlar la posición (columnas) en la fila
                    for ($k = 0; $k < count($arreglo[$i]); $k++) {
                        // Intercambio los elementos de las filas seleccionadas columna por columna
                        $aux = $arreglo[$i][$k];
                        $arreglo[$i][$k] = $arreglo[$j][$k];
                        $arreglo[$j][$k] = $aux;
                    }
                }
            }
        }
    }


 /*Función para agrupar los seguimientos grupales segun el ID
 */
    function agrupar_Seguimientos_grupales($arreglo) {
        $NuevoArregloGrupal = [];
        for ($elementoRevisar=0;$elementoRevisar<count($arreglo);$elementoRevisar++){
             $arregloAuxiliar = $arreglo[$elementoRevisar][0];
             $nombres="";
             $nombresImpirmir="";
             $codigos="";
             $contador=1;

             //Función que captura tanto los nombres como los codigos y crea un texto
             //para cada uno los cuales seran usado para ponerse en la tabla
             for ($tuplaGrupo = 0; $tuplaGrupo < count($arreglo[$elementoRevisar]); $tuplaGrupo++) {
                $cuenta=count($arreglo[$elementoRevisar])-1;
                 if ($tuplaGrupo == $cuenta){
                    $nombres .= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                    $nombresImpirmir .= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                    $codigos .= $arreglo[$elementoRevisar][$tuplaGrupo][11];
                 }else {
                     $nombres .= $arreglo[$elementoRevisar][$tuplaGrupo][0];
                     $nombresImpirmir .= $arreglo[$elementoRevisar][$tuplaGrupo][0] .",";
                     $codigos.= $arreglo[$elementoRevisar][$tuplaGrupo][11];
              }
            }
             //se al arreglo los nombres y los codigos concatenados al final del arreglo
             $arregloAuxiliar[0] = $nombres;
             $arregloAuxiliar[11] = $codigos;
             array_push($arregloAuxiliar,$nombresImpirmir);
             array_push($NuevoArregloGrupal,$arregloAuxiliar);
        }
        return $NuevoArregloGrupal;
    }


/*Función utilizada para agrupar la información del array deacuerdo a parametros especificos de la variable
$campoComparar.
*/


function agrupar_informacion($infoMonitor,$campoComparar){
         $nuevoArreglo = [];
         for ($i=0;$i<count($infoMonitor);$i++) {
             //se inician variables
             $confirmarAnanir = "si";
             $posicion = 0;
             //si es el primer elemento del arreglo siempre se añadira
             if (count($nuevoArreglo) != 0) {
                //si ya hay elementos en el arreglo
                 for ($j=0;$j<count($nuevoArreglo);$j++) {
                     //se verifica que no exista otra persona con el mismo nombre
                    if ($infoMonitor[$i][$campoComparar] == $nuevoArreglo[$j][0][$campoComparar]) {
                     //si existe entonces no se añadira un nuevo al arreglo sino uno nuevo a la posicion
                         $confirmarAnanir = "no";
                         $posicion = $j;
                     }
                }
             }
             //si se retorna si es decir que no existen registros del estudiante
             if ($confirmarAnanir == "si") {
                 $arregloEstudiante = array();
                 //se agrega al arreglo
                 $tamano = count($nuevoArreglo);
                 array_push($arregloEstudiante,$infoMonitor[$i]);
                 $nuevoArreglo[$tamano] = $arregloEstudiante;
             }else {
                 $arregloEstudiante = array();
                 $arregloEstudiante = $nuevoArreglo[$posicion];
                 array_push($arregloEstudiante,$infoMonitor[$i]);
             //si no es prque ya tiene registro asi que se agrega registro al estudiante
                 $nuevoArreglo[$posicion] = [];
                 $nuevoArreglo[$posicion] = $arregloEstudiante;

             }
        }
        return $nuevoArreglo;
         }

/*Función que crea la tabla de los estudiantes que pertenecen a un monitor determinado */

function crearTablaYToggle($arregloImprimirPares, $monitorNo, $arregloImprimirGrupos, $codigoEnviarN1, $codigoEnviarN2,$rol,$sistemas=false) {
        $stringRetornar = "";

        //se recorre cada estudiante
        for ($student=0;$student<count($arregloImprimirPares);$student++) {
            $stringRetornar .= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading pares" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse' .$monitorNo. $arregloImprimirPares[$student][0][20] .'">'. $arregloImprimirPares[$student][0][0] .'<span> R.P  : <b><label for="revisado_pares_'.$codigoEnviarN1.'_'.$student.'">'. $arregloImprimirPares[$student][0][24] .'</label></b> - NO R.P : <b><label for="norevisado_pares_'.$codigoEnviarN1.'_'.$student.'">'. $arregloImprimirPares[$student][0][25] .'</label></b> - Total  : <b>'. $arregloImprimirPares[$student][0][26] .'</b> </span></a></h4></div>';
            $stringRetornar .= '<div id="collapse' .$monitorNo. $arregloImprimirPares[$student][0][20] .'" class="panel-collapse collapse"><div class="panel-body">';

            //se crea un toogle para cada seguimiento que presente dicho estudiante
            for ($tupla=0;$tupla<count($arregloImprimirPares[$student]);$tupla++) {
                $stringRetornar .= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapse_' .$monitorNo. $arregloImprimirPares[$student][0][20] .$tupla. '"> <label  for="fechatext_'. $arregloImprimirPares[$student][$tupla][23] .'"/ id="fecha_texto_'. $arregloImprimirPares[$student][$tupla][23] .'"> Registro : '. $arregloImprimirPares[$student][$tupla][1] .'</label></a></h4></div>';
                $stringRetornar .= '<div id="collapse_' .$monitorNo. $arregloImprimirPares[$student][0][20] .$tupla. '" class="panel-collapse collapse"><div class="panel-body hacer-scroll" style="overflow-y"><table class="table table-hover $students_table" id="$students_table'. $arregloImprimirPares[$student][0][20].$arregloImprimirPares[$student][0][19] .'">';
                $stringRetornar .= '<thead><tr><th></th><th></th><th></th></tr></thead>';
                $stringRetornar .= '<tbody id='.$tupla .'_'. $arregloImprimirPares[$student][$tupla][23] .'>';
                $stringRetornar .= '<div class="table-info-pilos col-sm-12"><div class="col-sm-4" style="display: none" id="titulo_fecha_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>FECHA :</b><input id="fecha_'. $arregloImprimirPares[$student][$tupla][23] .'" type="date" class="no-borde-fondo fecha"  value="'. $arregloImprimirPares[$student][$tupla][29] .'"/></div></div>';
                $stringRetornar .= '<div class"table-info-pilos col-sm-12"><div class="col-sm-4 "><b>LUGAR:</b> <input id="lugar_'. $arregloImprimirPares[$student][$tupla][23] .'"class="no-borde-fondo editable lugar" readonly value="'. $arregloImprimirPares[$student][$tupla][4] .'"></div><div class="col-md-4" id="hora_inicial_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display: "><label for="h_ini" class="select-hour">HORA INICIO</label><input class="no-borde-fondo fecha" readonly id="h_inicial_texto_'. $arregloImprimirPares[$student][$tupla][23] .'" value="'. $arregloImprimirPares[$student][$tupla][2] .' "></div><div class="col-md-4" id="mod_hora_ini_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display: none"><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA INICIO</label><select  class="select-hour" id="h_ini_'. $arregloImprimirPares[$student][$tupla][23] .'" name="h_ini" ></select><label class="col-md-1 col-xs-1" for="m_ini">:</label><select class="select-hour" id="m_ini_'. $arregloImprimirPares[$student][$tupla][23] .'"  name="m_ini"></select></div><div class="col-md-4" id="hora_final_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display: "><label for="h_ini" class="form-control-label col-md-4 col-xs-4">HORA FIN </label><input class="no-borde-fondo fecha" readonly id="h_final_texto_'. $arregloImprimirPares[$student][$tupla][23] .'" value="'. $arregloImprimirPares[$student][$tupla][3] .'"></div><div class="col-md-4" id="mod_hora_final_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display: none"><label for="h_fin" class="form-control-label col-md-4 col-xs-4">HORA FIN</label><select  class="select-hour" id="h_fin_'. $arregloImprimirPares[$student][$tupla][23] .'" name="h_fin" ></select><label class="col-md-1 col-xs-1" for="m_fin">:</label><select class="select-hour" id="m_fin_'. $arregloImprimirPares[$student][$tupla][23] .'"  name="m_fin"></select></div></div>';
                $stringRetornar .= '<div class="table-info-pilos col-sm-12"><b>TEMA:</b><br><input id="tema_'. $arregloImprimirPares[$student][$tupla][23] .'" class="no-borde-fondo editable tema" readonly  value="'. $arregloImprimirPares[$student][$tupla][5] .'"></div>';
                $stringRetornar .= '<div class="table-info-pilos col-sm-12"><b>OBJETIVOS:</b><br><textarea id="objetivos_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][22] .'</textarea></div></div>';

                $riesgo = "";
                $valor = -1;
                //se verifica el tipo de riesgo y asi mismo se añadira
                //la clase para la identificacion
                if ($arregloImprimirPares[$student][$tupla][8] == 1) {
                    $riesgo = "bajo";
                    $valor = 1;
                }
                else if ($arregloImprimirPares[$student][$tupla][8] == 2) {
                    $riesgo = "medio";
                    $valor = 2;
                }
                else if ($arregloImprimirPares[$student][$tupla][8] == 3) {
                    $riesgo = "alto";
                    $valor = 3;
                }
                else {
                    $riesgo = "no";
                }

                if ($riesgo != "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .'" id="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>INDIVIDUAL:</b><br><textarea id="obindividual_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][7] .'</textarea><br>RIESGO: '. $riesgo;
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';


                }
                else if ($riesgo == "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .' quitar-ocultar ocultar individual"><b>INDIVIDUAL:</b><br><textarea id="obindividual_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_individual_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline hidden" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="0">No registra';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_individual_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';



                }


                //se verifica el tipo de riesgo y asi mismo se añadira
                //la clase para la identificacion
                if ($arregloImprimirPares[$student][$tupla][10] == 1) {
                    $riesgo = "bajo";
                    $valor = 1;
                }
                else if ($arregloImprimirPares[$student][$tupla][10] == 2) {
                    $riesgo = "medio";
                    $valor = 2;
                }
                else if ($arregloImprimirPares[$student][$tupla][10] == 3) {
                    $riesgo = "alto";
                    $valor = 3;
                }
                else {
                    $riesgo = "no";
                }

                if ($riesgo != "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .'" id="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>FAMILIAR:</b><br><textarea id="obfamiliar_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][9] .'</textarea><br>RIESGO: '. $riesgo;
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class=" col-md-12 top-buffer"></div>';


                }
                else if ($riesgo == "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .' quitar-ocultar ocultar"><b>FAMILIAR:</b><br><textarea id="obfamiliar_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_familiar_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline hidden" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="0">No registra';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_familiar_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';

                }

                //se verifica el tipo de riesgo y asi mismo se añadira
                //la clase para la identificacion
                if ($arregloImprimirPares[$student][$tupla][12] == 1) {
                    $riesgo = "bajo";
                    $valor = 1;
                }
                else if ($arregloImprimirPares[$student][$tupla][12] == 2) {
                    $riesgo = "medio";
                    $valor = 2;
                }
                else if ($arregloImprimirPares[$student][$tupla][12] == 3) {
                    $riesgo = "alto";
                    $valor = 3;
                }
                else {
                    $riesgo = "no";
                }

                if ($riesgo != "no") {

                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .'"id="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>ACADEMICO:</b><br><textarea id="obacademico_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][11] .'</textarea><br>RIESGO: '. $riesgo;
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';


                }
                else if ($riesgo == "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .' quitar-ocultar ocultar"><b>ACADEMICO:</b><br><textarea id="obacademico_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra</div>';
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_academico_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline hidden" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="0">No registra';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_academico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class=" col-md-12 top-buffer"></div>';

                }

                //se verifica el tipo de riesgo y asi mismo se añadira
                //la clase para la identificacion
                if ($arregloImprimirPares[$student][$tupla][14] == 1) {
                    $riesgo = "bajo";
                    $valor = 1;
                }
                else if ($arregloImprimirPares[$student][$tupla][14] == 2) {
                    $riesgo = "medio";
                    $valor = 2;
                }
                else if ($arregloImprimirPares[$student][$tupla][14] == 3) {
                    $riesgo = "alto";
                    $valor = 3;
                }
                else {
                    $riesgo = "no";
                }

                if ($riesgo != "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .'" id="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>ECONOMICO:</b><br><textarea id="obeconomico_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][13] .'</textarea><br>RIESGO: '. $riesgo;
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12  top-buffer"></div>';



                }
                else if ($riesgo == "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. riesgo .' quitar-ocultar ocultar"><b>ECONOMICO:</b><br><textarea id="obeconomico_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_economico_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline hidden" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="0">No registra';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_economico_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';

                }
                //se verifica el tipo de riesgo y asi mismo se añadira
                //la clase para la identificacion
                if ($arregloImprimirPares[$student][$tupla][16] == 1) {
                    $riesgo = "bajo";
                    $valor = 1;
                }
                else if ($arregloImprimirPares[$student][$tupla][16] == 2) {
                    $riesgo = "medio";
                    $valor = 2;
                }
                else if ($arregloImprimirPares[$student][$tupla][16] == 3) {
                    $riesgo = "alto";
                    $valor = 3;
                }
                else {
                    $riesgo = "no";
                }

                if ($riesgo != "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. $riesgo .'" id="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][15] .'</textarea><br>RIESGO: '. $riesgo;
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';


                }
                else if ($riesgo == "no") {
                    $stringRetornar .= '<div class="table-info-pilos col-sm-12 riesgo_'. riesgo .' quitar-ocultar ocultar"><b>UNIVERSITARIO:</b><br><textarea id="obuniversitario_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly></textarea><br>RIESGO:No registra';
                    $stringRetornar .= '<div class="col-md-12 radio-ocultar ocultar" id="radio_universitario_div'. $arregloImprimirPares[$student][$tupla][23] .'">';
                    $stringRetornar .= '<label class="radio-inline hidden" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'"  value="0">No registra';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">Bajo';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="2">Medio';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" >';
                    $stringRetornar .= '<input type="radio" name="riesgo_universitario_'. $arregloImprimirPares[$student][$tupla][23] .'" value="3">Alto';
                    $stringRetornar .= '</label>';
                    $stringRetornar .= '<label class="radio-inline" ><span style="color:gray;" class="glyphicon glyphicon-erase limpiar" id="clean_individual_risk"></span></label>';
                    $stringRetornar .= '</div></div>';
                    $stringRetornar .= '</td></tr>';
                    $stringRetornar.= '<div class="col-md-12 top-buffer"></div>';

                }

                $stringRetornar .= '<div class="table-info-pilos col-sm-12"><b>OBSERVACIONES:</b><br><textarea id="observacionesGeneral_'. $arregloImprimirPares[$student][$tupla][23] .'" class ="no-borde-fondo editable" readonly>'. $arregloImprimirPares[$student][$tupla][17] .'</textarea></div>';

                $stringRetornar .= '<div class="table-info-pilos col-sm-12"><b>CREADO POR:</b><br>'. $arregloImprimirPares[$student][$tupla][21] .'</div>';

                //----en caso que tenga el rol correspondiente se añade un campo y un boton para
                //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
                if ($rol  == 3 or $rol  == 7 or $sistemas==1) {
                    if ($arregloImprimirPares[$student][$tupla][27] != 1 or ($arregloImprimirPares[$student][$tupla][27] == 1 && $sistemas==1)) {

                        $stringRetornar .= '<div class="table-info-pilos col-sm-12"><b>REPORTAR OBSERVACIÓN</b><br><textarea  id="textarea_'. $arregloImprimirPares[$student][$tupla][23] .'" class="textarea-seguimiento-pilos" name="individual_'. $codigoEnviarN1 .'_'. $codigoEnviarN2 .'_'. $arregloImprimirPares[$student][$tupla][1] .'_'. $arregloImprimirPares[$student][$tupla][0] .'" rows="4" cols="150"></textarea><br>';

                    }
                    if ($arregloImprimirPares[$student][$tupla][27] == 1) {
                        $stringRetornar .= '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional"  id="profesional_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1" checked>R. profesional</label><label class="checkbox-inline">';
                    }
                    else {
                        $stringRetornar .= '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" name="profesional" id="profesional_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">R. profesional</label><label class="checkbox-inline">';
                    }

                    if ($arregloImprimirPares[$student][$tupla][28] == 1) {
                        $stringRetornar .= '<input type="checkbox" name="practicante" id="practicante_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1" checked>R. practicante</label></div>';
                    }
                    else {
                        $stringRetornar .= '<input type="checkbox" name="practicante" id="practicante_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1">R. practicante</label></div>';
                    }
                    if ($arregloImprimirPares[$student][$tupla][27] != 1 or $sistemas ==1 ) {
                        $stringRetornar .= '<div class="col-sm-12"></div><div class="col-sm-4 col" id="enviar_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display: "><span class="btn btn-info btn-lg  botonCorreo" value="'. $arregloImprimirPares[$student][$tupla][23] .'" id="correo_'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Enviar observaciones</span></div><div class="col-sm-4" id="editar_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display:"><span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Editar</span></div><div class="col-sm-4" id="borrar_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display:"><span class="btn btn-info btn-lg botonBorrar"  value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Borrar</span></div></div>';
                        $stringRetornar .= '<div class="col-sm-12"><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Guardar</span></div><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Cancelar</span></div></div><td></tr>';
                    }


                }
                else {
                    if ($rol  == 4) {
                        if ($arregloImprimirPares[$student][$tupla][27] == 1 ) {
                            $stringRetornar .= '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" class="hide" name="profesional"  id="profesional_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1" checked></label><label class="checkbox-inline">';
                        }
                        else {
                            $stringRetornar .= '<div class="col-sm-12"><label class="checkbox-inline"><input type="checkbox" class="hide" name="profesional" id="profesional_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1"></label><label class="checkbox-inline">';
                        }

                        if ($arregloImprimirPares[$student][$tupla][28] == 1 ) {
                            $stringRetornar .= '<input type="checkbox" name="practicante" class="hide"   id="practicante_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1" checked></label></div>';
                        }
                        else {
                            $stringRetornar .= '<input type="checkbox" name="practicante" class="hide"  id="practicante_'. $arregloImprimirPares[$student][$tupla][23] .'" value="1"></label></div>';
                        }
                        if ($arregloImprimirPares[$student][$tupla][27] != 1 or $sistemas==1) {
                        $stringRetornar .= '<div class="col-sm-12"><div class="col-sm-4" id="editar_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display:"><span class="btn btn-info btn-lg botonesSeguimiento botonEditarSeguimiento" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Editar</span></div><div class="col-sm-4" id="borrar_'. $arregloImprimirPares[$student][$tupla][23] .'" style="display:"><span class="btn btn-info btn-lg botonBorrar"  value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Borrar</span></div></div>';
                        $stringRetornar .= '<div class="col-sm-12"><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonModificarSeguimiento ocultar" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Guardar</span></div><div class="col-sm-5"><span class="btn btn-info btn-lg botonesSeguimiento botonCancelarSeguimiento ocultar" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Cancelar</span></div></div><td></tr>';}
                    }

                }


                //cerre el colapsable correspondientes
                $stringRetornar .= '</tbody></table></div></div></div></div>';
            }
            $stringRetornar .= '</div></div></div></div>';
        }

        //si existen seguimiento grupales
        if (count($arregloImprimirGrupos) != 0) {
            $stringRetornar .= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading grupal" style="background-color: #D0C4C4;"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' .$monitorNo. $arregloImprimirGrupos[0][11] .'">SEGUIMIENTOS GRUPALES <span> R.P  : <b><label for="revisado_grupal_'.$codigoEnviarN1.'">'.$arregloImprimirGrupos[0][14].'</label></b> - NO R.P : <b><label for="norevisado_grupal_'.$codigoEnviarN1.'">'.$arregloImprimirGrupos[0][15].'</label></b> - Total  : <b><label for="total_grupal_'.$codigoEnviarN1.'">'. $arregloImprimirGrupos[0][16] .'</b> </span></a></h4></div>';
            $stringRetornar .= '<div id="collapsegroup' .$monitorNo. $arregloImprimirGrupos[0][11] .'" class="panel-collapse collapse"><div class="panel-body">';
            for ($grupo=0; $grupo<count($arregloImprimirGrupos);$grupo++) {
                $stringRetornar .= '<div class="panel-group"><div class="panel panel-default"><div class="panel-heading"><h4 class="panel-title"><a data-toggle="collapse" href="#collapsegroup' .$monitorNo. $grupo .$$arregloImprimirGrupos[$grupo][11] .'">'. $arregloImprimirGrupos[$grupo][1] .'</a></h4></div>';
                $stringRetornar .= '<div id="collapsegroup' .$monitorNo. $grupo .$$arregloImprimirGrupos[$grupo][11] .'" class="panel-collapse collapse"><div class="panel-body"><table class="table table-hover" id="grouptable">';
                $stringRetornar .= '<thead><tr><th></th><th></th><th></th></tr></thead>';
                $stringRetornar .= '<tbody id='. $grupo .'_'. $arregloImprimirGrupos[$grupo][12] .'>';
                $stringRetornar .= '<tr><td>'. $arregloImprimirGrupos[$grupo][1] .'</td>';
                $stringRetornar .= '<td>LUGAR: '. $arregloImprimirGrupos[$grupo][4] .'</td>';
                $stringRetornar .= '<td>HORA: '. $arregloImprimirGrupos[$grupo][2] .'-'. $arregloImprimirGrupos[$grupo][3] .'</td></tr>';

                $stringRetornar .= '<tr><td colspan="3"><b>ESTUDIANTES:</b><br> '. $arregloImprimirGrupos[$grupo][17] .'</td></tr>';

                $stringRetornar .= '<tr><td colspan="3"><b>TEMA:</b><br> '. $arregloImprimirGrupos[$grupo][5] .'</td></tr>';

                $stringRetornar .= '<tr><td colspan="3"><b>ACTIVIDADES GRUPALES:</b><br> '. $arregloImprimirGrupos[$grupo][6] .'</td></tr>';

                $stringRetornar .= '<tr><td colspan="3"><b>OBSERVACIONES:</b><br>'. $arregloImprimirGrupos[$grupo][7] .'</td></tr>';

                $stringRetornar .= '<tr><td colspan="3"><b>CREADO POR:</b><br>'. $arregloImprimirGrupos[$grupo][13] .'</td></tr>';


                if ($rol  == 3 or $rol  == 7 or ($name == "administrador" or $name == "sistemas1008" or $name == "Administrador")) {
                    $stringRetornar .= '<tr><td colspan="3"><b>REPORTAR OBSERVACIÓN</b><br><textarea id="grupal_'.$codigoEnviarN1 .'_'.$codigoEnviarN2 .'_'. $arregloImprimirGrupos[$grupo][1] .'_'. $arregloImprimirGrupos[$grupo][14] .'" rows="4" cols="150"></textarea><br><br><span class="btn btn-info btn-lg botonCorreo" value="'. $arregloImprimirPares[$student][$tupla][23] .'" type="button">Enviar observaciones</span><td></tr>';
                }

                //en caso que tenga el rol correspondiente se añade un campo y un boton para
                //enviar un mensaje con observaciones tanto al monitor que hizo el seguimiento como al profesional que lo envia
                $stringRetornar .= '</tbody></table></div></div></div></div>';
            }
            $stringRetornar .= '</div></div></div></div>';

        }

        $globalArregloPares = [];
        $globalArregloGrupal = [];

        return $stringRetornar;
    }
   

    
                
 ?>