<?php
// This file is part of Moodle - https://moodle.org/
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
 * Moodle CLI script - Traer las asignaciones históricos hasta la fecha.
 *
 * @package     block_ases
 * @copyright   2022 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../../config.php');
global $CFG;
require_once ($CFG->libdir . '/clilib.php');
require_once (__DIR__ . '/../../core/module_loader.php');
require_once (__DIR__ . '/../ases_report/asesreport_lib.php');
require_once (__DIR__ . '/../pilos_tracking/pilos_tracking_lib.php');
require_once (__DIR__ . '/../role_management/role_management_lib.php');


module_loader('periods');

// @ Todo: escribir la documentación
$usage = "Put a one line summary of what the script does here.

Usage:
    # php demo.php --paramname=<value>
    # php demo.php [--help|-h]

Options:
    -h --help               Print this help.
    --paramname=<value>     Describe the parameter and the meaning of its values.
";

list($options, $unrecognised) = cli_get_params([
    'help' => false,
    'instance' => null,
    'end-period' => null,
    'start-period' => null
], [
    'h' => 'help'
]);

if ($unrecognised) {
    $unrecognised = implode(PHP_EOL . '  ', $unrecognised);
    cli_error(get_string('cliunknowoption', 'core_admin', $unrecognised));
}

if ($options['help']) {
    cli_writeln($usage);
    exit(2);
}
if (empty($options['instance'])) {
    cli_error('Missing mandatory argument instance.', 2);
}
$instanceid = $options['instance'];
$first_period = null;
$last_period = null;
if (empty($options['start-period'])) {
    $first_period = core_periods_get_period_by_name('2019A', $instanceid);
    //cli_error('Missing mandatory argument paramname.', 2);
}
if (empty($options['end-period'])) {
    $last_period = core_periods_get_current_period($instanceid);
    //cli_error('Missing mandatory argument paramname.', 2);
}

$pros = get_professionals_by_instance($instanceid, $first_period->id);
$practicants = null;
/**
 * Lo siento, lo ideal sería cada ciclo y arreglo por aparte y después unirlos
 * con algo como array_merge...
 */
foreach ($pros as $name => $val) {
    $_temp = get_practicantes_profesional($val->id, $instanceid, $first_period->id);
    $pros[$name] = array_column($_temp, 1);
    $pros[$name] = array_flip($pros[$name]);
    $count=0;
    foreach ($pros[$name] as $pract => $mon) {
        $pract_id = $_temp[$count][0];
        $_temp_monitors = get_monitors_of_pract($pract_id, $instanceid, $first_period->id);
        $pros[$name][$pract] = array_flip(array_column($_temp_monitors, 'fullname'));
        $count++;
        $count_students=0;
        foreach ($pros[$name][$pract] as $key => $mon) {
            $_temp_monitors = array_values($_temp_monitors);
            $monitor_id = ($_temp_monitors[$count_students])->id_usuario;
            $_temp_students = get_students_of_monitor($monitor_id, $instanceid, $first_period->id);

            $pros[$name][$pract][$key] = array_flip(
                array_column($_temp_students, 'username')
            );

            $count_students++;
        }
    }
}
cli_write("..Generando archivo csv..\n");

file_put_contents("test.json",json_encode(array_flip($pros)));

cli_write("Terminado.\n");
