<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// files needed to connect to database
include_once '../config/database.php';
include_once '../controllers/patents.php';
include_once '../controllers/patent_meta.php';

$data = json_decode(file_get_contents("php://input"), true);

$user_lang = $_POST["locale"];
$user_time_zone = $_POST["timezone"];

$user_id = $_POST["userID"];

include_once '../include/common.inc.php';
include_once '../include/time-zone.inc.php';

$database = new Database($LOG);
$db = $database->getConnection();

$patents = new Patents($db);
$patent_metas = new PatentMeta($db);

$fileName  =  $_FILES['image']['name'];
$tempPath  =  $_FILES['image']['tmp_name'];
$fileSize  =  $_FILES['image']['size'];

$prosecution_history = json_decode($_POST["proHist"]);


use \Firebase\JWT\JWT;

$jwt=isset($_POST["authToken"]) ? $_POST["authToken"] : "";

if($jwt){
 
    // if decode succeed
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        if(empty($fileName)) {
            $errorMSG = json_encode(array("message" => "please select image", "status" => false));	
            echo $errorMSG;
        }

        $upload_path = '../uploads/patentImages/'; // set upload folder path 

        $fileExt = strtolower(pathinfo($fileName,PATHINFO_EXTENSION)); // get image extension

        $patents->user_id = $user_id;
        $patents->patent_name = $_POST["patentName"];
        $patents->created = $timestamp;
        $patents->updated = $timestamp;
        $patent_id = $patents->create_patent();
        if($patent_id) {
            if($fileSize < 5000000){
                move_uploaded_file($tempPath, $upload_path . $patent_id.".".$fileExt); // move file from system temporary path to our upload folder path 
            }

            $patent_metas->patent_id = $patent_id;
            $patent_metas->created = $timestamp;
            $patent_metas->updated = $timestamp;

            try {
                $patent_metas->create_patent_meta('patentImageTitle', $patent_id.".".$fileExt);
                $patent_metas->create_patent_meta('Owner_name', $_POST["userName"]);
                $patent_metas->create_patent_meta('Patent_category', $_POST["patentCategory"]);
                $patent_metas->create_patent_meta('Patent_status', $_POST["patentStatus"]);
                $patent_metas->create_patent_meta('start_price', $_POST["startPrice"]);
                $patent_metas->create_patent_meta('Buyout_price', $_POST["buyoutPrice"]);
                $patent_metas->create_patent_meta('Patent_abstract', $_POST["patentAbstract"]);

                $patent_metas->create_patent_meta('Patent_summary', $_POST["patentSummary"]);
                $patent_metas->create_patent_meta('Filling_info', $_POST["fillingInfo"]);
                $patent_metas->create_patent_meta('Application_status', $_POST["appStatus"]);
                $patent_metas->create_patent_meta('Patent_URL', $_POST["patentURL"]);

                $patent_metas->create_patent_meta('proHist', $_POST["proHist"]);

                $LOG->info("Patent created succcessfully for".$user_id);

                http_response_code(200);

                echo json_encode(array (
                    "message" => "Patent created successfully",
                    "success" => true
                ));
            }
            catch(Exception $e) {
                $LOG->error($e->getMessage());

                http_response_code(200);

                echo json_encode(array (
                    "message" => "Failed to create a patent",
                    "error" => $e,
                    "success" => false
                ));
            }
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