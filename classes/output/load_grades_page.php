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
 * Load grades
 *
 * @author     David S. Cortés
 * @package    block_ases
 * @copyright  2022 David S. Cortés <david.cortes@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_ases\output;

use renderable;
use renderer_base;
use templatable;
use stdClass;

class load_grades_page implements renderable, templatable
{
    /** @var string $sometext Some text to show how to pass data to a template. */
    var $data = null;

    public function __construct($data) {
        $this->data = $data;
    }
    public function export_for_template(renderer_base $output)
    {
        $data = new stdClass();
        $data->data = $this->data;
        return $data;
    }
}