<<<<<<< HEAD
<?php
/*
 * Consultas modulo registro de notas.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->libdir.'/gradelib.php';
//require_once('../../../../querylib.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 
// require_once $CFG->dirroot.'/grade/report/grader/lib.php';
// require_once $CFG->dirroot.'/grade/lib.php';

///*********************************///
///*** Get info calificador methods ***///
///*********************************///

/*
 * Función que retorna un arreglo de todos los cursos donde hay matriculados estudiantes de una instancia determinada organizados segun su profesor.
 * @param $instanceid
 * @return Array 
 */

function get_courses_pilos($instanceid){
    global $DB;
    
    //Se consulta el programa al cual esta asociada la instancia
    $query_prog = "
        SELECT pgr.cod_univalle as cod
        FROM {talentospilos_instancia} inst
        INNER JOIN {talentospilos_programa} pgr ON inst.id_programa = pgr.id
        WHERE inst.id_instancia= $instanceid";
    
    $prog = $DB->get_record_sql($query_prog)->cod;    
   
    //Si el código del programa es 1008 la cohorte comenzará por SP y si no, empezará por el código del programa
    if($prog === '1008'){
        $cohort = 'SP';
    }else{
        $cohort = $prog;
    }
    
    $query_courses = "
        SELECT DISTINCT curso.id,
                        curso.fullname,
                        curso.shortname,
        
          (SELECT concat_ws(' ',firstname,lastname) AS fullname
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
                AND cursoP.id = curso.id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc) AS nombre
        FROM {course} curso
        INNER JOIN {enrol} ROLE ON curso.id = role.courseid
        INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
        WHERE enrols.userid IN
            (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante 
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id
    FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
    WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = '$cohort')";
    $result = $DB->get_records_sql($query_courses);
    
    $result = processInfo($result);
    return $result;
}
// print_r(get_courses_pilos(19));

/*
 * Función que retorna un arreglo de profesores, dado un objeto consulta
 * @param $info
 * @return Array con el siguiente formato: array("$nomProfesor" => array(array("id" => $id_curso, "nombre"=>$nom_curso,"shortname"=>$shortname_curso), array(...)))
 */
function processInfo($info){
    $profesores = [];
    
    foreach ($info as $course) {
        $profesor = $course->nombre;
        $id = $course->id;
        $nombre = $course->fullname;
        $shortname = $course->shortname;
        $curso=["id"=>$id,"nombre"=>$nombre,"shortname"=>$shortname];
        if(!isset($profesores[$profesor])){
            $profesores[$profesor] = [];
        }
        
        array_push($profesores[$profesor],$curso) ;
    }
    return $profesores;
}


/*
 * Función que retorna informacion de un curso por su id
 * @param $id_curso
 * @return Object $curso
 */
function get_info_course($id_curso){
    global $DB;
    $course = $DB->get_record_sql("SELECT fullname FROM {course} WHERE id = $id_curso");
    
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
                AND cursoP.id = $id_curso
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher);
    
    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
                    FROM mdl_user usuario INNER JOIN mdl_user_enrolments enrols ON usuario.id = enrols.userid 
                    INNER JOIN mdl_enrol enr ON enr.id = enrols.enrolid 
                    INNER JOIN mdl_course curso ON enr.courseid = curso.id  
                    WHERE curso.id= $id_curso AND usuario.id IN (SELECT user_m.id
                                                                 FROM  mdl_user user_m
                                                                 INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
                                                                 INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
                                                                 INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
                                                                 INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante 
                                                                 INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
                                                                 WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

                                                                INTERSECT

                                                                SELECT user_m.id
                                                                FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
                                                                WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP')";

    $estudiantes = $DB->get_records_sql($query_students);

    $header_categories = get_categorias_calificador($id_curso);


    $curso = new stdClass;
    $curso->nombre_curso = $course->fullname;
    $curso->profesor = $profesor->fullname;
    $curso->estudiantes = $estudiantes;
    $curso->header_categories = $header_categories;
    
    return $curso;
}


/**
 * Returns de string html table with the students, categories and his notes.
 *
 * @param $id_curso
 * @return string HTML
**/
function get_categorias_calificador($id_curso){
    global $USER;
    $USER->gradeediting[$id_curso] = 1;

    $context = context_course::instance($id_curso);
    
    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$id_curso));
    $report = new grade_report_grader($id_curso, $gpr, $context);
    // $tabla = $report->get_grade_table();
    // echo htmlspecialchars($tabla);
    $report->load_users();
    $report->load_final_grades();
    return $report->get_grade_table();
}
// print_r(get_categorias_curso(3));

///*********************************///
///***    Update grades method   ***///
///*********************************///

/**
 * Update grades from a student
 *
 * @param   $userid
 *          $item
 *          $finalgrade: value of grade
 *          $courseid
 *       
 * @return true if update and false if not.
 */

function update_grades_moodle($userid, $itemid, $finalgrade,$courseid){
  if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$courseid))) { // we must verify course id here!
      return false;
  }
  
  if ($grade_item->update_final_grade($userid, $finalgrade, 'gradebook', false, FORMAT_MOODLE)) {
    if($finalgrade < 3){
      return send_email_alert($userid, $itemid,$finalgrade,$courseid);
    }else{
      $resp = new stdClass;
      $resp->nota = true;
      return $resp;
    }
  } else {

    $resp = new stdClass;
    $resp->nota = false;

    return $resp;
  }

}

function send_email_alert($userid, $itemid,$grade,$courseid){
      global $USER;
      global $DB;

      $resp = new stdClass;
      $resp->nota = true;
      
      $sending_user = $DB->get_record_sql("SELECT * FROM {user} WHERE username = 'sistemas1008'");
      
      $userFromEmail = new stdClass;

      $userFromEmail->email = $sending_user->email;
      $userFromEmail->firstname = $sending_user->firstname;
      $userFromEmail->lastname = $sending_user->lastname;
      $userFromEmail->maildisplay = true;
      $userFromEmail->mailformat = 1;
      $userFromEmail->id = $sending_user->id; 
      $userFromEmail->alternatename = '';
      $userFromEmail->middlename = '';
      $userFromEmail->firstnamephonetic = '';
      $userFromEmail->lastnamephonetic = '';

      $user_moodle = get_full_user($userid);
      $nombre_estudiante = $user_moodle->firstname." ".$user_moodle->lastname;

      $subject = "ALERTA ACADÉMICA $nombre_estudiante";

      $curso = $DB->get_record_sql("SELECT fullname, shortname FROM {course} WHERE id = $courseid");
      $nombre_curso= $curso->fullname." ".$curso->shortname;
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
                AND cursoP.id = $courseid
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
      $profesor = $DB->get_record_sql($query_teacher)->fullname;
      $item = $DB->get_record_sql("SELECT itemname FROM {grade_items} WHERE id = $itemid");
      $itemname = $item->itemname;
      $nota = number_format($grade,2);
      $nom_may = strtoupper($nombre_curso);
      $titulo = "<b>ALERTA ACADÉMICA CURSO $nom_may <br> PROFESOR: $profesor</b><br> ";
      $mensaje = "Se le informa que se ha presentado una alerta académica del estudiante $nombre_estudiante en el curso $nombre_curso<br> 
        El estudiante ha obtenido la siguiente calificación:<br> <br> <b>$itemname: <b> $nota <br><br> 
        Cordialmente<br>
        <b>Oficina TIC<br>
        Estrategia ASES<br>
        Universidad del Valle</b>";

      $user_ases = get_adds_fields_mi($userid);
      $id_tal = $user_ases->idtalentos;

      $monitor = get_assigned_monitor($id_tal);
      $nombre_monitor = $monitor->firstname." ".$monitor->lastname;
      $saludo_mon = "Estimado monitor $nombre_monitor<br><br>";

      $monitorToEmail = new stdClass;
      $monitorToEmail->email = $monitor->email;
      $monitorToEmail->firstname = $monitor->firstname;
      $monitorToEmail->lastname = $monitor->lastname;
      $monitorToEmail->maildisplay = true;
      $monitorToEmail->mailformat = 1;
      $monitorToEmail->id = $monitor->id; 
      $monitorToEmail->alternatename = '';
      $monitorToEmail->middlename = '';
      $monitorToEmail->firstnamephonetic = '';
      $monitorToEmail->lastnamephonetic = '';

      $messageHtml_mon = $titulo.$saludo_mon.$mensaje ;   
      $messageText_mon = html_to_text($messageHtml_mon);

      $email_result = email_to_user($monitorToEmail, $userFromEmail, $subject, $messageText_mon, $messageHtml_mon, ", ", true);

      if($email_result!=1){ 
        $resp->monitor = false;
      }else{
        $resp->monitor = true;

        $practicante = get_assigned_pract($id_tal);
        $nombre_practicante = $practicante->firstname." ".$practicante->lastname;
        $saludo_prac = "Estimado practicante $nombre_practicante<br><br>";

        $practicanteToEmail = new stdClass;
        $practicanteToEmail->email = $practicante->email;
        $practicanteToEmail->firstname = $practicante->firstname;
        $practicanteToEmail->lastname = $practicante->lastname;
        $practicanteToEmail->maildisplay = true;
        $practicanteToEmail->mailformat = 1;
        $practicanteToEmail->id = $practicante->id; 
        $practicanteToEmail->alternatename = '';
        $practicanteToEmail->middlename = '';
        $practicanteToEmail->firstnamephonetic = '';
        $practicanteToEmail->lastnamephonetic = '';

        $messageHtml_prac = $titulo.$saludo_prac.$mensaje ;   
        $messageText_prac = html_to_text($messageHtml_prac);

        $email_result_prac = email_to_user($practicanteToEmail, $userFromEmail, $subject, $messageText_prac, $messageHtml_prac, ", ", true);

        if($email_result_prac!=1){
          $resp->practicante = false;
        }else{
          $resp->practicante = true;

          $profesional = get_assigned_professional($id_tal);
          $nombre_profesional = $profesional->firstname." ".$profesional->lastname;
          $saludo_prof = "Estimado profesional $nombre_profesional<br><br>";

          $profesionalToEmail = new stdClass;
          $profesionalToEmail->email = $profesional->email;
          $profesionalToEmail->firstname = $profesional->firstname;
          $profesionalToEmail->lastname = $profesional->lastname;
          $profesionalToEmail->maildisplay = true;
          $profesionalToEmail->mailformat = 1;
          $profesionalToEmail->id = $profesional->id; 
          $profesionalToEmail->alternatename = '';
          $profesionalToEmail->middlename = '';
          $profesionalToEmail->firstnamephonetic = '';
          $profesionalToEmail->lastnamephonetic = '';

          $messageHtml_prof = $titulo.$saludo_prof.$mensaje ;   
          $messageText_prof = html_to_text($messageHtml_prof);

          $email_result_prof = email_to_user($profesionalToEmail, $userFromEmail, $subject, $messageText_prof, $messageHtml_prof, ", ", true);

          if($email_result_prof!=1){
            $resp->profesional = false;
          }else{
            $resp->profesional = true;
          }

        }
      }
      
      return $resp;
  
}



//update_grades_moodle(92,49,5,3);
///*********************************///
///*** Wizard categories methods ***///
///*********************************///
/**
 * It performs the insertion of a category considering whether it is of weighted type or not,
 * after which it inserts the item that represents the category. The latter is necessary for the category to have a weight.
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return Int --- ok->1 || error->0
**/
function insertCategory($course,$father,$name,$weighted,$weight){
    global $DB;
    
    //Instance an object category to use insert_record
    $object = new stdClass;
    $object ->courseid=$course;
    $object ->fullname=$name;
    $object ->parent =$father;
    $object ->aggregation=$weighted;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    $succes=$DB->insert_record('grade_categories',$object);
    
    if($succes)
    {
      if(insertItem($course,$succes,$name,$weight,false)===1)
      {
        return 1;    
      }else{
          return 0;
      }
    }
    return 0;
}

/**
 * It performs the insertion of a category parcial and if it works, it returns the id  the created category
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return int --- ok->id_cat || error->0
**/
function insertCategoryParcial($course,$father,$name,$weighted,$weight){
    global $DB;
      
    //Instance an object category to use insert_record
    $object = new stdClass;
    $object ->courseid=$course;
    $object ->fullname=$name;
    $object ->parent =$father;
    $object ->aggregation=$weighted;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    $succes=$DB->insert_record('grade_categories',$object);
      
    if($succes){
        if(insertItem($course,$succes,$name,$weight,false)===1)
        {
          return $succes;    
        }else{
            return 0;
        }
    }

    return 0;
}

/**
 * It performs the insertion of parcial
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return Int --- ok-> 1 || error-> 0
**/
function insertParcial($course,$father,$name,$weighted,$weight){
    $succes = insertCategoryParcial($course,$father,$name,$weighted,$weight);
    if($succes != 0){
        if(insertItem($course,$succes,$name,0,true) === 1){
            if(insertItem($course,$succes,"Opcional de ".$name,0,true)===1){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}


/**
 *Performs the insertion of item, either flat item or an item related to a category,
 *the latter is necessary to be able to assign a weight in case the category is a 
 * daughter of another category with weighted rating
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $valsend
 * @param $item
 * @return Int --- ok-> 1 || error-> 0
**/
function insertItem($course,$father,$name,$valsend,$item){
    global $DB;
    
    //Instance an object item to use insert_record
    if($item)
    {
    $object = new stdClass;
    $object ->courseid=$course;
    $object -> categoryid=$father;
    $object ->itemname=$name;
    $object -> itemnumber=0;
    $object -> itemtype='manual';
    $object -> sortorder=getNextIndex($course);
    $object -> aggregationcoef=$valsend;
    $object -> grademax=5;
    }else{
    $object = new stdClass;
    $object ->courseid=$course;
    $object -> itemtype='category';
    $object -> sortorder=getNextIndex($course);
    $object -> aggregationcoef=$valsend;
    $object -> iteminstance=$father;
    $object -> grademax=5;
    }
    
    $succes=$DB->insert_record('grade_items',$object);
    
    if($succes)
    {
        return 1;
    }else
    {
        return 0;
    }
    
}

/**
 *Make a query to find the last index of the sort element corresponding to the category that is being entered
 *
 * @param $course
 * @return int --- nextindex
**/
function getNextIndex($course){
    global $DB;
    $sql_query = "SELECT max(sortorder) FROM {grade_items} WHERE courseid=".$course.";";
    $output=$DB->get_record_sql($sql_query);
    $nextindex=($output->max)+1;
    return $nextindex;
}


/**
 * Make a html_string with the categories tree of a course identified by $courseid
 *
 * @param $idCourse
 * @return String hmtl
**/
function getCategoriesandItems($courseid){

    global $DB;
   
    $sql_query="SELECT {user_enrolments}.userid AS id
                FROM {enrol} INNER JOIN {user_enrolments} ON ({user_enrolments}.enrolid ={enrol}.id) 
                WHERE courseid=".$courseid.";";
                
    $userid = $DB->get_record_sql($sql_query)->id;
    $context = context_course::instance($courseid);
     //print_r($userid);

    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table_categories($report);
    if ($report->fill_table()) {
        //print_r($report->tabledata);
        return print_table_categories($report);
    }
}
//getCategoriesandItems(3);

/**
 * Returns the HTML from the flexitable.
 * @param grade_report_user $report info of which will be shown
 * @return string
**/ 
function print_table_categories($report){
    $maxspan = $report->maxdepth;

          /// Build table structure
          $html = "
              <table cellspacing='0'
                     cellpadding='0'
                     summary='" . s($report->get_lang_string('tablesummary', 'gradereport_user')) . "'
                     class='boxaligncenter generaltable user-grade'>
              <thead>
                  <tr>
                      <th id='".$report->tablecolumns[0]."' class=\"header column-{$report->tablecolumns[0]}\" colspan='$maxspan'>".$report->tableheaders[0]."</th>\n";

          for ($i = 1; $i < count($report->tableheaders); $i++) {
              $html .= "<th id='".$report->tablecolumns[$i]."' class=\"header column-{$report->tablecolumns[$i]}\">".$report->tableheaders[$i]."</th>\n";
          }
  
          $html .= "
                  </tr>
              </thead>
              <tbody>\n";
  
          /// Print out the table data
          for ($i = 0; $i < count($report->tabledata); $i++) {
              $html .= "<tr>\n";
              for ($j = 0; $j < count($report->tablecolumns); $j++) {
                  $name = $report->tablecolumns[$j];
                  $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
                  $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '';
                  $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
                  $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
                  $id = (isset($report->tabledata[$i][$name]['id'])) ? "id='{$report->tabledata[$i][$name]['id']}'" : '';
                  $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
                  if (isset($content)&&!isCategoryTotal($content)) {
                      if(isCategory($content)){
                        $categoryid = explode("_",$id)[1];
                        $weight = getweightofCategory($categoryid);
                        if(!$weight || intval($weight) === 0){
                            $weight = '-';
                        }else{
                              $weight = '('. floatval($weight).' %)';
                        }  
                        $aggregation = getAggregationofCategory($categoryid);
                      $maxweight = getMaxWeight($categoryid);
                        $html .= "<$celltype $id $headers class='$class' $colspan><div id = '$aggregation' class = 'agg'> $content <p style = 'display: inline' class = 'maxweight' id = '$maxweight'>$weight</p> <button title = \" Crear nuevo item o categoria\" class = \" btn new\" style = \"float: right !important\">+</button> </div></$celltype>\n";
                      }else{
                        $id_item = explode("_",$id)[1];  
                        $weight = getweightofItem($id_item);
                        if(!$weight || intval($weight) === 0){
                            $weight = '-';
                        }else{
                            $weight = '('. floatval($weight).' %)';
                        }  
                        $html .= "<$celltype $id $headers class='$class' $colspan>$content <p style = 'display: inline'>$weight</p> </$celltype>\n";
                      }
                  }
              }
              $html .= "</tr>\n";
          }
  
          $html .= "</tbody></table>";
          return $html;
}


/**
 * Get the max weight that a new item can have in a category.
 *
 * @param $categoryid
 * @return int
**/
 function getMaxWeight($categoryid) {
  global $DB;
  $maxweight = 100;

  $query = "SELECT sum(peso) as total
            FROM
              (SELECT id,
                      SUM(aggregationcoef) AS peso
               FROM mdl_grade_items
               WHERE categoryid = $categoryid
               GROUP BY id
               UNION SELECT item.id,
                            SUM(item.aggregationcoef) AS peso
               FROM mdl_grade_items item
               INNER JOIN mdl_grade_categories cat ON item.iteminstance=cat.id
               WHERE cat.parent = $categoryid
               GROUP BY item.id)AS pesos";
  $result = $DB->get_record_sql($query);

  if($result){
    $weight = $result->total;
  }else{
    $weight = 0;
  }

  $maxweight = $maxweight - $weight;

  return $maxweight;
}


/**
 * Reduce grade information to display in categories tree
 *
 * @param &$report
 * @return null
**/
function reduce_table_categories(&$report){
    $report->showpercentage = false;
    $report->showrange = false; 
    $report->showfeedback = false;
    $report->showcontributiontocoursetotal = false;
    $report->showweight = false;
    $report->showgrade = false; 
    $report->showtotalsifcontainhidden = false;
    $report->setup_table();
}

/**
 * Say if find the string "gradeitemdescriptionfiller" in a parameter $string to determinate if is a total item
 *
 * @param $string
 * @return boolean
**/
function isCategoryTotal($string){
    if(stripos($string, "gradeitemdescriptionfiller") === false && stripos($string, "Total") == false){
        return false;
    }else{
        return true;
    }
    
}

/**
 * Say if find the string "Categoria" or "Category" in a parameter $string
 *
 * @param $string
 * @return boolean
**/

function isCategory($string){
    if((stripos($string, "Categoría") === false)&&(stripos($string, "Category") === false)){
        return false;
    }else{
        return true;
    }
    
}


/**
 * Get the weight of an item.
 *
 * @param $itemid
 * @return int weight
**/
function getweightofItem($itemid){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items}
                  WHERE id = ".$itemid;
                  
    $output = $DB->get_record_sql($sql_query);
    if($output){
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Get the weight of an category.
 *
 * @param $itemid
 * @return int weight
**/
function getweightofCategory($id){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items} item INNER JOIN {grade_categories} cat on item.iteminstance=cat.id 
                  WHERE cat.id = ".$id;
                  
    $output = $DB->get_record_sql($sql_query);
    if($output){
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Get the aggregation tipe of an category.
 *
 * @param $itemid
 * @return int weight
**/

function getAggregationofCategory($categoryid){
    global $DB;
    
    $sql_query = "
        SELECT aggregation
        FROM {grade_categories} 
        WHERE id = '$categoryid'";
    $output = $DB->get_record_sql($sql_query);

    $aggregation = $output->aggregation ;

    return $aggregation;
}

?>
=======
<?php
/*
 * Consultas modulo registro de notas.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->libdir.'/gradelib.php';
//require_once('../../../../querylib.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php';
require_once $CFG->dirroot.'/blocks/ases/managers/lib/student_lib.php'; 
// require_once $CFG->dirroot.'/grade/report/grader/lib.php';
// require_once $CFG->dirroot.'/grade/lib.php';

///*********************************///
///*** Get info calificador methods ***///
///*********************************///

/*
 * Función que retorna un arreglo de todos los cursos donde hay matriculados estudiantes de una instancia determinada organizados segun su profesor.
 * @param $instanceid
 * @return Array 
 */

function get_courses_pilos($instanceid){
    global $DB;
    
    //Se consulta el programa al cual esta asociada la instancia
    $query_prog = "
        SELECT pgr.cod_univalle as cod
        FROM {talentospilos_instancia} inst
        INNER JOIN {talentospilos_programa} pgr ON inst.id_programa = pgr.id
        WHERE inst.id_instancia= $instanceid";
    
    $prog = $DB->get_record_sql($query_prog)->cod;    
   
    //Si el código del programa es 1008 la cohorte comenzará por SP y si no, empezará por el código del programa
    if($prog === '1008'){
        $cohort = 'SP';
    }else{
        $cohort = $prog;
    }
    
    $query_courses = "
        SELECT DISTINCT curso.id,
                        curso.fullname,
                        curso.shortname,
        
          (SELECT concat_ws(' ',firstname,lastname) AS fullname
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
                AND cursoP.id = curso.id
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc) AS nombre
        FROM {course} curso
        INNER JOIN {enrol} ROLE ON curso.id = role.courseid
        INNER JOIN {user_enrolments} enrols ON enrols.enrolid = role.id
        WHERE enrols.userid IN
            (SELECT user_m.id
     FROM  mdl_user user_m
     INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
     INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
     INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
     INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante 
     INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
     WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

    INTERSECT

    SELECT user_m.id
    FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
    WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = '$cohort')";
    $result = $DB->get_records_sql($query_courses);
    
    $result = processInfo($result);
    return $result;
}
// print_r(get_courses_pilos(19));

/*
 * Función que retorna un arreglo de profesores, dado un objeto consulta
 * @param $info
 * @return Array con el siguiente formato: array("$nomProfesor" => array(array("id" => $id_curso, "nombre"=>$nom_curso,"shortname"=>$shortname_curso), array(...)))
 */
function processInfo($info){
    $profesores = [];
    
    foreach ($info as $course) {
        $profesor = $course->nombre;
        $id = $course->id;
        $nombre = $course->fullname;
        $shortname = $course->shortname;
        $curso=["id"=>$id,"nombre"=>$nombre,"shortname"=>$shortname];
        if(!isset($profesores[$profesor])){
            $profesores[$profesor] = [];
        }
        
        array_push($profesores[$profesor],$curso) ;
    }
    return $profesores;
}


/*
 * Función que retorna informacion de un curso por su id
 * @param $id_curso
 * @return Object $curso
 */
function get_info_course($id_curso){
    global $DB;
    $course = $DB->get_record_sql("SELECT fullname FROM {course} WHERE id = $id_curso");
    
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
                AND cursoP.id = $id_curso
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
    $profesor = $DB->get_record_sql($query_teacher);
    
    $query_students = "SELECT usuario.id, usuario.firstname, usuario.lastname, usuario.username
                    FROM mdl_user usuario INNER JOIN mdl_user_enrolments enrols ON usuario.id = enrols.userid 
                    INNER JOIN mdl_enrol enr ON enr.id = enrols.enrolid 
                    INNER JOIN mdl_course curso ON enr.courseid = curso.id  
                    WHERE curso.id= $id_curso AND usuario.id IN (SELECT user_m.id
                                                                 FROM  mdl_user user_m
                                                                 INNER JOIN mdl_user_info_data data ON data.userid = user_m.id
                                                                 INNER JOIN mdl_user_info_field field ON data.fieldid = field.id
                                                                 INNER JOIN mdl_talentospilos_usuario user_t ON data.data = CAST(user_t.id AS VARCHAR)
                                                                 INNER JOIN mdl_talentospilos_est_estadoases estado_u ON user_t.id = estado_u.id_estudiante 
                                                                 INNER JOIN mdl_talentospilos_estados_ases estados ON estados.id = estado_u.id_estado_ases
                                                                 WHERE estados.nombre = 'ACTIVO/SEGUIMIENTO' AND field.shortname = 'idtalentos'

                                                                INTERSECT

                                                                SELECT user_m.id
                                                                FROM mdl_user user_m INNER JOIN mdl_cohort_members memb ON user_m.id = memb.userid INNER JOIN mdl_cohort cohorte ON memb.cohortid = cohorte.id 
                                                                WHERE SUBSTRING(cohorte.idnumber FROM 1 FOR 2) = 'SP')";

    $estudiantes = $DB->get_records_sql($query_students);

    $header_categories = get_categorias_calificador($id_curso);


    $curso = new stdClass;
    $curso->nombre_curso = $course->fullname;
    $curso->profesor = $profesor->fullname;
    $curso->estudiantes = $estudiantes;
    $curso->header_categories = $header_categories;
    
    return $curso;
}


/**
 * Returns de string html table with the students, categories and his notes.
 *
 * @param $id_curso
 * @return string HTML
**/
function get_categorias_calificador($id_curso){
    global $USER;
    $USER->gradeediting[$id_curso] = 1;

    $context = context_course::instance($id_curso);
    
    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$id_curso));
    $report = new grade_report_grader($id_curso, $gpr, $context);
    // $tabla = $report->get_grade_table();
    // echo htmlspecialchars($tabla);
    $report->load_users();
    $report->load_final_grades();
    return $report->get_grade_table();
}
// print_r(get_categorias_curso(3));

///*********************************///
///***    Update grades method   ***///
///*********************************///

/**
 * Update grades from a student
 *
 * @param   $userid
 *          $item
 *          $finalgrade: value of grade
 *          $courseid
 *       
 * @return true if update and false if not.
 */

function update_grades_moodle($userid, $itemid, $finalgrade,$courseid){
  if (!$grade_item = grade_item::fetch(array('id'=>$itemid, 'courseid'=>$courseid))) { // we must verify course id here!
      return false;
  }
  
  if ($grade_item->update_final_grade($userid, $finalgrade, 'gradebook', false, FORMAT_MOODLE)) {
    if($finalgrade < 3){
      return send_email_alert($userid, $itemid,$finalgrade,$courseid);
    }else{
      $resp = new stdClass;
      $resp->nota = true;
      return $resp;
    }
  } else {

    $resp = new stdClass;
    $resp->nota = false;

    return $resp;
  }

}

function send_email_alert($userid, $itemid,$grade,$courseid){
      global $USER;
      global $DB;

      $resp = new stdClass;
      $resp->nota = true;
      
      $sending_user = $DB->get_record_sql("SELECT * FROM {user} WHERE username = 'sistemas1008'");
      
      $userFromEmail = new stdClass;

      $userFromEmail->email = $sending_user->email;
      $userFromEmail->firstname = $sending_user->firstname;
      $userFromEmail->lastname = $sending_user->lastname;
      $userFromEmail->maildisplay = true;
      $userFromEmail->mailformat = 1;
      $userFromEmail->id = $sending_user->id; 
      $userFromEmail->alternatename = '';
      $userFromEmail->middlename = '';
      $userFromEmail->firstnamephonetic = '';
      $userFromEmail->lastnamephonetic = '';

      $user_moodle = get_full_user($userid);
      $nombre_estudiante = $user_moodle->firstname." ".$user_moodle->lastname;

      $subject = "ALERTA ACADÉMICA $nombre_estudiante";

      $curso = $DB->get_record_sql("SELECT fullname, shortname FROM {course} WHERE id = $courseid");
      $nombre_curso= $curso->fullname." ".$curso->shortname;
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
                AND cursoP.id = $courseid
              ORDER BY userenrol.timecreated ASC
              LIMIT 1) AS subc";
      $profesor = $DB->get_record_sql($query_teacher)->fullname;
      $item = $DB->get_record_sql("SELECT itemname FROM {grade_items} WHERE id = $itemid");
      $itemname = $item->itemname;
      $nota = number_format($grade,2);
      $nom_may = strtoupper($nombre_curso);
      $titulo = "<b>ALERTA ACADÉMICA CURSO $nom_may <br> PROFESOR: $profesor</b><br> ";
      $mensaje = "Se le informa que se ha presentado una alerta académica del estudiante $nombre_estudiante en el curso $nombre_curso<br> 
        El estudiante ha obtenido la siguiente calificación:<br> <br> <b>$itemname: <b> $nota <br><br> 
        Cordialmente<br>
        <b>Oficina TIC<br>
        Estrategia ASES<br>
        Universidad del Valle</b>";

      $user_ases = get_adds_fields_mi($userid);
      $id_tal = $user_ases->idtalentos;

      $monitor = get_assigned_monitor($id_tal);
      $nombre_monitor = $monitor->firstname." ".$monitor->lastname;
      $saludo_mon = "Estimado monitor $nombre_monitor<br><br>";

      $monitorToEmail = new stdClass;
      $monitorToEmail->email = $monitor->email;
      $monitorToEmail->firstname = $monitor->firstname;
      $monitorToEmail->lastname = $monitor->lastname;
      $monitorToEmail->maildisplay = true;
      $monitorToEmail->mailformat = 1;
      $monitorToEmail->id = $monitor->id; 
      $monitorToEmail->alternatename = '';
      $monitorToEmail->middlename = '';
      $monitorToEmail->firstnamephonetic = '';
      $monitorToEmail->lastnamephonetic = '';

      $messageHtml_mon = $titulo.$saludo_mon.$mensaje ;   
      $messageText_mon = html_to_text($messageHtml_mon);

      $email_result = email_to_user($monitorToEmail, $userFromEmail, $subject, $messageText_mon, $messageHtml_mon, ", ", true);

      if($email_result!=1){ 
        $resp->monitor = false;
      }else{
        $resp->monitor = true;

        $practicante = get_assigned_pract($id_tal);
        $nombre_practicante = $practicante->firstname." ".$practicante->lastname;
        $saludo_prac = "Estimado practicante $nombre_practicante<br><br>";

        $practicanteToEmail = new stdClass;
        $practicanteToEmail->email = $practicante->email;
        $practicanteToEmail->firstname = $practicante->firstname;
        $practicanteToEmail->lastname = $practicante->lastname;
        $practicanteToEmail->maildisplay = true;
        $practicanteToEmail->mailformat = 1;
        $practicanteToEmail->id = $practicante->id; 
        $practicanteToEmail->alternatename = '';
        $practicanteToEmail->middlename = '';
        $practicanteToEmail->firstnamephonetic = '';
        $practicanteToEmail->lastnamephonetic = '';

        $messageHtml_prac = $titulo.$saludo_prac.$mensaje ;   
        $messageText_prac = html_to_text($messageHtml_prac);

        $email_result_prac = email_to_user($practicanteToEmail, $userFromEmail, $subject, $messageText_prac, $messageHtml_prac, ", ", true);

        if($email_result_prac!=1){
          $resp->practicante = false;
        }else{
          $resp->practicante = true;

          $profesional = get_assigned_professional($id_tal);
          $nombre_profesional = $profesional->firstname." ".$profesional->lastname;
          $saludo_prof = "Estimado profesional $nombre_profesional<br><br>";

          $profesionalToEmail = new stdClass;
          $profesionalToEmail->email = $profesional->email;
          $profesionalToEmail->firstname = $profesional->firstname;
          $profesionalToEmail->lastname = $profesional->lastname;
          $profesionalToEmail->maildisplay = true;
          $profesionalToEmail->mailformat = 1;
          $profesionalToEmail->id = $profesional->id; 
          $profesionalToEmail->alternatename = '';
          $profesionalToEmail->middlename = '';
          $profesionalToEmail->firstnamephonetic = '';
          $profesionalToEmail->lastnamephonetic = '';

          $messageHtml_prof = $titulo.$saludo_prof.$mensaje ;   
          $messageText_prof = html_to_text($messageHtml_prof);

          $email_result_prof = email_to_user($profesionalToEmail, $userFromEmail, $subject, $messageText_prof, $messageHtml_prof, ", ", true);

          if($email_result_prof!=1){
            $resp->profesional = false;
          }else{
            $resp->profesional = true;
          }

        }
      }
      
      return $resp;
  
}



//update_grades_moodle(92,49,5,3);
///*********************************///
///*** Wizard categories methods ***///
///*********************************///
/**
 * It performs the insertion of a category considering whether it is of weighted type or not,
 * after which it inserts the item that represents the category. The latter is necessary for the category to have a weight.
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return Int --- ok->1 || error->0
**/
function insertCategory($course,$father,$name,$weighted,$weight){
    global $DB;
    
    //Instance an object category to use insert_record
    $object = new stdClass;
    $object ->courseid=$course;
    $object ->fullname=$name;
    $object ->parent =$father;
    $object ->aggregation=$weighted;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    $succes=$DB->insert_record('grade_categories',$object);
    
    if($succes)
    {
      if(insertItem($course,$succes,$name,$weight,false)===1)
      {
        return 1;    
      }else{
          return 0;
      }
    }
    return 0;
}

/**
 * It performs the insertion of a category parcial and if it works, it returns the id  the created category
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return int --- ok->id_cat || error->0
**/
function insertCategoryParcial($course,$father,$name,$weighted,$weight){
    global $DB;
      
    //Instance an object category to use insert_record
    $object = new stdClass;
    $object ->courseid=$course;
    $object ->fullname=$name;
    $object ->parent =$father;
    $object ->aggregation=$weighted;
    $object ->timecreated=time();
    $object ->timemodified=$object ->timecreated;
    $object->aggregateonlygraded = 0;
    $object->aggregateoutcomes = 0;

    $succes=$DB->insert_record('grade_categories',$object);
      
    if($succes){
        if(insertItem($course,$succes,$name,$weight,false)===1)
        {
          return $succes;    
        }else{
            return 0;
        }
    }

    return 0;
}

/**
 * It performs the insertion of parcial
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $weighted (aggregation)
 * @param $weight
 * @return Int --- ok-> 1 || error-> 0
**/
function insertParcial($course,$father,$name,$weighted,$weight){
    $succes = insertCategoryParcial($course,$father,$name,$weighted,$weight);
    if($succes != 0){
        if(insertItem($course,$succes,$name,0,true) === 1){
            if(insertItem($course,$succes,"Opcional de ".$name,0,true)===1){
                return 1;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }else{
        return 0;
    }
}


/**
 *Performs the insertion of item, either flat item or an item related to a category,
 *the latter is necessary to be able to assign a weight in case the category is a 
 * daughter of another category with weighted rating
 *
 * @param $course
 * @param $father
 * @param $name
 * @param $valsend
 * @param $item
 * @return Int --- ok-> 1 || error-> 0
**/
function insertItem($course,$father,$name,$valsend,$item){
    global $DB;
    
    //Instance an object item to use insert_record
    if($item)
    {
    $object = new stdClass;
    $object ->courseid=$course;
    $object -> categoryid=$father;
    $object ->itemname=$name;
    $object -> itemnumber=0;
    $object -> itemtype='manual';
    $object -> sortorder=getNextIndex($course);
    $object -> aggregationcoef=$valsend;
    $object -> grademax=5;
    }else{
    $object = new stdClass;
    $object ->courseid=$course;
    $object -> itemtype='category';
    $object -> sortorder=getNextIndex($course);
    $object -> aggregationcoef=$valsend;
    $object -> iteminstance=$father;
    $object -> grademax=5;
    }
    
    $succes=$DB->insert_record('grade_items',$object);
    
    if($succes)
    {
        return 1;
    }else
    {
        return 0;
    }
    
}

/**
 *Make a query to find the last index of the sort element corresponding to the category that is being entered
 *
 * @param $course
 * @return int --- nextindex
**/
function getNextIndex($course){
    global $DB;
    $sql_query = "SELECT max(sortorder) FROM {grade_items} WHERE courseid=".$course.";";
    $output=$DB->get_record_sql($sql_query);
    $nextindex=($output->max)+1;
    return $nextindex;
}


/**
 * Make a html_string with the categories tree of a course identified by $courseid
 *
 * @param $idCourse
 * @return String hmtl
**/
function getCategoriesandItems($courseid){

    global $DB;
   
    $sql_query="SELECT {user_enrolments}.userid AS id
                FROM {enrol} INNER JOIN {user_enrolments} ON ({user_enrolments}.enrolid ={enrol}.id) 
                WHERE courseid=".$courseid.";";
                
    $userid = $DB->get_record_sql($sql_query)->id;
    $context = context_course::instance($courseid);
     //print_r($userid);

    $gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'user', 'courseid'=>$courseid, 'userid'=>$userid));
    $report = new grade_report_user($courseid, $gpr, $context, $userid);
    reduce_table_categories($report);
    if ($report->fill_table()) {
        //print_r($report->tabledata);
        return print_table_categories($report);
    }
}
//getCategoriesandItems(3);

/**
 * Returns the HTML from the flexitable.
 * @param grade_report_user $report info of which will be shown
 * @return string
**/ 
function print_table_categories($report){
    $maxspan = $report->maxdepth;

          /// Build table structure
          $html = "
              <table cellspacing='0'
                     cellpadding='0'
                     summary='" . s($report->get_lang_string('tablesummary', 'gradereport_user')) . "'
                     class='boxaligncenter generaltable user-grade'>
              <thead>
                  <tr>
                      <th id='".$report->tablecolumns[0]."' class=\"header column-{$report->tablecolumns[0]}\" colspan='$maxspan'>".$report->tableheaders[0]."</th>\n";

          for ($i = 1; $i < count($report->tableheaders); $i++) {
              $html .= "<th id='".$report->tablecolumns[$i]."' class=\"header column-{$report->tablecolumns[$i]}\">".$report->tableheaders[$i]."</th>\n";
          }
  
          $html .= "
                  </tr>
              </thead>
              <tbody>\n";
  
          /// Print out the table data
          for ($i = 0; $i < count($report->tabledata); $i++) {
              $html .= "<tr>\n";
              for ($j = 0; $j < count($report->tablecolumns); $j++) {
                  $name = $report->tablecolumns[$j];
                  $class = (isset($report->tabledata[$i][$name]['class'])) ? $report->tabledata[$i][$name]['class'] : '';
                  $colspan = (isset($report->tabledata[$i][$name]['colspan'])) ? "colspan='".$report->tabledata[$i][$name]['colspan']."'" : '';
                  $content = (isset($report->tabledata[$i][$name]['content'])) ? $report->tabledata[$i][$name]['content'] : null;
                  $celltype = (isset($report->tabledata[$i][$name]['celltype'])) ? $report->tabledata[$i][$name]['celltype'] : 'td';
                  $id = (isset($report->tabledata[$i][$name]['id'])) ? "id='{$report->tabledata[$i][$name]['id']}'" : '';
                  $headers = (isset($report->tabledata[$i][$name]['headers'])) ? "headers='{$report->tabledata[$i][$name]['headers']}'" : '';
                  if (isset($content)&&!isCategoryTotal($content)) {
                      if(isCategory($content)){
                        $categoryid = explode("_",$id)[1];
                        $weight = getweightofCategory($categoryid);
                        if(!$weight || intval($weight) === 0){
                            $weight = '-';
                        }else{
                              $weight = '('. floatval($weight).' %)';
                        }  
                        $aggregation = getAggregationofCategory($categoryid);
                      $maxweight = getMaxWeight($categoryid);
                        $html .= "<$celltype $id $headers class='$class' $colspan><div id = '$aggregation' class = 'agg'> $content <p style = 'display: inline' class = 'maxweight' id = '$maxweight'>$weight</p> <button title = \" Crear nuevo item o categoria\" class = \" btn new\" style = \"float: right !important\">+</button> </div></$celltype>\n";
                      }else{
                        $id_item = explode("_",$id)[1];  
                        $weight = getweightofItem($id_item);
                        if(!$weight || intval($weight) === 0){
                            $weight = '-';
                        }else{
                            $weight = '('. floatval($weight).' %)';
                        }  
                        $html .= "<$celltype $id $headers class='$class' $colspan>$content <p style = 'display: inline'>$weight</p> </$celltype>\n";
                      }
                  }
              }
              $html .= "</tr>\n";
          }
  
          $html .= "</tbody></table>";
          return $html;
}


/**
 * Get the max weight that a new item can have in a category.
 *
 * @param $categoryid
 * @return int
**/
 function getMaxWeight($categoryid) {
  global $DB;
  $maxweight = 100;

  $query = "SELECT sum(peso) as total
            FROM
              (SELECT id,
                      SUM(aggregationcoef) AS peso
               FROM mdl_grade_items
               WHERE categoryid = $categoryid
               GROUP BY id
               UNION SELECT item.id,
                            SUM(item.aggregationcoef) AS peso
               FROM mdl_grade_items item
               INNER JOIN mdl_grade_categories cat ON item.iteminstance=cat.id
               WHERE cat.parent = $categoryid
               GROUP BY item.id)AS pesos";
  $result = $DB->get_record_sql($query);

  if($result){
    $weight = $result->total;
  }else{
    $weight = 0;
  }

  $maxweight = $maxweight - $weight;

  return $maxweight;
}


/**
 * Reduce grade information to display in categories tree
 *
 * @param &$report
 * @return null
**/
function reduce_table_categories(&$report){
    $report->showpercentage = false;
    $report->showrange = false; 
    $report->showfeedback = false;
    $report->showcontributiontocoursetotal = false;
    $report->showweight = false;
    $report->showgrade = false; 
    $report->showtotalsifcontainhidden = false;
    $report->setup_table();
}

/**
 * Say if find the string "gradeitemdescriptionfiller" in a parameter $string to determinate if is a total item
 *
 * @param $string
 * @return boolean
**/
function isCategoryTotal($string){
    if(stripos($string, "gradeitemdescriptionfiller") === false && stripos($string, "Total") == false){
        return false;
    }else{
        return true;
    }
    
}

/**
 * Say if find the string "Categoria" or "Category" in a parameter $string
 *
 * @param $string
 * @return boolean
**/

function isCategory($string){
    if((stripos($string, "Categoría") === false)&&(stripos($string, "Category") === false)){
        return false;
    }else{
        return true;
    }
    
}


/**
 * Get the weight of an item.
 *
 * @param $itemid
 * @return int weight
**/
function getweightofItem($itemid){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items}
                  WHERE id = ".$itemid;
                  
    $output = $DB->get_record_sql($sql_query);
    if($output){
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Get the weight of an category.
 *
 * @param $itemid
 * @return int weight
**/
function getweightofCategory($id){
    global $DB;
    
    $sql_query = "SELECT aggregationcoef as weight 
                  FROM {grade_items} item INNER JOIN {grade_categories} cat on item.iteminstance=cat.id 
                  WHERE cat.id = ".$id;
                  
    $output = $DB->get_record_sql($sql_query);
    if($output){
        $weight = $output->weight;
        return $weight;
    }
    return false;
}

/**
 * Get the aggregation tipe of an category.
 *
 * @param $itemid
 * @return int weight
**/

function getAggregationofCategory($categoryid){
    global $DB;
    
    $sql_query = "
        SELECT aggregation
        FROM {grade_categories} 
        WHERE id = '$categoryid'";
    $output = $DB->get_record_sql($sql_query);

    $aggregation = $output->aggregation ;

    return $aggregation;
}

?>
>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
