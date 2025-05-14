<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

$id = $_SESSION['id'];

$idJob = $_GET['id'] ?? null;
if(!$idJob) {
    jsonResponse(100, "Invalid request data");
}

$input = file_get_contents("php://input");
$requestData = json_decode($input, true);

// Obtiene datos de empleado
$employee = $db->getUserById($id);

$employee = $employee->fetch_assoc();
if($employee['role'] != 'employee') {
    jsonResponse(1,"Error: Datos de no empleado");
}

// Obtener datos de la tarea (job order)
$job = $db->getJobDataById($idJob);

$jobData = $job->fetch_all(MYSQLI_ASSOC)[0];

//print_r($jobData);
// Para guardar el comentario, la tarea no puede estar finalizada del todo
if(!empty($jobData['check_in_employee']) && !empty($jobData['check_out_employee'])) {
    jsonResponse(2, "Error: Fichajes realizados con anterioridad");
}

if(empty($jobData['check_in_employee'])) {
    //Es un fichaje de entrada
    $jobData['check_in_employee'] = getNowDt();
    $type = 'in';
    $message = "Fichaje realizado con éxito, puedes cerrar la ventanita y volver a entrar en el parte más tarde si tienes que realizar la salida";
}
else {
    //Es un fichaje de salida
    $jobData['check_out_employee'] = getNowDt();
    $type = 'out';
    $message = "Fichaje de salida realizado con éxito. Tarea marcada como completada";
}
//print_r($jobData);
$res = $db->updateJobOrder($jobData, $idJob);
//$res = true;

if(!$res) {
    jsonResponse(3, "Error: Error actualizando fichaje");
}

jsonResponse(0, [$type, $message]);

function jsonResponse(int $code, mixed $message):string {
    header("Content-Type: application/json");
    die(json_encode(['code' => $code, 'message' => $message]));
}