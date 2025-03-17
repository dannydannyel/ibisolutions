<?php


class DataBase {
    private const HOST = "localhost";
    private const USER = "root";
    private const PWD = "";
    private const DATABASE = "ibisolutions";
    private MySQLi $conn;
    private string|null $lastError = null;
    private string|null $lastErrorDetail = null;
    private int|null $lastInsertId = null;

    public function __construct() {
        
        $this->conn = new mysqli(self::HOST, self::USER, self::PWD, self::DATABASE);
        if($this->conn->connect_error) {
            die("DATABASE connection error");
        }
    }

    public function getLastError() {
        return $this->lastError;
    }
    public function getLastErrorDetail() {
        return $this->lastErrorDetail;
    }
    public function getLastInsertId() {
        return $this->lastInsertId;
    }

    /**
     * Alias or protection
     * 
     * Protects against sql injection attacks using native mysqli real_escape_string method
     * 
     * @author Yanina
     * @param string value: The value to convert
     * @return string the value converted
     */
    public function pr(string $value) {
        return $this->conn->real_escape_string($value);
    }

    public function q(string $query, array $params=[]):false|mysqli_result {

        $res = $this->conn->execute_query($query, $params);
        if($res === false) {
            $this->lastError = $this->conn->errno;
            $this->lastErrorDetail = $this->conn->error;
            return false;
        }
        return $res;
    }

    /**
     * Ejecuta una query directa sin usar parámetros (debe estar protegida antes)
     * 
     * Devuelve true si era una query de lectura, false en caso de erro y un resultset si la query era SELECT y correcta
     */
    public function qdirect(string $query):bool|mysqli_result {

        $res = $this->conn->query($query);
        
        if($res === false) {
            $this->lastError = $this->conn->errno;
            $this->lastErrorDetail = $this->conn->error;
            return false;
        }
        if(str_starts_with(strtolower($query), "insert")) {
            $this->lastInsertId = $this->conn->insert_id;
        }
        return $res;
    }

    public function getUserList(string|null|array $filterRol=null):false|mysqli_result {
        if(is_null($filterRol)) {
            $whereClause = "WHERE 1";
        }
        else {
            
            if(is_array($filterRol)) {
                $role = array_map([$this, 'pr'], $filterRol);
                $role = array_map(function($r) {
                    return "'{$r}'";
                }, $role);
                $role = implode(",", $role);
                $whereClause = "WHERE role IN ({$role})";
            }
            else {
                $role = $this->pr($filterRol);
                $whereClause = "WHERE role='{$role}'";
            }
            
        }
        $query = "SELECT id, dni, name, surname, phone, role, email FROM users {$whereClause} ORDER BY name";
        
        $res = $this->q($query);
        return $res;
    }

    /**
     * Obtiene el listado de villas disponibles en formato mysqli_resultset
     * @return false|mysqli_result Error para falso o el resultset en caso de ok
     */
    public function getVillaList():false|mysqli_result {
        $query = "SELECT v.id, v.name, v.address, v.iduser, v.num_rooms, v.num_baths, v.pool, u.name as owner FROM users u JOIN villas v ON v.iduser=u.id ORDER BY v.name";
        $res = $this->q($query);
        return $res;
    }

    
    /**
     * Obtiene el listado de servicios disponibles en formato mysqli_resultset
     * @return false|mysqli_result Error para falso o el resultset en caso de ok
     */
    public function getServiceList():false|mysqli_result {
        $query = "SELECT s.id, s.name FROM services s ORDER BY s.name";
        $res = $this->q($query);
        return $res;
    }

    /**
     * Localiza un regsitro de usuario por su id
     */
    public function getUserById(int $id):false|mysqli_result {
        $query = "SELECT id, dni, name, surname, phone, role, email, address FROM users WHERE id=?";
        $res = $this->q($query, array($id));
        return $res;
    }

     /**
     * Localiza un regsitro de usuario por su id
     */
    public function getVillaById(int $id):false|mysqli_result {
        $query = "SELECT id, name, address, iduser, num_rooms, num_baths, pool FROM villas WHERE id=?";
        $res = $this->q($query, array($id));
        return $res;
    }

    /**
     * Actualiza la info de usuario basado en un array hash de parámetros
     * 
     * @return false|true error, or ok
     */
    public function updateUser(array $data, int $id):bool {


        $query = "UPDATE users SET dni=:dni, name=:name, surname=:surname, phone=:phone, role=:role, email=:email, address=:address WHERE id=:id";
        
        foreach($data as $field=>$value) {
            $value = $this->conn->real_escape_string($value);
            $query = str_replace(":" . $field, "'". $value."'", $query);
        }

        //echo $query;
        $res = $this->qdirect($query);
        return $res;
            
        
        
    }

    /**
     * Actualiza la info de villa basado en un array hash de parámetros
     * 
     * @return false|true error, or ok
     */
    public function updateVilla(array $data, int $id):bool {
        unset($data['id']);

        $query = "UPDATE villas SET name=:name, address=:address, iduser=:iduser, num_rooms=:num_rooms, num_baths=:num_baths, pool=:pool WHERE id=:id";
        
        foreach($data as $field=>$value) {
            $value = $this->conn->real_escape_string($value);
            $query = str_replace(":" . $field, "'". $value."'", $query);
        }
        $query = str_replace(":id", $id, $query);

        //echo $query;
        $res = $this->qdirect($query);
        return $res;
            
        
        
    }

    /**
     * Crea un nuevo usuario, recibiendo un array hash con sus valores
     * 
     * @return false|true error, or ok
     */
    public function insertUser(array $data):bool {


        $query = "INSERT INTO users SET dni=:dni, name=:name, surname=:surname, phone=:phone, role=:role, email=:email, address=:address, passwd=:passwd";
        
        foreach($data as $field=>$value) {
            $value = $this->conn->real_escape_string($value);
            $query = str_replace(":" . $field, "'". $value."'", $query);
        }


        //echo $query;
        $res = $this->qdirect($query);
        return $res;
            
        
        
    }
    /**
     * Crea una nueva villa, recibiendo un array hash con sus valores
     * 
     * @return false|true error, or ok
     */
    public function insertVilla(array $data):bool {


        $query = "INSERT INTO villas SET name=:name, address=:address, iduser=:iduser, num_rooms=:num_rooms, num_baths=:num_baths, pool=:pool";
        
        foreach($data as $field=>$value) {
            $value = $this->conn->real_escape_string($value);
            $query = str_replace(":" . $field, "'". $value."'", $query);
        }


        //echo $query;
        $res = $this->qdirect($query);
        return $res;
            
        
        
    }


    /**
     * Check a valid user login based on mail and password
     * 
     * @author Yanina
     * @param string mail
     * @param string passwd The plain password to check
     * 
     */
    public function checkUserLogin(string $mail, string $passwd):null|false|array {
        
        $query = "SELECT name, id, passwd, role FROM users WHERE email=?";
        try {
            $res = $this->conn->execute_query($query, [$mail]);
        }
        catch(mysqli_sql_exception $ex) {
            $this->lastError = "Error interno validando usuario";
            $this->lastErrorDetail = "Error en query {$query} en metodo checkUserLogin";
            return false;
        }
        if(!$res->num_rows) {
            $this->lastErrorDetail = "Email no encontrado para hacer login";
            return null;
        }
        $row = $res->fetch_assoc();
        
        $passwdEnc = $row['passwd'];
        if(!password_verify($passwd, $passwdEnc)) {
            $this->lastErrorDetail = "Password erroneo con el mail {$mail}";
            return null;
        }

        //TODO: Habria que comprobar password_needs_rehash()
        
        //TODO: Hay que comprobar los roles del usuario y enviarlos como retorno
        return $row;
        
    
    }
 }


// "select id from usuarios where password=?"; password=1234
 $db = new DataBase();
 return $db;