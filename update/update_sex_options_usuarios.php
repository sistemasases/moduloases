<?php
// require_once (__DIR__ . '/../../../config.php');
// require_once(__DIR__ . '/../classes/Sexo.php');
// require_once(__DIR__ . '/../classes/AsesUser.php');


// $users = AsesUser::get_all();
// /* @var Sexo $male */;
// $male = Sexo::get_by(array(Sexo::SEXO => 'Masculino'));
// /* @var Sexo $female */;
// $female = Sexo::get_by(array(Sexo::SEXO => 'Femenino'));
// /* @var Sexo $none */;
// $none = Sexo::get_by(array(Sexo::SEXO => 'NO REGISTRA'));
// print_r($female);
// print_r($male);
// print_r($none);

// $some_non_trivial = Sexo::get_by(array(Sexo::SEXO => 'Intersexual'));
// // $DB->start_delegated_transaction();
// foreach($users as $user) {
//     if($user->sexo === '' || $user->sexo === 'N') {
//         $user->sexo = $none->id;
//     }
//     if($user->sexo === 'F') {
//         $user->sexo = $female->id;
//     }
//     if($user->sexo === 'M') {
//         $user->sexo = $male->id;
//     }
//     $user->update();
// }

// $users =AsesUser::get_all();
// echo '<pre>';
// print_r(array_slice($users, 0 , 10));
// //$DB->force_transaction_rollback();

?>