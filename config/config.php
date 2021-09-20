<?php

$IM_PATH = getenv('IM_PATH');

$CONFIG_JSON_PATH = getenv('IM_PATH') . '\config.json';
$CONFIG_XML_PATH = getenv('IM_PATH') . '\config.xml';


$config = json_decode(file_get_contents(''.$CONFIG_JSON_PATH, true));

/*---------DB Server paths--------*/
defined('DB_HOST') || define('DB_HOST',$config->DB_CREDENTIALS->DB_HOST);
defined('DB_NAME') || define('DB_NAME',$config->DB_CREDENTIALS->DB_NAME);
defined('DB_USER') || define('DB_USER',$config->DB_CREDENTIALS->DB_USER);
defined('DB_PASSWORD') || define('DB_PASSWORD',$config->DB_CREDENTIALS->DB_PASSWORD);

/*-----------Project file paths-----------*/
defined('MAIN_PATH') || define('MAIN_PATH', $_SERVER['DOCUMENT_ROOT'].$config->APPLICATION_PATH->MAIN_PATH);

/*------------Mail Server Credentials------------*/
defined('MAIL_HOST') || define('MAIL_HOST', $config->MAIL_SERVER_CREDENTIALS->MAIL_HOST);
defined('MAIL_USER') || define('MAIL_USER', $config->MAIL_SERVER_CREDENTIALS->MAIL_USER);
defined('MAIL_PASSWORD') || define('MAIL_PASSWORD', $config->MAIL_SERVER_CREDENTIALS->MAIL_PASSWORD);
defined('MAIL_PORT') || define('MAIL_PORT', $config->MAIL_SERVER_CREDENTIALS->MAIL_PORT);
defined('MAIL_SMTP_AUTH') || define('MAIL_SMTP_AUTH', $config->MAIL_SERVER_CREDENTIALS->MAIL_SMTP_AUTH);
defined('FROM_MAIL_ADDRESS') || define('FROM_MAIL_ADDRESS', $config->MAIL_SERVER_CREDENTIALS->FROM_MAIL_ADDRESS);
defined('FROM_MAIL_NAME') || define('FROM_MAIL_NAME', $config->MAIL_SERVER_CREDENTIALS->FROM_MAIL_NAME);

/*--------------JSON Web Token Credentials----------------  */
defined('KEY') || define('KEY', $config->JWT_TOKEN_CREDENTIALS->KEY);
defined('TIME_ZONE') || define('TIME_ZONE', $config->JWT_TOKEN_CREDENTIALS->TIME_ZONE);
defined('APPROVED_URL') || define('APPROVED_URL', $config->JWT_TOKEN_CREDENTIALS->APPROVED_URL);


/*------------Mail Server Settings-----------*/
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
 
//Load Composer's autoloader
require (MAIN_PATH.'vendor/autoload.php');

//Instantiation and passing `true` enables exceptions
$mail = new PHPMailer(true);

//Server settings begin
$mail->SMTPDebug = 0;                      //Enable verbose debug output
$mail->isSMTP();                                            //Send using SMTP
$mail->Host       = MAIL_HOST;                     //Set the SMTP server to send through
$mail->SMTPAuth   = MAIL_SMTP_AUTH;                                   //Enable SMTP authentication
$mail->Username   = MAIL_USER;                     //SMTP username
$mail->Password   = MAIL_PASSWORD;                               //SMTP password
$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
$mail->Port       = MAIL_PORT;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above
//Server settings end


// JSON web token
// show error reporting
error_reporting(E_ALL);
 
// set your default time-zone
date_default_timezone_set(TIME_ZONE);
 
// variables used for jwt
$key = KEY; // signature for JWT
$issuer = APPROVED_URL; // issuer of the token
$issued_at = time();
$expiration_time = $issued_at + (60 * 60); // valid for 1 hour

?>