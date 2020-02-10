<?php
/**
 * Created by PhpStorm.
 * User: Alejandro Palacios Hurtado
 * Date: 1/24/20
 * Time: 10:40 AM
 */

// Standard GPL and phpdocs

require_once(dirname(__FILE__). '/../../../../config.php');
require_once(dirname(__FILE__) . '/customgrader_lib.php');

header('Content-Type: application/json');

global $USER;

$raw_data = file_get_contents("php://input");

$input = json_decode( $raw_data );

/**
 * Api
 * @author Alejandro Palacios Hurtado
 * @see customgrader_lib.php
 */

// Example of valid input. params = Parameters
// { "function":"get_***", "params":[ ases_student_code ] }

if( isset( $input->function ) ){

    if( $input->function == "get_grader_data" ){

        $data = get_normalized_all_grade_info($input->courseid);
        echo json_encode($data);

    }

    if( $input->function == "update_grade" ){

        $userid = $input->userid;
        $itemid =  $input->itemid;
        update_grade_items_by_course( $input->courseid);

        if(!update_grades_moodle_($userid ,$itemid, $input->finalgrade, $input->courseid)) {
            return_with_code(-3);
            return false;
        } else {
            echo json_encode (array(
                'grade'=> grade_grade::fetch(array('userid'=>$userid , 'itemid'=>$itemid)),
                'other_grades'=> get_student_grades($input->courseid, null, $userid)
            ));
        }

    }

    if( $input->function == "update_category" ){

        /** @var  $category grade_category */
        $category = $input->category;
        $editedCategoryResponse = editCategory($category);

        $levels = get_table_levels($category->courseid);
        $response = [
            'category' => $editedCategoryResponse,
            'levels' => $levels
        ];
        echo json_encode(array($response));

    }


    if( $input->function == "update_item" ){

        /** @var  $item grade_item */
        $item = $input->item;
        $editedItemResponse =  editItem($item);
        update_grade_items_by_course( $input->courseid);
        $levels = get_table_levels($item->courseid);

        $response = [
            'item' => $editedItemResponse,
            'levels' => $levels,
            'other_grades'=> get_student_grades($editedItemResponse->courseid)
        ];
        echo json_encode(array($response));

    }


    if( $input->function == "add_category" ){

        /** @var  $category grade_category */
        $category = $input->category;
        $weight = $input->weight;
        $cat_creation_response = insertCategory(
            $category->courseid,
            $category->parent_category,
            $category->fullname,
            $category->aggregation,
            $weight);
        if ($cat_creation_response !== false) {
            $category = $cat_creation_response['category'];
            $item = $cat_creation_response['category_item'];
        }
        $levels = get_table_levels($category->courseid);
        $response = [
            'levels'=>$levels,
            'category_item'=>$item,
            'category'=>$category,
        ];
        echo json_encode(array($response));

    }

    if( $input->function == "add_item" ){

        /** @var  $item grade_item */
        $item = $input->item;
        $item_or_false = insertItem($item->courseid, $item->parent_category, $item->itemname, $item->aggregationcoef );
        if ($item_or_false !== false) {
            $levels = get_table_levels($item->courseid);
            $response = [
                'levels'=>$levels,
                'item'=>$item_or_false,
            ];
            echo json_encode(array($response));
        } else {
            return_with_code(-3);
            return false;
        }

    }

    if( $input->function == "add_partial_exam" ){

        $partial_exam = $input->partial_exam;
        $insert_response = insertParcial(
            $partial_exam->courseid,
            $partial_exam->parent_category,
            $partial_exam->itemname,
            $partial_exam->aggregation,
            $partial_exam->aggregationcoef );
        if ($insert_response !== false) {
            $levels = get_table_levels($partial_exam->courseid);
            $response = [
                'levels'=>$levels,
            ];
            echo json_encode(array(array_merge($response, $insert_response)));
        } else {
            eturn_with_code(-3);
            return false;
        }

    }


    if( $input->function == "delete_item" ){

        $item_id = $input->itemId;
        $item = grade_item::fetch(array('id'=>$item_id));
        $course_id =  $item->courseid;
        $deleted = delete_item($item_id, $course_id);
        $levels = get_table_levels($course_id);
        $response = [
            'levels' => $levels
        ];
        echo json_encode(array($response));

    }

    if( $input->function == "delete_category" ){

        $category_id = $input->categoryId;

        $category = grade_category::fetch(array('id'=>$category_id));
        $course_id= $category->courseid;
        $deleted = delete_category($category_id, $course_id);

        $levels = get_table_levels($course_id);
        $response = [
            'levels' => $levels
        ];
        echo json_encode(array($response));

    }



}else{
    return_with_code( -2 );
}

function return_with_code( $code ){

    switch( $code ){

        case -1:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "You are not allowed to access this resource.",
                    "data_response" => ""
                )
            );
            break;
        case -2:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Error in the scheme.",
                    "data_response" => ""
                )
            );
            break;
        case -3:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Invalid values in the parameters.",
                    "data_response" => ""
                )
            );
            break;
        case -4:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Function not defined.",
                    "data_response" => ""
                )
            );
            break;

        case -5:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "Duplicate.",
                    "data_response" => ""
                )
            );
            break;

        case -99:
            echo json_encode(
                array(
                    "status_code" => $code,
                    "error_message" => "critical error.",
                    "data_response" => ""
                )
            );
            break;

    }

    die();
}