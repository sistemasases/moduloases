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

>>>>>>> 97c7d23d80c7365c0b40027b0d4abac40b2e33b4
print_r(get_grades_courses_student_semester(144,true));