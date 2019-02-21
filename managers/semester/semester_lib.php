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
 * Semester lib
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2016 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace semester;
/**
 * Get the semester_name regex
 * @see talentospilos_semestre.nombre
 * @return string
 */
function get_semester_name_regex(): string {
    return '/^[0-9]{4}[A|B]{1}$/';
}

/**
 * Check if a given semester name is valid
 * @param string $semester_name
 * @see get_semester_name_regex()
 * @return bool
 */
function valid_semester_name(string $semester_name ): bool {
    return preg_match(get_semester_name_regex(), $semester_name);
}
