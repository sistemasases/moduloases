<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Course and teacher report view
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once (__DIR__ . '/../../../config.php');


echo '<pre>';
$sql = <<<SQL
select mdl_cohort_members.id as mdl_cohort_members_id, username as codigo, firstname, lastname, mdl_talentospilos_semestre.nombre as mdl_talentospilos_semestre_nombre from mdl_talentospilos_history_academ
    inner join mdl_talentospilos_semestre
    on mdl_talentospilos_semestre.id = mdl_talentospilos_history_academ.id_semestre
inner join mdl_talentospilos_user_extended
on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_history_academ.id_estudiante
inner join mdl_user
on mdl_user.id = mdl_talentospilos_user_extended.id_moodle_user
inner join mdl_cohort_members
    on mdl_cohort_members.userid = mdl_user.id
inner join mdl_talentospilos_inst_cohorte
    on mdl_cohort_members.cohortid = mdl_talentospilos_inst_cohorte.id_cohorte
inner join mdl_cohort
    on mdl_cohort.id = mdl_talentospilos_inst_cohorte.id_cohorte
SQL;

print_r($DB->get_records_sql($sql));
echo '</pre>';