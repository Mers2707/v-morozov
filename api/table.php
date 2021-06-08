<?php
header("Access-Control-Allow-Origin: http://v-morozov/#");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");
 
include_once 'config/database.php';
$database = new Database();
$db = $database->getConnection();
$stmt = $db->query("SELECT * FROM `users`");
$stmt->execute();
$result_array = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result_array);
?>