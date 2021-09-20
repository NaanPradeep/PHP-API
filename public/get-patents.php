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

include_once '../include/common.inc.php';

$database = new Database($LOG);
$db = $database->getConnection();

$patents = new Patents($db);
$patent_metas = new PatentMeta($db);

use \Firebase\JWT\JWT;

$jwt=isset($data["authToken"]) ? $data["authToken"] : "";

if($jwt){
 
    // if decode succeed
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));

        $patent_data = $patents->get_patents_all();
        $patent_meta_data = $patent_metas->get_patent_meta_all();

        if($patent_data !== null && $patent_meta_data !== null) {

            // $LOG->info("Profile updated for ".$user->email);
            
            http_response_code(200);

            echo json_encode(array (
                "patents" => $patent_data,
                "patent_metas" => $patent_meta_data,
                "success" => true
            ));
        } else {
            http_response_code(200);

            // $LOG->error("Failed to fetch the data");

            echo json_encode(array (
                "message" => _("Failed to fetch data"),
                "success" => false
            ));
        }
    }
    catch (Exception $e){
        // $LOG->error($e->getMessage());

        http_response_code(401);

        echo json_encode(array (
            "message" => "Access Denied",
            "error" => $e,
            "success" => false
        ));
    }

}

