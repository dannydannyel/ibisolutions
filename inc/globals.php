<?php
class IbiSolution {
    public const VERSION = "0.3.01";
    public const LASTCHANGE = "20250517";
    public const DEFAULT_ROLE = "admin";
    public const DEFAULT_PASS = "Tawo07881!";
    public const ROLES = array('admin'=>"Admin", 'employer'=>"Dueño", 'employee'=>"Empleado");
}

/*
* Genera la constante public_url usando variables de servidor, detectando el protocolo (http o https), añade :// y luego el hostname (dominio), por ulitmo la parte de directorio, en caso de haber del recurso actual
* Por ejemplo, en local podria ser: HTTP_HOST seria localhost, PHP_SELF sería Proyecto1/index.php (al hacer dirname de esto se queda solo con Proyecto1)
*/


//TODO: Hacer esto bien
define("PUBLIC_URL", "http://www.ibisolutions.com/");
define("BASE_PATH", realpath(__DIR__ . "/../") . "/");

function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST'];
    $script = $_SERVER['SCRIPT_NAME'];
    
    // Remove script filename to get the base URL
    $path = rtrim(dirname($script), '/');

    return "$protocol://$host$path/";
}


function redirect(string $url) {
    header("Location:" . PUBLIC_URL . $url);
    die(); // Importante esta linea para que no siga ejecutando nada mas
}

function checkAuth() {
    @session_start();
    if(!isset($_SESSION['id'])) {  
        redirect("index.php?auth=0");
    }
}

function logout() {
    @session_start();
    session_destroy();
    redirect("index.php?auth=0");
}

function genUrl(string $endpoint) {
    return PUBLIC_URL . $endpoint;
}


/**
 * Alias of trim. Applies trim() to each array value
 */
function tr(array $data) {
    return array_map("trim", $data);
}

/**
 * Alias of date, generates the current date in international or spanish format
 */
function d(int $addDays = 0, bool $esFormat=false):string {
    $date = new DateTime();
    if($addDays>0) {
        $date->add(new DateInterval("P".$addDays."D"));
    }
    return ($esFormat) ? $date->format('d/m/Y') : $date->format('Y-m-d');
}

/**
 * Alias of format date: Triest to convert datetime formatted in ISO 8601 (yyyy-mm-ddThh:mm:ss) into mysqli format (yyyy-mm-dd hh:mm:ss)
 * 
 * @param string $dateFormat The date formatted as ISO 8601
 * @return string The formated date or the original in case not T detected
 */
function fd(string $dateFormat):string {
    if(str_contains($dateFormat, 'T')) {
        return str_replace('T', ' ', $dateFormat);
    }
    return $dateFormat;
}

function jsonErrorDie(string $message, int $errCode = 0) {
    header("Content-Type:application/json");
    die(json_encode(['error' => $errCode, 'message' => $message]));
}

function jsonFullCalendarResponse(array $events) {
    header("Content-Type:application/json");
    die(json_encode($events));
}

/**
 * Devuelve una fecha y tiempo de ahora en formato mysql listo para actualizar BBDD
 * 
 * @return string Fecha y hora validos de ahora en formato yyyy-mm-dd hh:mm:ss
 */
function getNowDt():string {
    return (new DateTime())->format('Y-m-d H:i:s');
}