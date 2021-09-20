<?php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';
include_once '../controllers/patents.php';
include_once '../include/common.inc.php';

$data = json_decode(file_get_contents("php://input"), true);

$database = new Database($LOG);
$db = $database->getConnection();

$patents = new Patents($db);
$patents->id = $data["patent_id"];

use \Firebase\JWT\JWT;

$jwt=isset($data["authToken"]) ? $data["authToken"] : "";

if($jwt){
    // if decode succeed
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $patent_data = $patents->get_patent_by_id();
        if($patent_data) {
            if($patent_data["user_id"] === $data["userID"]) {
                $deleted = $patents->delete_patent();
                if($deleted) {
                    http_response_code(200);

                    echo json_encode(array (
                        "message" => "Patent deleted successfully",
                        "success" => true
                    ));
                } else {
                    http_response_code(200);

                    echo json_encode(array (
                        "message" => "Failed to delete the patent. Please try again later.",
                        "success" => false
                    ));
                }
            } else{
                http_response_code(200);

                echo json_encode(array (
                    "message" => "Invalid request.",
                    "success" => false
                ));
            }
        } else {
            http_response_code(200);

            echo json_encode(array (
                "message" => "Failed to delete the patent. Please try again later.",
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