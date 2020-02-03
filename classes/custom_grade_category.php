<?php
namespace local_customgrader;
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
 * Custom grader report for ASES utitlities
 *
 * @author     Luis Gerardo Manrique Cardona
 * @package    block_ases
 * @copyright  2018 Luis Gerardo Manrique Cardona <luis.manrique@correounivalle.edu.co>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * 
 */
class custom_grade_category extends \grade_category {
    /**
     * @var number $grade_item Associated grade item id
     */
    public $grade_item;
    /**
     * Add the respective grade item belonging to grade category
     */
    private static function append_grade_item(\grade_category $grade_category) {
        $_grade_category = (object) $grade_category;
        $item = $grade_category->get_grade_item();
        $_grade_category->grade_item = $item->id;
        return $_grade_category;
    }
    /**
     * Finds and returns a grade_category instance based on params.
     *
     * @param array $params associative arrays varname=>value
     * @return custom_grade_category The retrieved grade_category instance or false if none found.
     */
    public static function fetch($params) {
        $category = parent::fetch($params);
        $custom_category = custom_grade_category::append_grade_item($category);
        return $custom_category;
    }

    /**
     * Return all categories by params
     * @param array $params
     * @return array
     */
    public static function fetch_all($params)
    {
        $categories = parent::fetch_all($params);
        $custom_grade_categories = array_map(function($c){return self::append_grade_item($c);}, $categories);
        return $custom_grade_categories;
    }
}