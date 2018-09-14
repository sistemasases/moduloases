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
 * Talentos Pilos
 *
 * @author     Luis Gerardo Manrique Cardona 
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona  <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die;
/**
 * Common functions to cohort than does not exists in moodle core
 */
class cohort_lib {
    /**
    * Return all moodle cohorts filtered by id_instance
    * @param string $id_instance
    * @return array $cohorts Cohorts filtered by id_instance, empty array if does not exists 
    * @see {@link https://docs.moodle.org/35/en/Cohorts}
    */
    public static function get_cohorts($id_instance)
    {
        global $DB;


        $sql_query = "SELECT * FROM {cohort}
                        WHERE  id IN (SELECT id_cohorte
                                        FROM   {talentospilos_inst_cohorte}
                                        WHERE  id_instancia = $id_instance)";

        $cohorts = $DB->get_records_sql($sql_query);
        return $cohorts;
    }

    /**
     * Get the moodle url of ases cohort configuration
     * @return moodle_url Absolute url of page for configurations, empty string if some error occurs
     * @example http://localhost/moodle/cohort/index.php?contextid=934651
     * @see moodle lib/weblib.php, class moodle_url
     */
    public static function get_context_management_url(): moodle_url  {
        $ases_context = cohort_lib::get_ases_context();
        if(!$ases_context && !$ases_context->context_id) {
            return '';
        }
        $context_id = $ases_context->context_id ;
        $url =  new moodle_url('/cohort/index.php', array('contextid'=>$context_id));
        return $url;
    } 
    /**
     * Check if a ases user is in some ases context 
     */
    /**
     * Get the primary category where the ases program is defined
     * @see {@link  https://docs.moodle.org/35/en/Course_categories}
     * @see Table mdl_context
     * @return mixed Context id and category id of the context of ases, 0 if exist some error
     * @example stdClass Object ( [context_id] => 934651 [category_id] => 30940 )
     */
    public static function get_ases_context() {
        global $DB ;
        $plugin_name='ases';
        /**
         * Get the known context when block ases is defined, get the courses under this categories and 
         * get the parent category of that category, this should be the category where ases program  
         * is actualy defining its courses
         * @example
         * Ases block is defined at course 1002 (Programming languages), and the course 1002 is in the category 82
         * (Some Category Name), the parent category of 82 is the course category 30940 (DIRECCIÓN ESTRATEGIA DE ACOMPAÑAMIENTO ASES)
         * stdClass Object ( [context_id] => some context [category_id] => 30940 )
         */ 
          
        $sql_query = 
            "SELECT id AS context_id, instanceid AS category_id FROM {context}  WHERE instanceid =( SELECT  cc.parent AS category_parent
            FROM {user} u
            INNER JOIN {role_assignments} ra ON ra.userid = u.id
            INNER JOIN {context} ct ON ct.id = ra.contextid
            INNER JOIN {course} c ON c.id = ct.instanceid
            INNER JOIN {role} r ON r.id = ra.roleid
            INNER JOIN {course_categories} cc ON cc.id = c.category
            WHERE r.id =5 and u.id IN (select tpu.id_moodle_user FROM {talentospilos_user_extended} AS tpu LIMIT 200)
            AND  ct.id in (SELECT DISTINCT parentcontextid FROM {block_instances} WHERE blockname = '$plugin_name') limit 1)
            and contextlevel = 40";
        try {
            $course_category = $DB->get_record_sql($sql_query);
            return $course_category;
        } catch (Exception $e) {
            error_log($e->getMessage());
            return false;
        }

    }
}
?>