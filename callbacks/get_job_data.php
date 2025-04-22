<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
  jsonErrorDie("Error: ID no especificado");
}

// Obtiene datos del trabajo solicitado
$job = $db->getJobDataById($id);
$job = $job->fetch_assoc();

$idEmployee = $job['iduser'];
// Obtiene datos de empleado
$employee = $db->getUserById($idEmployee);

$employee = $employee->fetch_assoc();
if($employee['role'] != 'employee') {
    jsonErrorDie("Error: Datos de no empleado");
}

// Devolver respuesta en JSON
echo json_encode([
  'employee' => $employee,
  'job' => $job
]);
