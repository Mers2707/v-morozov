<?php
header("Access-Control-Allow-Origin: http://v-morozov/#");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");
 
include_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();
$variable = $_POST['variable'];
foreach($variable as $value){
    $stmt = $db->prepare("DELETE FROM `users` WHERE `users`.`id` = ? ");
    $stmt->bindParam(1, $value);
    $stmt->execute();
}
?>