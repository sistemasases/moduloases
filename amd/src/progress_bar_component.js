/**
 * Progress bar component
 * @module amd/src/progress_bar_component
 * @author Luis Gerardo Manrique Cardona
 * @copyright 2018 Luis Gerardo Manrique Cardonaz <luis.manrique@correounivalle.edu.co>
 * @license  http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(['jquery'], function($){

    return{
        init: function(){
            /* Cada elemento .progressbar li es un item de el menu*/
            var menu_size = $('.progressbar li').length;
            /**
             *  Cada elemento de progressbar debe tener un ancho de tal forma que la suma de los porcentajes de los
             *  anchos no supere el 100% */
            var progrses_bar_li_width = 100 / menu_size;
            $('.progressbar li').css('width', progrses_bar_li_width + '%');
            /* Si la barra de progreso se pinta inmediatamente se carga la pagina antes de ajustar el ancho de los
             * '''li''' entonces la pagina tendra comportamientos extraños, para resolver esto, la barra de progreso
             * solamente se muestra cuando su ancho ha sido ajustado
             */
            $('.progressbar-container').css('display', 'inline');
        }
    };

});