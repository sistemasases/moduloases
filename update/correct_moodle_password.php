<?php
/*
require_once (__DIR__ .'/../../../config.php');
require_once(__DIR__ .'/../managers/user_management/user_lib.php');
require_once(__DIR__.'/../../../user/lib.php');
$wrong_users_usernames = [
    '1928485-3660',
    '1923565-3550',
    '1926111-3747',
    '1923035-3743',
    '1924861-3267',
    '1923174-3267',
    '1927381-3267',
    '1928481-3267',
    '1925002-3267',
    '1926178-3267',
    '1929793-3267',
    '1923378-3267',
    '1924983-3267',
    '1930475-3267',
    '1926959-3267',
    '1924226-3267',
    '1327951-3743',
    '1923674-3140',
    '1926247-3146',
    '1922367-3747',
    '1923885-3748',
    '1929019-3749',
    '1922819-3749',
    '1925106-3267',
    '1925137-3267',
    '1924716-3267',
    '1926574-3340',
    '1926504-3267',
    '1929481-3267',
    '1926154-3267',
    '1928008-3267',
    '1925237-3461',
    '1924374-3461',
    '1932460-3267',
    '1925293-3267',
    '1930862-3267',
    '1923407-3660'
];
try {
    foreach($wrong_users_usernames as $username) {
        $user = core_user::get_user_by_username($username);
        if(!$user) {
            echo "La persona con username $username no existe</br>";
            continue;
        }
        $last_login_date = date("Y-m-d", $user->lastlogin);
        $error_date ='2019-05-15';
        if($last_login_date>$error_date) {
            echo "El usuario $user->firstname, $user->username ya habia accedido en la fecha $last_login_date, no se cambia la contraseña</br>";
            continue;
        }
        $student_code = explode('-', $username)[0];
        $user->password = user_get_password($student_code, $user->firstname, $user->lastname);
        echo "Cambiada la contraseña de el usuario $user->firstname, $user->username a $user->password </br>";

        user_update_user($user);
    }

}catch (Error $e) {
    print_r($e);
}

*/
