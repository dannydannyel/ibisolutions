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

    /**
     * Executes a query using params as plain array (hash array not accepted)
     * @param string $query
     * @param array $params Plain array to match each ? in query string
     * @return bool|mysqli_result False on error, que resultset when ok
     */
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
     * Executes query but accepts the :field format as hash array, not the ? method
     * @param string $query The query to execute with :field format
     * @param array $params A hash array with field=>value format
     * @return false|mysqli_result False on error, resultset on ok for select query, true on updatable query result.
     */
    public function qParams(string $query, array $params=[]):bool|mysqli_result {
      
       $query = $this->replaceQueryParams($query, $params);
        

        $res = $this->qdirect($query);
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


    public function getFullJobOrder(int $idEmployer):false|mysqli_result {
        $query = "SELECT u.name, u.surname, u.id as iduser, v.name as villa, v.id as idvilla, j.id as idjob, j.check_in, j.check_out, j.check_in_employee, j.check_out_employee FROM users u INNER JOIN job_orders j ON u.id=j.idemployee INNER JOIN villas v ON j.idvilla=v.id WHERE j.idemployer=:idEmployer";
        $res = $this->qParams($query, ['idEmployer' => $idEmployer]);
        return $res;
    }
    /**
     * Actualiza la info de usuario basado en un array hash de parámetros
     * 
     * @return false|true error, or ok
     */
    public function updateUser(array $data, int $id):bool {

        $data['id'] = $id;
        
        $query = "UPDATE users SET dni=:dni, name=:name, surname=:surname, phone=:phone, role=:role, email=:email, address=:address WHERE id=:id";
        
       
        $res = $this->qParams($query, $data);
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
     * Crea un nuevo parte de trabajo, recibiendo un array hash con los valores
     * 
     * @return false|true error, or ok
     */
    public function insertJobOrder(array $data):bool {


        $query = "INSERT INTO job_orders SET check_in=:check_in, check_out=:check_out, idemployer=:idemployer, idemployee=:idemployee, idvilla=:idvilla, idservice=:idservice, comment=:comment";
        
    
        //echo $query;
        $res = $this->qParams($query, $data);
        return $res;
            
        
    }
    public function checkUserEmailRep(string $mail, int $id=null):bool {
        $params = [
            'email' => $mail
        ];
        $query = "SELECT id FROM users WHERE email=:email";
        if(is_numeric($id)) {
            $params['id'] = $id;
            $query .= " AND id<>:id";
        }
        
        $res = $this->qParams($query, $params);
        
        if($res === false) {
            return false;
        }
        
        return $res->num_rows > 0;
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

    /**
     * Check for duplicated task for a given employer in a given villa, taken care that is inside the same check in and check out range
     * 
     * @param string $dateCheckIn
     * @param string $dateCheckOut
     * @param int $idEmployer
     * @param int $idVilla
     * @return bool
     */
    public function checkReasignedService(string $dateCheckIn, string $dateCheckOut, int $idEmployee, int $idVilla):bool|null {
        //example SELECT COUNT(*) as dup FROM job_orders WHERE (('2025-03-01 10:00' BETWEEN check_in AND check_out) OR ('2025-03-02 21:00' BETWEEN check_in AND check_out)) AND idemployee=4 AND idvilla=1;
        //SELECT COUNT(*) as n FROM job_orders WHERE ((check_in <= '2025-03-01 10:00' AND check_out > '2025-03-01 10:00') OR (check_in < '2025-03-01 21:00' AND check_out > '2025-03-01 21:00')) AND idemployee=4 AND idvilla=1;
        $query = "SELECT COUNT(*) as dup FROM job_orders WHERE ((? BETWEEN check_in AND check_out) OR (? BETWEEN check_in AND check_out)) AND idemployee=? AND idvilla=?";
        
        try {
            //echo $dateCheckIn, " ", $dateCheckOut, " ", $idEmployee, " ", $idVilla;
            $res = $this->conn->execute_query($query, [$dateCheckIn, $dateCheckOut, $idEmployee, $idVilla]);
        }
        catch(mysqli_sql_exception $ex) {
            $this->lastError = "Error interno revisando reasignación duplicada en villa";
            $this->lastErrorDetail = "Error en query {$query} en metodo checkUserLogin";
            return null;
        }
        if(!$res->num_rows) {
            $this->lastErrorDetail = "Sutuación errónea imposible (singularidad sql)";
            return null;
        }
        $row = $res->fetch_assoc();
        return ($row['dup']>0);
    }

    // Función para reemplazar los valores en la query de manera segura
    private function replaceQueryParams($query, $params) {
        $mysqli = $this->conn;
        return preg_replace_callback(
            '/:\b([a-zA-Z_][a-zA-Z0-9_]*)\b/', // Busca parámetros que inician con ":" y son palabras completas
            function ($matches) use ($params, $mysqli) {
                $key = $matches[1]; // Extraemos el nombre del parámetro sin ":"
                if (!array_key_exists($key, $params)) {
                    return $matches[0]; // Si no existe en los parámetros, lo dejamos igual
                }
                $value = $params[$key];

                // Si el valor es numérico, lo dejamos tal cual; si es string, lo escapamos y lo ponemos entre comillas
                return is_numeric($value) ? $value : "'" . $mysqli->real_escape_string($value) . "'";
            },
            $query
        );
    }
 }


// "select id from usuarios where password=?"; password=1234
 $db = new DataBase();
 return $db;