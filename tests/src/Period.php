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
 * Doble de prueba para el core de períodos.
 * @see https://phpunit.readthedocs.io/es/latest/test-doubles.html
 *
 * @author David S. Cortés <david.cortes@correounivalle.edu.co>
 */
class Period
{
    private static $periods = [
        [
            'nombre' => '2020A',
            'fecha_inicio' => '2020-08-01',
            'fecha_fin' => '2020-08-13',
            'id_instancia' => '450299'
        ],
        [
            'nombre' => '2020B',
            'fecha_inicio' => '2020-08-14',
            'fecha_fin' => '2021-01-29',
            'id_instancia' => '450299'
        ],
        [
            'nombre' => '2021A',
            'fecha_inicio' => '2021-02-01',
            'fecha_fin' => '2021-09-30',
            'id_instancia' => '450299'
        ],
    ];

    /**
     * Retorna todos los periodos ($periods)
     * @return object
     */
    public function get_all_periods()
    {
        return self::periods;
    }


    /**
     * Retorna el período actual, el cual por efectos de simplicidad
     * será el último elemento de $periods.
     * @return object
     */
    public function get_current_period()
    {
        return array_pop(self::periods);
    }
}
