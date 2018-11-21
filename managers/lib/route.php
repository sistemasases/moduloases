<?php
require_once (__DIR__ . '/_limonade.php');
use function Limonade\route_build;
/**
 *
 * Return a key value array with the params given in a URL path
 * For example, if you enter the path format '/user/:id' and the path
 * '/user/55' then `array('id'=>55')` is returned, if a path given is
 * '/post/50' path is given and the same path format showed before is given,
 * `false` is returned
 *
 * @param $path string URL path
 * @param $path_format string URL path format like limonade path definition
 * @return array|false Return an array key-value if exist some
 *  param in the route, false otherwise
 * @see https://github.com/sofadesign/limonade
 * @return array
 */
function route_get_params($path_format, $path) {
    $pattern = route_compile_path_format($path_format);
    $names = route_get_names($path_format);
    $matches = array();
    preg_match($pattern, $path, $matches);
    /* In limonade always, the first match is the complete path, the rest of matches are the real matches */
    array_shift($matches);
    if(preg_match($pattern, $path, $matches)) {
        if (count($matches) > 1) {
            array_shift($matches);
            $n_matches = count($matches);
            $n_names = count($names);
            if ($n_matches < $n_names) {
                $a = array_fill(0, $n_names - $n_matches, null);
                $matches = array_merge($matches, $a);
            } else if ($n_matches > $n_names) {
                $names = range($n_names, $n_matches - 1);
            }
            $arr_comb = array_combine($names, $matches);
            return $arr_comb;
        }
    }
    return array();

}

function route_compile_path_format($path_format) {
    $route = route_build('GET', $path_format, null);
    return $route['pattern'];
}
function route_get_names($path_format) {
    $route = route_build('GET', $path_format, null);
    return $route['names'];
}