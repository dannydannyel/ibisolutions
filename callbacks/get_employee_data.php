<?php
require_once __DIR__ . "/../inc/globals.php";
$db = require_once BASE_PATH . "/inc/database.php";
checkAuth();

$id = $_GET['id'] ?? null;

if (!$id) {
  jsonErrorDie("Error: ID no especificado");
}
// Obtiene datos de empleado
$employee = $db->getUserById($id);

$employee = $employee->fetch_assoc();
if($employee['role'] != 'employee') {
    jsonErrorDie("Error: Datos de no empleado");
}

// Obtener trabajos del empleado
$jobs = $db->getEmployeeTasks($id);

$jobs = $jobs->fetch_all(MYSQLI_ASSOC);

// Devolver respuesta en JSON
echo json_encode([
  'employee' => $employee,
  'jobs' => $jobs
]);
