<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../controllers/patents.php';
include_once '../controllers/patent_meta.php';

$user_lang = $_POST["locale"];
$user_time_zone = $_POST["timezone"];

// setting up translation and time_zone
include_once '../include/common.inc.php';
include_once '../include/time-zone.inc.php';

$database = new Database($LOG);
$db = $database->getConnection();

$patents = new Patents($db);
$patent_metas = new PatentMeta($db);

if(isset($_FILES['image'])) {
    $fileName  =  $_FILES['image']['name'];
    $tempPath  =  $_FILES['image']['tmp_name'];
    $fileSize  =  $_FILES['image']['size'];
}

$patents->id = $_POST["patentID"];
$old_patent_name = $patents->get_patent_name();

$patent_metas->patent_id = $_POST["patentID"];

$patents->updated = $timestamp;
$patent_metas->updated = $timestamp;
$patent_metas->created = $timestamp;

$image_name = $patent_metas->get_meta_value('patentImageTitle');
$old_patent_category = $patent_metas->get_meta_value('Patent_category');
$old_patent_status = $patent_metas->get_meta_value('Patent_status');
$old_patent_start_price = $patent_metas->get_meta_value('start_price');
$old_patent_buyout_price = $patent_metas->get_meta_value('Buyout_price');
$old_patent_abstract = $patent_metas->get_meta_value('Patent_abstract');

$old_patent_summary = $patent_metas->get_meta_value('Patent_summary');
$old_patent_filling_info = $patent_metas->get_meta_value('Filling_info');
$old_patent_app_status = $patent_metas->get_meta_value('Application_status');
$old_patent_patent_url = $patent_metas->get_meta_value('Patent_URL');

$prosecution_history = json_decode($_POST["proHist"]);

use \Firebase\JWT\JWT;

$jwt=isset($_POST["authToken"]) ? $_POST["authToken"] : "";

if($jwt){
 
    // if decode succeed
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        try {

            if(isset($_FILES['image'])) {
                $upload_path = '../uploads/patentImages/';
                $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION));

                if($fileSize < 5000000){
                    unlink($upload_path . $image_name);
                    $rand = mt_rand(100000,999999);
                    $new_file_name = "".$_POST["patentID"]."_".$rand."";
                    move_uploaded_file($tempPath, $upload_path . $new_file_name .".".$fileExt); // move file from system temporary path to our upload folder path 
                    $patent_metas->update_patent_metas("patentImageTitle", $new_file_name.".".$fileExt);
                }
            }

            if($old_patent_name !== $_POST["patentName"]) {
                $patents->patent_name = $_POST["patentName"];
                $patents->update_patent();
            }
            if($old_patent_category !== $_POST["patentCategory"]) {
                $patent_metas->update_patent_metas("Patent_category", $_POST["patentCategory"]);
            }
            if($old_patent_status !== $_POST["patentStatus"]) {
                $patent_metas->update_patent_metas("Patent_status", $_POST["patentStatus"]);
            }
            if($old_patent_start_price !== $_POST["startPrice"]) {
                $patent_metas->update_patent_metas("start_price", $_POST["startPrice"]);
            }
            if($old_patent_buyout_price !== $_POST["buyoutPrice"]) {
                $patent_metas->update_patent_metas("Buyout_price", $_POST["buyoutPrice"]);
            }
            if($old_patent_abstract !== $_POST["patentAbstract"]) {
                $patent_metas->update_patent_metas("Patent_abstract", $_POST["patentAbstract"]);
            }
            if($old_patent_summary !== $_POST["patentSummary"]) {
                $patent_metas->update_patent_metas("Patent_summary", $_POST["patentSummary"]);
            }
            if($old_patent_filling_info !== $_POST["fillingInfo"]) {
                $patent_metas->update_patent_metas("Filling_info", $_POST["fillingInfo"]);
            }
            if($old_patent_app_status !== $_POST["appStatus"]) {
                $patent_metas->update_patent_metas("Application_status", $_POST["appStatus"]);
            }
            if($old_patent_patent_url !== $_POST["patentURL"]) {
                $patent_metas->update_patent_metas("Patent_URL", $_POST["patentURL"]);
            }

            $patent_metas->update_patent_metas("proHist", $_POST["proHist"]);

            http_response_code(200);

            echo json_encode(array (
                "message" => "Patent updated successfully",
                "success" => true
            ));

        } catch(Exception $e) {
            http_response_code(200);

            echo json_encode(array (
                "message" => "Failed to update the patent. Please try again later.",
                "error" => $e,
                "success" => false
            ));
        }
    }
    catch (Exception $e){
        $LOG->error($e->getMessage());

        http_response_code(401);

        echo json_encode(array (
            "message" => "Access Denied",
            "error" => $e,
            "success" => false
        ));
    }

}


