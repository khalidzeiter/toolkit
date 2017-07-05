<?php

// Database Handler Class
class DBHandler {
    // Prepare Data Values
    protected function prepareValues(PDOStatement &$stmt) {
        foreach (static::$tableSchema as $col => $type) {
            $stmt->bindParam(":{$col}", $this->$col, $type);
        }
    }

    // Bind SQL Parameters
    private function bindSQLParameters() {
        $sqlParams = '';
        foreach (static::$tableSchema as $col => $type) {
            $sqlParams .= $col . ' = :' . $col . ', ';
        }
        return trim($sqlParams, ', ');
    }

    // Create (INSERT) Function
    public function create() {
        global $db;

        // Create (INSERT) SQL Statement
        $sql = 'INSERT INTO ' . static::$tableName . ' SET ' . self::bindSQLParameters();

        // Prepare & Execute SQL Query
        $stmt = $db->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    // Update (UPDATE) Function
    public function update() {
        global $db;

        // Update (UPDATE) SQL Statement
        $sql = 'UPDATE ' . static::$tableName . ' SET ' . self::bindSQLParameters() . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};

        // Prepare & Execute SQL Query
        $stmt = $db->prepare($sql);
        $this->prepareValues($stmt);
        return $stmt->execute();
    }

    // Delete (DELETE) Function
    public function delete() {
        global $db;

        // Delete (DELETE) SQL Statement
        $sql = 'DELETE FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' . $this->{static::$primaryKey};

        // Prepare & Execute SQL Query
        $stmt = $db->prepare($sql);
        return $stmt->execute();
    }

    /*
     * Read (SELECT) data from database
     * @return object
     * (Object on Success, False on Failure)
     */
    // Get (SELECT) All Data
    public function getAll() {
        global $db;

        // Get (SELECT) SQL Statement
        $sql = 'SELECT * FROM ' . static::$tableName;

        // Prepare & Execute SQL Query
        $stmt = $db->prepare($sql);
        if ($stmt->execute() === true) {
            return $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::$className, array_keys(static::$tableSchema));
        } else {
            return false;
        }
    }

    // Get (SELECT) Data By Primary Key
    public function getByPK($pk) {
        global $db;

        // Get (SELECT) Data By PK SQL Statement
        $sql = 'SELECT * FROM ' . static::$tableName . ' WHERE ' . static::$primaryKey . ' = ' . $pk;

        // Prepare & Execute SQL Query
        $stmt = $db->prepare($sql);
        if ($stmt->execute() === true) {
            $obj = $stmt->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, static::$className, array_keys(static::$tableSchema));
            return array_shift($obj);
        }
        return false;
    }
}

// Users Database Controller (Example)
class Users extends DBHandler {
    // Table Structure (Columns Names)
    private $id;
    private $name;
    private $username;
    private $email;
    private $bio;
    private $passwd;
    private $privileges;
    private $ban;

    protected static $primaryKey = 'id';    // Primary Key Name
    protected static $tableName = "users"; // Table Name
    public static $tableSchema = array(   // Table Schema & Data Type
        'id' => PDO::PARAM_INT,
        'name' => PDO::PARAM_STR,
        'username' => PDO::PARAM_STR,
        'email' => PDO::PARAM_STR,
        'bio' => PDO::PARAM_STR,
        'passwd' => PDO::PARAM_STR,
        'privileges' => PDO::PARAM_INT,
        'ban' => PDO::PARAM_INT
    );
    protected static $className = __CLASS__;

    public function __construct($id, $name, $username, $email, $bio, $passwd, $privileges, $ban) {
        $this->id = $id;
        $this->name = $name;
        $this->username = strtolower($username);
        $this->email = $email;
        $this->bio = $bio;
        $this->passwd = sha1($passwd);
        $this->privileges = $privileges;
        $this->ban = $ban;
    }

    // Get
    public function __get($prop) {
        return $this->$prop;
    }

    // Set
    public function __set($prop, $value) {
        $this->$prop = $value;
    }
}

// Initialization Of Database Control Object
$dsn = "mysql://hostname=localhost;dbname=logsys";  // Data Source Name
$user = "root";                                     // Database Username
$pass = "mysql";                                    // Database Password
$options = array(                                   // PDO Options
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8'
);
// Connect to Database
try {
    $db = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    echo $e->getMessage();
}
