<<<<<<< HEAD
<?php

require_once('query.php');


if(isset($_POST['dat']) && isset($_POST['user'])){
    
    if($_POST['dat'] == 'semesters'){
        $total_semesters = get_grades_courses_student_semester($_POST['user'], true);
        
        echo json_encode($total_semesters);
    }
}

=======
<?php

require_once('query.php');


if(isset($_POST['dat']) && isset($_POST['user'])){
    
    if($_POST['dat'] == 'semesters'){
        $total_semesters = get_grades_courses_student_semester($_POST['user'], true);
        
        echo json_encode($total_semesters);
    }
}

>>>>>>> db_management
print_r(get_grades_courses_student_semester(144,true));