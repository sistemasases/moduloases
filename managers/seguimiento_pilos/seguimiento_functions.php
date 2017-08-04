<?php
/*
 * Funciones del módulo seguimientos pilos que se utilizarán en la view. 
 */

/*
 * Función que transforma el arreglo retornado por la peticion en un arreglo que posteriormente se utilizará para crear el toogle.
 */


function transformarConsultaMonitorArray($array,&$pares,&$grupal,$codigoMonitor, $noMonitor, $instanceid,$codigoPracticante=null){

	foreach($array as $seguimiento){
		if ($seguimiento->tipo == "PARES") {

		$array_auxiliar = [];
        $fecha = gmdate('M/d/Y', $seguimiento->fecha);
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
         	array_push($array_auxiliar,$seguimiento->fecha); // 18
         	array_push($array_auxiliar,$seguimiento->id_estudiante); // 19 idtalentos
         	array_push($array_auxiliar,$nombremon_enviar); // 20
         	array_push($array_auxiliar,$seguimiento->objetivos); // 21
         	array_push($array_auxiliar,$seguimiento->id_seguimiento); // 22
         	array_push($array_auxiliar,$seguimiento->registros_estudiantes_revisados); // 23
         	array_push($array_auxiliar,$seguimiento->registros_estudiantes_norevisados); // 24
         	array_push($array_auxiliar,$seguimiento->registros_estudiantes_total); // 25
         	array_push($array_auxiliar,$seguimiento->profesional); // 26
         	array_push($array_auxiliar,$seguimiento->practicante); // 27

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
        //metodo que agrupa informacion de los seguimientos de pares por el codigo
        $arregloImprimirPares = agrupar_informacion($pares, 20);

        //metodo que agrupa informacion de los seguimientos grupales por los codigos
        $arregloImprimirGrupos = agrupar_informacion($grupal, 12);

        //metodo que deja un solo registro grupal con el mismo codigo y concatena nombres y codigos de los estudiantes
        $arregloImprimirGrupos = agrupar_Seguimientos_grupales($arregloImprimirGrupos);

        //se ordena los seguimientos de cada estudiante segun la fecha
        for ($grupo=0;$grupo<count($arregloImprimirPares);$grupo++) {
            ordenaPorColumna($arregloImprimirPares[$grupo], 18);//correct
        }

         //se retorna la informacion del toogle creado desde el punto del monitor
       // return crearTablaYToggle($arregloImprimirPares, $noMonitor, $arregloImprimirGrupos, $codigoMonitor, $codigoPracticante);
}





    //Funcion que ordena un arreglo segun la columna definida de menos valor a mayor
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

               // print_r($arreglo);

    }


 /*Funcion para agrupar los seguimientos grupales segun el ID
 */
    function agrupar_Seguimientos_grupales($arreglo) {
        $NuevoArregloGrupal = [];
        for ($elementoRevisar=0;$elementoRevisar<count($arreglo);$elementoRevisar++){
             $arregloAuxiliar = $arreglo[$elementoRevisar][0];
             $nombres="";
             $nombresImpirmir="";
             $codigos="";
             $contador=1;

             //funcion que captura tanto los nombres como los codigos y crea un texto
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
                 $arregloEstudiante = [];
                 //se agrega al arreglo
                 $tamano = count($nuevoArreglo);
                 array_push($arregloEstudiante,$infoMonitor[$i]);
                 $nuevoArreglo[$tamano] = $arregloEstudiante;
             }else {
                 $arregloEstudiante = [];
                 $arregloEstudiante = $nuevoArreglo[$posicion];
                 array_push($arregloEstudiante,$infoMonitor[$i]);
             //si no es prque ya tiene registro asi que se agrega registro al estudiante
                 $nuevoArreglo[$posicion] = [];
                 $nuevoArreglo[$posicion] = $arregloEstudiante;
             }
        }
        return $nuevoArreglo;
         }




    
				
?>