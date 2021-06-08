<?php
header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/database.php';
include_once 'config/core.php';
include_once 'libs/php-jwt-master/src/BeforeValidException.php';
include_once 'libs/php-jwt-master/src/ExpiredException.php';
include_once 'libs/php-jwt-master/src/SignatureInvalidException.php';
include_once 'libs/php-jwt-master/src/JWT.php';
use \Firebase\JWT\JWT;
 
$data = json_decode(file_get_contents("php://input"));

$jwt=isset($data->jwt) ? $data->jwt : "";

if($jwt) { 
    try {
        $decoded = JWT::decode($jwt, $key, array('HS256'));
        $idUser = (array($decoded->data)[0]);
        
        $database = new Database();
        $db = $database->getConnection();
        $stmt = $db->prepare("SELECT * FROM `users` WHERE `users`.`id` = ? AND `users`.`block`=0");
        $stmt->bindParam(1, ($idUser->id));
        $stmt->execute();
        $num = $stmt->rowCount();
        if($num>0) {
            http_response_code(200);
            echo json_encode(array(
                "message" => "Logged.",
                "data" => $decoded->data
            ));
        } else {
            http_response_code(401);
            echo json_encode(array("message" => "Access denied."));           
        }
    }

    catch (Exception $e){
        http_response_code(401);
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}

else{
    http_response_code(401);
    echo json_encode(array("message" => "Access denied."));
}
?>