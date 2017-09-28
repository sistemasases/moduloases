<?php

require_once('academic_lib.php');


if(isset($_POST['dat']) && isset($_POST['user'])){
    
    if($_POST['dat'] == 'semesters'){
        $total_semesters = get_grades_courses_student_by_semester($_POST['user'], true);
        
        echo json_encode($total_semesters);
    }
}

