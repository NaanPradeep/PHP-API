<?php
// required headers
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

// files needed to connect to database
include_once '../include/common.inc.php';
include_once '../config/database.php'; 
include_once '../controllers/user.php';
include_once '../controllers/user_meta.php';
 
// get database connection
$database = new Database($LOG);
$db = $database->getConnection();
 
// instantiate user object
$user = new User($db, $LOG);

$user_meta = new UserMeta($db, $LOG);

// get posted data
$data = json_decode(file_get_contents("php://input"), true);

 
// set product property values
$user->email = $data["email"];
$email_exists = $user->emailExists();
$is_verified_user = $user->is_verified_user();


// getting values for user language, time-zone and currency from database
$user_meta->user_id = (int)$user->id;
$locale = $user_meta->get_account_language();
$time_zone = $user_meta->get_account_time_zone();
$currency = $user_meta->get_account_currency();
$user_image = $user_meta->get_account_userimage();

$notiification_settings = $user_meta->get_notification_settings();
$min_bid_threshold = $user_meta->get_min_bid_threshold();

// generate json web token
use \Firebase\JWT\JWT;

$token = array(
    "iat" => $issued_at,
    "exp" => $expiration_time,
    // "iss" => $issuer,
    "data" => array(
        "id" => $user->id,
        "full_name" => $user->full_name,
        "username" => $user->username,
        "email" => $user->email
    )
 );

 $_user = array(
     "id" => $user->id,
     "avatar" => $user_image,
     "full_name" => $user->full_name,
     "username" => $user->username,
     "email" => $user->email,
 );

 $_account_preference = array(
     "locale" => $locale,
     "time_zone" => $time_zone,
     "currency" => $currency
 );

 $_notification_settings = array(
     "notificationSettings" => $notiification_settings,
     "minBidThreshold" => $min_bid_threshold
 );


// check if email exists and user is verified and if password is correct
if($email_exists && $is_verified_user === '1' && password_verify($data["password"], $user->password)){

    // generate jwt
    $jwt = JWT::encode($token, $key);

    $LOG->info($user->email.' successfully signed in');
 
    // set response code
    http_response_code(200);

    // sending login success
    echo json_encode(array(
            "message" => _gettext("Successful login."),
            "token" => $jwt,
            "user" => $_user,
            "account_preference" => $_account_preference,
            "notification_settings" => $_notification_settings,
            "is_verified" => true,
            "auth_success" => true,
        )
    );
}
// user not verified
else if($email_exists && $is_verified_user === '0' && password_verify($data["password"], $user->password)) {

    // generate jwt
    $jwt = JWT::encode($token, $key);

    $LOG->info($user->email.' successfully signed in');
 
    // set response code
    http_response_code(200);
    
    // tell the user login success but not verified
    echo json_encode(array(
            "message" => _gettext("Login successfull"),
            "token" => $jwt,
            "user" => $_user,
            "account_preference" => $_account_preference,
            "is_verified" => false,
            "auth_success" => true
        )
    );
} else if(!$email_exists) {

    $LOG->error("Login failed for ".$data["email"].". Email does not exists");

     // set response code
     http_response_code(200);
 
     // tell the user email not exists
     echo json_encode(array(
         "message" => _gettext('Your email does not exists'),
         "auth_success" => false
     ));
} else {
    $LOG->error("Login failed ".$data["email"].". Invalid Credentials");
    // set response code
    http_response_code(200);
 
    // tell the user login failed
    echo json_encode(array(
        "message" => _gettext("Invalid credentials"),
        "auth_success" => false
    ));
}
?>