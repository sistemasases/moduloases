<?php
/*
 * Consultas modulo reportes academicos.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/grade_categories/grader_lib.php';

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
	FROM (SELECT user_m.id,SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname,user_m.username
		 FROM  {user} user_m
		 INNER JOIN {user_info_data} data ON data.userid = user_m.id
		 INNER JOIN {user_info_field} field ON data.fieldid = field.id
		 INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
		 INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
		 INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
		 WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'
	
		INTERSECT
	
		SELECT user_m.id, SUBSTRING(user_m.username FROM 1 FOR 7) as codigo, user_m.firstname, user_m.lastname,user_m.username
		FROM {user} user_m INNER JOIN {cohort_members} memb ON user_m.id = memb.userid INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
		WHERE cohorte.idnumber LIKE '$cohort%') estudiantes INNER JOIN {grade_grades} grades ON estudiantes.id = grades.userid
		INNER JOIN {grade_items} items ON grades.itemid = items.id 
		INNER JOIN {course} curso ON curso.id = items.courseid
	WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND
		  grades.finalgrade < 3 
	GROUP BY estudiantes.id, estudiantes.codigo, estudiantes.firstname, estudiantes.lastname, estudiantes.username";

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
							<td class='lastC' id = '$student->username' >$student->cantidad</td>
						</tr>";
	}

	$string_html.= "</table>";
	return $string_html;
}



/**
 * Función que retornta un string con una lista de las notas que tiene perdidas un estudiante.
 * 
 * @see get_loses_by_student($instance)
 * @param $username username instancia
 * @return String --> String Html con la tabla de estudiantes 
 */
function get_loses_by_student($username){
	global $DB;

	$query = "SELECT items.id, curso.fullname, items.itemname, grades.finalgrade
			  FROM {user} user_m INNER JOIN {grade_grades} grades ON user_m.id = grades.userid
		INNER JOIN {grade_items} items ON grades.itemid = items.id 
		INNER JOIN {course} curso ON curso.id = items.courseid
			  WHERE user_m.username = '$username' AND  grades.finalgrade < 3";

	$result = $DB->get_records_sql($query);
	$text = "";

	foreach($result as $nota){
		if(!$nota->itemname){
			$query_name = "SELECT cat.fullname as name
						   FROM {grade_items} item INNER JOIN {grade_categories} cat ON item.iteminstance = cat.id
						   WHERE item.id = $nota->id";
			$nota->itemname = $DB->get_record_sql($query_name)->name;
		}
		$note = round($nota->finalgrade,2);
		$text.="$nota->fullname $nota->itemname: $note\n";
	}
	return $text;
}

/**
 * Función que retorna un arreglo de todos los cursos donde hay matriculados estudiantes de una instancia determinada
 * @see get_courses_reports()
 * @return Array 
 */

function get_courses_for_report($user_id){
    global $DB;
    
    $query_semestre = "SELECT id, nombre FROM {talentospilos_semestre} WHERE id = (SELECT MAX(id) FROM {talentospilos_semestre})";
    $sem = $DB->get_record_sql($query_semestre);
    $id_semestre = $sem->id;
    $año = substr($sem->nombre,0,4);

    if(substr($sem->nombre,4,1) == 'A'){
        $semestre = $año.'02';
    }else if(substr($sem->nombre,4,1) == 'B'){
        $semestre = $año.'08';
    }
	//print_r($semestre);

	$intersect = "";
	
	$user_role = get_role_ases($user_id);

	if($user_role == "monitor_ps"){

		$intersect = " INTERSECT 
		                SELECT user_m.id
		                FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        WHERE mon_es.id_semestre = $id_semestre AND mon_es.id_monitor = $user_id AND field.shortname = 'idtalentos'
                        ";
    }
    elseif($user_role == "practicante_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario 
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol.id_jefe = $user_id AND field.shortname = 'idtalentos'
                        ";
    }
    elseif($user_role == "profesional_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario
                        INNER JOIN {talentospilos_user_rol} us_rol_prof ON us_rol.id_jefe = us_rol_prof.id_usuario
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol_prof.id_semestre = $id_semestre AND us_rol_prof.id_jefe = $user_id AND field.shortname = 'idtalentos'
                        ";
    }	

    $query_courses = "
	SELECT DISTINCT curso.id, curso.fullname, curso.shortname          
		FROM {course} curso
		INNER JOIN {enrol} ROLE ON curso.id = role.courseid
		INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
		WHERE SUBSTRING(curso.shortname FROM 15 FOR 6) = '$semestre' AND enrols.userid IN
			(SELECT user_m.id
				FROM  {user} user_m
				INNER JOIN {user_info_data} data ON data.userid = user_m.id
				INNER JOIN {user_info_field} field ON data.fieldid = field.id
				INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
				INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante
				INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
				WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'
				$intersect			
				)";
    $result = $DB->get_records_sql($query_courses);
        
    return $result;
}


function get_courses_report($user_id){
	$courses = get_courses_for_report($user_id);

	$string_html = "<table id = 'courses'>
						<thead>
							<tr>
								<th> Nombre </th>
								<th> Nombre Completo</th>
							</tr>
						</thead>"; 

	foreach ($courses as $course) {
		$string_html.= "<tr class='curso_reporte' id='$course->id'>
						<td>$course->fullname</td>
						<td>$course->shortname</td>
		        		</tr>";
	}

    $string_html.= "</table>";
	return $string_html;

}

/** 
 * Function that returns a course with all its information given the course id and the id of the logged user
 * @param $course_id
 * @param $user_id
 * @return Object $curso
 */

function get_info_course_for_reports($course_id, $user_id){
    global $DB;

	$query_semester = "SELECT MAX(id) FROM {talentospilos_semestre}";
	$id_semestre = $DB->get_record_sql($query_semester)->max;


    $intersect = "";

    $user_role = get_role_ases($user_id);

    if($user_role == "monitor_ps"){

		$intersect = " INTERSECT 
		                SELECT user_m.id
		                FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        WHERE mon_es.id_semestre = $id_semestre AND mon_es.id_monitor = $user_id AND field.shortname = 'idtalentos'
                        ";
    }
    elseif($user_role == "practicante_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario 
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol.id_jefe = $user_id AND field.shortname = 'idtalentos'
                        ";
    }
    elseif($user_role == "profesional_ps"){
        
        $intersect = " INTERSECT 
                        SELECT DISTINCT user_m.id
                        FROM {user} user_m
                        INNER JOIN {user_info_data} data ON data.userid = user_m.id
                        INNER JOIN {user_info_field} field ON data.fieldid = field.id
                        INNER JOIN {talentospilos_monitor_estud} mon_es ON data.data = CAST(mon_es.id_estudiante AS VARCHAR)
                        INNER JOIN {talentospilos_user_rol} us_rol ON mon_es.id_monitor = us_rol.id_usuario
                        INNER JOIN {talentospilos_user_rol} us_rol_prof ON us_rol.id_jefe = us_rol_prof.id_usuario
                        WHERE mon_es.id_semestre = $id_semestre AND us_rol.id_semestre = $id_semestre AND us_rol_prof.id_semestre = $id_semestre AND us_rol_prof.id_jefe = $user_id AND field.shortname = 'idtalentos'
                        ";
    }

    $course = $DB->get_record_sql("SELECT fullname FROM {course} WHERE id = $course_id");
    
    $query_teacher="SELECT concat_ws(' ',firstname,lastname) AS fullname
           FROM
             (SELECT usuario.firstname,
                     usuario.lastname,
                     userenrol.timecreated
              FROM {course} cursoP
              INNER JOIN {context} cont ON cont.instanceid = cursoP.id
              INNER JOIN {role_assignments} rol ON cont.id = rol.contextid
              INNER JOIN {user} usuario ON rol.userid = usuario.id
              INNER JOIN {enrol} enrole ON cursoP.id = enrole.courseid
              INNER JOIN {user_enrolments} userenrol ON (enrole.id = userenrol.enrolid
                                                           AND usuario.id = userenrol.userid)
              WHERE cont.contextlevel = 50
                AND rol.roleid = 3
                AND cursoP.id = $course_id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $professor = $DB->get_record_sql($query_teacher);
    
    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
                    FROM {user} usuario INNER JOIN {user_enrolments} enrols ON usuario.id = enrols.userid 
                    INNER JOIN {enrol} enr ON enr.id = enrols.enrolid 
                    INNER JOIN {course} curso ON enr.courseid = curso.id  
                    WHERE curso.id= $course_id AND usuario.id IN (SELECT user_m.id
                                                                 FROM  {user} user_m
                                                                 INNER JOIN {user_info_data} data ON data.userid = user_m.id
                                                                 INNER JOIN {user_info_field} field ON data.fieldid = field.id
                                                                 INNER JOIN {talentospilos_usuario} user_t ON data.data = CAST(user_t.id AS VARCHAR)
                                                                 INNER JOIN {talentospilos_est_estadoases} estado_u ON user_t.id = estado_u.id_estudiante 
                                                                 INNER JOIN {talentospilos_estados_ases} estados ON estados.id = estado_u.id_estado_ases
                                                                 WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'
                                                                 $intersect
                                                                 )";

    $students = $DB->get_records_sql($query_students);

    $header_categories = get_categories_global_grade_book($course_id);


    $curso = new stdClass;
    $curso->nombre_curso = $course->fullname;
    $curso->profesor = $professor->fullname;
    $curso->estudiantes = $students;
    $curso->header_categories = $header_categories;
    
    return $curso;
}

//print_r(get_info_course_for_reports(10, 324)->estudiantes);
