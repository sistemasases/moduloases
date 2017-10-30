<?php
/*
 * Consultas modulo reportes academicos.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 

/**
 * Función que consulta todos los estudiantes ASES que tienen itemas de calificacion perdidos 
 * 
 * @see studentsWithLoses($instance)
 * @param $instance id instancia
 * @return Array --> Array con la informacion de los estudiantes. 
 */
function studentsWithLoses($instance){
	global $DB;

	$query_semestre = "SELECT nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $sem = $DB->get_record_sql($query_semestre)->nombre;

    $año = substr($sem,0,4);

    if(substr($sem,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem,4,1) == 'B'){
        $semestre = $año.'08';
	}
	
	$query_prog = "
		SELECT pgr.cod_univalle as cod
		FROM {talentospilos_instancia} inst
		INNER JOIN {talentospilos_programa} pgr ON inst.id_programa = pgr.id
		WHERE inst.id_instancia= $instance";

	$prog = $DB->get_record_sql($query_prog)->cod;    

	//Si el código del programa es 1008 la cohorte comenzará por SP y si no, empezará por el código del programa
	if($prog === '1008'){
		$cohort = 'SP';
	}else{
		$cohort = $prog;
	}

	$query = "SELECT estudiantes.*, COUNT(grades.id) as cantidad
	FROM (SELECT user_m.id,SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
		 FROM  mdl_user user_m
		 INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
		 INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
		 INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
		 INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante
		 INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
		 WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'
	
		INTERSECT
	
		SELECT user_m.id, SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname
		FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id
		WHERE cohorte.idnumber LIKE '$cohort%') estudiantes INNER JOIN mdl_grade_grades grades ON estudiantes.id = grades.userid
		INNER JOIN mdl_grade_items items ON grades.itemid = items.id 
		INNER JOIN mdl_course curso ON curso.id = items.courseid
	WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND
		  grades.finalgrade < 3 
	GROUP BY estudiantes.id, estudiantes.codigo, estudiantes.firstname, estudiantes.lastname";

	$result = $DB->get_records_sql($query);
	return $result;
}	

/**
 * Función que procesa la consulta de estudiantes ASES con el numero de itemas de calificacion perdidos y retorna una tabla html
 * 
 * @see getReportStudents($instance)
 * @param $instance id instancia
 * @return String --> String Html con la tabla de estudiantes 
 */
function getReportStudents($instance){

	$students = studentsWithLoses($instance);
	$string_html = "<table id = 'students'>
				<thead>
					<tr>
                        <th> Código</th>
						<th> Nombre </th>
						<th> Apellidos </th>
						<th> Número de items perdidos </th>
                    </tr>
				</thead>";

	foreach ($students as $student) {
		$string_html.= "<tr>
							<td>$student->codigo</td>
							<td>$student->firstname</td>
							<td>$student->lastname</td>
							<td>$student->cantidad</td>
						</tr>";
	}

	$string_html.= "</table>";
	return $string_html;
}

?>
