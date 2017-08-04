<?php
/*
 * Consultas modulo registro de notas.
 */
require_once(__DIR__ . '/../../../../config.php');
require_once $CFG->libdir.'/gradelib.php';
//require_once('../../../../querylib.php');
require_once $CFG->dirroot.'/grade/lib.php';
require_once $CFG->dirroot.'/grade/report/user/lib.php'; 
// require_once $CFG->dirroot.'/grade/report/grader/lib.php';
// require_once $CFG->dirroot.'/grade/lib.php';



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
            (SELECT moodle_user.id
             FROM {user} moodle_user
             INNER JOIN {user_info_data} data ON moodle_user.id = data.userid
             INNER JOIN {user_info_field} field ON field.id = data.fieldid
             WHERE field.shortname = 'idtalentos'
               AND data.data IN
                 (SELECT CAST(id AS VARCHAR)
                  FROM {talentospilos_usuario})
               AND moodle_user.id IN
                 (SELECT user_m.id
                  FROM {user} user_m
                  INNER JOIN {cohort_members} memb ON user_m.id = memb.userid
                  INNER JOIN {cohort} cohorte ON memb.cohortid = cohorte.id
                  WHERE SUBSTRING(cohorte.idnumber
                                  FROM 1
                                  FOR 2) = '$cohort'))";
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
                        $html .= "<$celltype $id $headers class='$class' $colspan><div id = '$aggregation' class = 'agg'> $content <p style = 'display: inline'>$weight</p> <button title = \" Crear nuevo item o categoria\" class = \" btn new\" style = \"float: right !important\">+</button> </div></$celltype>\n";
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