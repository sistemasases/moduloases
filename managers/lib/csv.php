<?php


namespace csv;
/**
 * Function than allow to download a csv object based in an array of objects
 *
 * **The execution of this method send the file content, you not need print or echo
 * the method response**
 * @param $array
 * @param string $filename
 * @param string $delimiter
 * @param $skip_properties array A list of properties than the csv should not contain
 *  Example: if you not want the property names 'hidden_property',
 *  $skip_properties = ['hidden_property']
 */
function array_to_csv_download($array, $filename = "export.csv", $delimiter=";", $skip_properties=array()) {

    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');

    // open the "output" stream
    // see http://www.php.net/manual/en/wrappers.php.php#refsect2-wrappers.php-unknown-unknown-unknown-descriptioq
    $f = fopen('php://output', 'w');
    $first_object  = $array[0];
    $headers = \reflection\get_object_properties_description($first_object, $skip_properties);
    fputcsv($f, $headers, $delimiter);
    foreach ($array as $line) {
        $line = (array) $line;
        foreach($skip_properties as $skip_property) {
            unset($line[$skip_property]);
        }
        fputcsv($f, $line, $delimiter);
    }
}