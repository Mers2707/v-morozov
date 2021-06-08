<?php
header("Access-Control-Allow-Origin: http://v-morozov/#");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
include_once 'config/database.php';
include_once 'objects/user.php';
 
$database = new Database();
$db = $database->getConnection();
 
$user = new User($db);
$data = json_decode(file_get_contents("php://input"));
 
$user->user = $data->user;
$user->email = $data->email;
$user->password = $data->password;
$email_exists = $user->emailExists();
$user_exists = $user->userExists();

if (!$email_exists && !$user_exists) {
    if (
        !empty($user->user) &&
        !empty($user->email) &&
        !empty($user->password) &&
        $user->create()
    ) {
        http_response_code(200);
        echo json_encode(array("message" => "Successfully user create."));
    }
    else {
        http_response_code(400);
        echo json_encode(array("message" => "Can't create user."));
    }
} 
else{
        http_response_code(400);
        echo json_encode(array("message" => "Can't create user."));
}
?>