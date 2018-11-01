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

require_once(__DIR__.'/../../../../config.php');
require_once ($CFG->dirroot.'/grade/lib.php');
require_once ($CFG->dirroot.'/grade/report/grader/lib.php');
require_once ($CFG->dirroot.'/lib/gradelib.php');


require_once (__DIR__.'/../../vendor/autoload.php');

require_once (__DIR__.'/../../classes/DAO/BaseDAO.php');
require_once (__DIR__.'/../../classes/EstadoAses.php');
require_once (__DIR__.'/../../classes/EstadoAsesRegistro.php');
require_once (__DIR__.'/../../classes/AsesUser.php');
require_once (__DIR__.'/../../classes/AsesUserExtended.php');
require_once  (__DIR__.'/../student_profile/studentprofile_lib.php');
use  PHPHtmlParser\Dom;

use Latitude\QueryBuilder\Query\SelectQuery;
use function Latitude\QueryBuilder\{alias, on, fn, field, param, literal, QueryInterface, express, criteria, identify, identifyAll, listing};

defined('MOODLE_INTERNAL') || die;
/**
 * Base DAO definition
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Class _params Dummy class for this api params
 * @property string $mdl_user_id
 * @property string $mdl_course_id
 * @property int $page
 */

class _params {}

function _select_ases_users(): SelectQuery {
    return BaseDAO::get_factory()
        ->select()
        ->from(alias(AsesUser::get_table_name_for_moodle(), 'usuario'))
        ->innerJoin(
            alias(AsesUserExtended::get_table_name_for_moodle(), 'user_extended'),
            on('usuario.'.AsesUser::ID, 'user_extended.'.AsesUserExtended::ID_ASES_USER))
        ->innerJoin(
            alias('{user}', 'mdl_user'),
            on('mdl_user.id', 'user_extended.'.AsesUserExtended::ID_MOODLE_USER)
        )
        ->limit(5)
        ;
}

function _select_active_ases_users(): SelectQuery {
    return _select_ases_users()
        ->innerJoin(
            alias('{talentospilos_est_estadoases}', 'est_estadoases'),
            on('est_estadoases.id_estudiante', 'usuario.id'))
        ->innerJoin(
            alias('{talentospilos_estados_ases}', 'estados_ases'),
            on('estados_ases.id', 'est_estadoases.id_estao_ases'))
        //->where(field('estados_ases.nombre')->eq('seguimiento'))*/

        ;
}

class ases_grade_report_grader extends grade_report_grader {


    public $course_id;
    public $instance_id;

    function __construct(int $courseid, $instance_id, object $gpr, $context,  $page = null, ?int $sortitemid = null)
    {
        parent::__construct($courseid, $gpr, $context, $page, $sortitemid);
        $this->instance_id = $instance_id;
    }

    function get_left_rows($displayaverages) {
        $rows = parent::get_left_rows($displayaverages);
        $doc = new DOMDocument();
        /* Se editan los href de cada nombre de usuario en la tabla*/
        /* @var html_table_row $row */
        foreach($rows as $row) {
            /* @var html_table_cell $cell */
            foreach($row->cells as &$cell) {
                if(strpos($cell->text, 'username') && strpos($cell->text, 'href')  ) {
                    /* A element */
                    /* @var DOMDocument $document */

                    $student_code = $this->users;
                    $doc->loadHTML($cell->text);
                    /* @var string $cell_user_profile_link Example: http://localhost/moodle/user/profile.php?id=122098 */

                    /**
                     * The cell->text have two ´a´ elements, first is a link of the user image
                     * second ´a´ is a link of username
                     */
                    $cell_user_image_link_str = $doc->getElementsByTagName('a')->item(0)->getAttribute('href');
                    $cell_user_profile_url = parse_url($cell_user_image_link_str);
                    $url_query = array();
                    parse_str($cell_user_profile_url['query'], $url_query);
                    $cell_user_id = $url_query['id'];
                    /* $this->users only have the fields than return ´user_picture::fields functions´*/
                    $user_complete= user_get_users_by_id([$cell_user_id])[$cell_user_id];
                    $link_general_report = get_student_profile_url($this->courseid, $this->instance_id, $user_complete->username);
                    /* Change the link for ´a´ of user image*/
                    $doc->getElementsByTagName('a')->item(0)->setAttribute('href', $link_general_report->out());
                    /* Change the link for ´a´ of user name*/
                    $doc->getElementsByTagName('a')->item(1)->setAttribute('href', $link_general_report->out());
                    $new_text= $doc->saveXML();
                    $cell->text = $new_text;

                }
            }
        }
    return $rows;
    }
    /**
     *
     */
    function setup_users()
    {
        global $DB;
        parent::setup_users(); // TODO: Change the autogenerated stub

        $ids_ases_users_active = <<<SQL

        select distinct  mdl_talentospilos_user_extended.id_moodle_user from {talentospilos_usuario} mdl_talentospilos_usuario
inner join mdl_talentospilos_user_extended
    on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_usuario.id
inner join {talentospilos_est_estadoases} mdl_talentospilos_est_estadoases
    on mdl_talentospilos_user_extended.id_ases_user = mdl_talentospilos_est_estadoases.id_estudiante
inner join {talentospilos_estados_ases} mdl_talentospilos_estados_ases
    on mdl_talentospilos_est_estadoases.id_estado_ases = mdl_talentospilos_estados_ases.id
where mdl_talentospilos_estados_ases.nombre = 'seguimiento'
and mdl_talentospilos_user_extended.tracking_status = 1

        
SQL;

        $this->userwheresql .= "AND u.id in ($ids_ases_users_active)";
    }
}

/**
 * Class API
 * @property _params $params
 */
class API {
    private $gpr;
    protected $params;
    public function __construct() {
        global $DB;
        $this->init_params();
        $sortitemid = 0;
        $context = context_course::instance($this->params->mdl_course_id);
        $this->gpr = new grade_plugin_return(array('type'=>'report', 'plugin'=>'grader', 'courseid'=>$this->params->mdl_course_id));
        $report = new ases_grade_report_grader($this->params->mdl_course_id, 450299, $this->gpr, $context, $this->params->page? $this->params->page: null, $sortitemid);
        $report->load_users();
        $report->load_final_grades();

        print_r($report->get_grade_table());

    }

    /**
     * @return _params
     */
    private function init_params() {
        $this->params = json_decode(file_get_contents('php://input'));
    }
}
$r = new API();

