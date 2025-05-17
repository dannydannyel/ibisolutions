<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

$id = $_SESSION['id'];

$idJob = $_GET['id'] ?? null;
if(!$idJob) {
    jsonResponse(100, "Invalid request data");
}

/* This is when request data comes from direct JSON entry
$input = file_get_contents("php://input");
$requestData = json_decode($input, true);
*/
// This is when request data comes from form
$requestData = $_POST;
if(!isset($requestData['adminComment']) || !isset($requestData['idEmp']) || !isset($requestData['checkInEmp']) || !isset($requestData['checkOutEmp'])) {
    jsonResponse(101, "Invalid request data");
}


// Obtiene datos de administrador
$admin = $db->getUserById($id);

$admin = $admin->fetch_assoc();
if($admin['role'] != 'employee' && $admin['role'] != 'admin') {
    jsonResponse(1,"Error: Datos de no administrador");
}

// Obtener datos de la tarea (job order)
$job = $db->getJobDataById($idJob);

$jobData = $job->fetch_all(MYSQLI_ASSOC)[0];

//print_r($jobData);
// Guardar comentario de administador
$jobData['comment'] = $requestData['adminComment'];
$jobData['check_in_employee'] = $requestData['checkInEmp'] ?? null;
$jobData['check_out_employee'] = $requestData['checkOutEmp'] ?? null;
$res = $db->updateJobOrder($jobData, $idJob);
if(!$res) {
    jsonResponse(3, "Error: Error actualizando trabajo");
}

jsonResponse(0, "Actualizado parte de trabajo " . $idJob);

function jsonResponse(int $code, mixed $message):string {
    header("Content-Type: application/json");
    die(json_encode(['code' => $code, 'message' => $message]));
}