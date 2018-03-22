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
 * Estrategia ASES
 *
 * @author     Juan Pablo Moreno Muñoz
 * @package    block_ases
 * @copyright  2018 Juan Pablo Moreno Muñoz <moreno.juan@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(dirname(__FILE__). '/../../../../config.php');


function get_created_items_per_course(){
    global $DB;

    $query = "SELECT DISTINCT materias_criticas.id, courses.shortname, courses.fullname, 
                users.firstname, users.lastname, COUNT(items.id) AS count_items
                FROM
                    (SELECT id
                    FROM {course} AS courses 
                        INNER JOIN (SELECT codigo_materia 
                                    FROM {talentospilos_materias_criti}) AS materias_criticas
                                    ON materias_criticas.codigo_materia = SUBSTR(courses.shortname, 4, 7)
                        WHERE SUBSTR(courses.shortname, 15, 4) = '2018') AS materias_criticas

                        INNER JOIN

                            (SELECT DISTINCT enrols.courseid
                            FROM {cohort_members} AS members 
                            INNER JOIN {cohort} AS cohorts ON cohorts.id = members.cohortid
                            INNER JOIN {user_enrolments} AS enrolments ON  enrolments.userid = members.userid
                            INNER JOIN {enrol} AS enrols ON enrols.id = enrolments.enrolid
                            WHERE (cohorts.idnumber LIKE 'SPP%') OR (cohorts.idnumber LIKE 'SPE%')) AS cursos_ases

                            ON cursos_ases.courseid = materias_criticas.id

                            INNER JOIN {course} AS courses ON courses.id = materias_criticas.id
                            INNER JOIN {grade_items} AS items ON items.courseid = courses.id
                            INNER JOIN {context} AS context ON courses.id = context.instanceid
                            INNER JOIN {role_assignments} AS role_assignments ON role_assignments.contextid = context.id
                            INNER JOIN {user} AS users ON users.id = role_assignments.userid
                            INNER JOIN {role} AS roles ON roles.id = role_assignments.roleid

                            WHERE role_assignments.roleid = 3

                            GROUP BY materias_criticas.id, courses.shortname, courses.fullname, users.firstname, users.lastname";

    $result = $DB->get_records_sql($query);

    
}