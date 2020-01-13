<?php
/** Red Framework
 * Model Class - Handle All Data Sets
 * ORM
 * @author REDCODER
 * http://redframework.ir
 */

namespace Red\Base;

use Exception;
use Red\EnvironmentProvider\Environment;
use Red\Red;
use Red\ValidateService\Validate;
use Red\Output\Output;


// Flags For Query Method (CRUD)
define('CREATE', 'CREATE');
define('READ', 'READ');
define('UPDATE', 'UPDATE');
define('DELETE', 'DELETE');

// Flags For Query Method (Conditional RUD)
define('READ_CONDITION', 'READ_CONDITION');
define('UPDATE_CONDITION', 'UPDATE_CONDITION');
define('DELETE_CONDITION', 'DELETE_CONDITION');


// Free Query Flag
define('FREE', 'FREE');
define('COUNT', 'COUNT');
define('COUNT_CONDITION', 'COUNT_CONDITION');
define('AVG', 'AVG');
define('AVG_CONDITION', 'AVG_CONDITION');

define('DB1', 'DATABASE_1');
define('DB2', 'DATABASE_2');
define('DB3', 'DATABASE_3');


/**
 * Class Model
 * @package App\Red
 */
class Model
{


    /**
     * @var $db_1_connection Database
     */

    protected $db_1_connection;

    /**
     * @var $db_2_connection Database
     */

    protected $db_2_connection;

    /**
     * @var $db_3_connection Database
     */

    protected $db_3_connection;

    protected $fields;
    protected $parameters;
    protected $condition_parameters;
    public static $condition_fields;

    protected $db_1_content;
    protected $db_2_content;
    protected $db_3_content;

    protected $db_1_pages_count;
    protected $db_2_pages_count;
    protected $db_3_pages_count;

    protected static $db_1_query_history = array();
    protected static $db_2_query_history = array();
    protected static $db_3_query_history = array();

    /**
     * Model Constructor
     * Initializing DB
     * Run Migration If Necessary
     */
    public function __construct()
    {

        if (Environment::get('DATABASE_1', 'Status') == 'off' && Environment::get('DATABASE_2', 'Status') == 'off' && Environment::get('DATABASE_3', 'Status') == 'off') {

            if (Environment::get('DEBUG', 'Errors') == 'on') {
                http_response_code(500);

                $error_no = "Add Your Database !";
                $error_message = "No Database is Connected";
                Output::printC($error_no);
                Output::printC($error_message);
                exit();
            } else {
                http_response_code(500);
                Output::printC("Sorry ! Unexpected Error Occurred");
                exit();
            }
        }


        if (Environment::get('DATABASE_1', 'Status') == 'on') {
            $this->initDb('DATABASE_1');
        }

        if (Environment::get('DATABASE_2', 'Status') == 'on') {
            $this->initDb('DATABASE_2');
        }

        if (Environment::get('DATABASE_3', 'Status') == 'on') {
            $this->initDb('DATABASE_3');
        }

    }

    /**
     * Model Destructor
     * Closing DB Connection
     */
    public function __destruct()
    {
        $this->db_1_connection = NULL;
        $this->db_2_connection = NULL;
        $this->db_3_connection = NULL;
    }


    /**
     * Setting PDO Object Into A Variable
     * @param $database
     * @return mixed
     */
    protected function db($database)
    {
        if ($database == 'DATABASE_1') {
            return $this->db_1_connection;
        } else if ($database == 'DATABASE_2') {
            return $this->db_2_connection;
        } else if ($database == 'DATABASE_3') {
            return $this->db_3_connection;
        }

        return FALSE;
    }


    /**
     * Initialize Databases
     * @param $database
     */
    protected function initDb($database)
    {

        try {
            $config = Environment::get($database);
        } catch (Exception $error) {
            http_response_code(500);
            echo $error->getMessage();
            exit();
        }

        if ($config['Driver'] === "mysql") {

            try {
                $dsn = $config['Driver'] . ":host=" . $config['Host'] . ";dbname=" . $config['Name'] . ";charset=" . $config['Charset'];
                if ($database == 'DATABASE_1') {
                    $this->db_1_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_1_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else if ($database == 'DATABASE_2') {
                    $this->db_2_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_2_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else if ($database == 'DATABASE_3') {
                    $this->db_3_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_3_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }

            } catch (\PDOException $error) {


                if (Environment::get('DEBUG', 'Errors') == 'on') {
                    http_response_code(500);
                    $error_no = 'MYSQL Database Connection Failed';
                    $error_message = $error;
                    Output::printC($error_no);
                    Output::printC($error_message);
                    exit();
                } else {
                    http_response_code(500);
                    Output::printC("Sorry ! Unexpected Error Occurred");
                    exit();
                }
            }

            if ($config['Backup'] === "on") {

                if (!is_dir(DB_BACKUP_PATH . $config['Name'])) {
                    mkdir(DB_BACKUP_PATH . $config['Name']);
                }

                $backup_path = DB_BACKUP_PATH . $config['Name'] . DS;
                $files_list = scandir($backup_path);
                $backup_exist = FALSE;

                $pattern = '/(' . $config["Name"] . '_' . date("Y-m-d") . ')/';

                foreach ($files_list as $file) {
                    if (preg_match($pattern, $file)) {
                        $backup_exist = TRUE;
                    }
                }

                if ($config['BackupTime'] == 'anytime') {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".sql";
                        $command = MYSQL_DUMP_PATH . " --opt --user=" . $config['User'] . " --password=\"" . $config['Password'] . "\" --host=" . $config['Host'] . " --port=" . $config['Port'] . " " . $config['Name'] . " > " . $backup_path . $backup_name;
                        exec($command);
                    }
                }


                if ($config['BackupTime'] == date("H")) {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".sql";
                        $command = MYSQL_DUMP_PATH . " --opt --user=" . $config['User'] . " --password=\"" . $config['Password'] . "\" --host=" . $config['Host'] . " --port=" . $config['Port'] . " " . $config['Name'] . " > " . $backup_path . $backup_name;
                        exec($command);
                    }

                }
            }

            if ($config['Migration'] == 'on') {
                if ($database == 'DATABASE_1') {
                    $migration_file = DB1_MIGRATION_PATH;
                } else if ($database == 'DATABASE_2') {
                    $migration_file = DB2_MIGRATION_PATH;
                } else if ($database == 'DATABASE_3') {
                    $migration_file = DB3_MIGRATION_PATH;
                }

                $server_name = $config['Host'];
                $port = $config['Port'];
                $username = $config['User'];
                $password = $config['Password'];
                $database_name = $config['Name'];

                $migration_command = MYSQL_PATH . " --host={$server_name} --port={$port} --user={$username} --password={$password} {$database_name} < $migration_file";
                exec($migration_command);
            }

        } else if ($config['Driver'] == 'sqlsrv') {
            try {
                $dsn = 'sqlsrv:Server=' . $config['Host'] . ";Database=" . $config['Name'];


                if ($database == 'DATABASE_1') {
                    $this->db_1_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_1_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    if ($config['Charset'] == 'utf-8') {
                        $this->db_1_connection->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
                    }
                } else if ($database == 'DATABASE_2') {
                    $this->db_2_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_2_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    if ($config['Charset'] == 'utf-8') {
                        $this->db_2_connection->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
                    }
                } else if ($database == 'DATABASE_3') {
                    $this->db_3_connection = new Database($dsn, $config['User'], $config['Password']);
                    $this->db_3_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    if ($config['Charset'] == 'utf-8') {
                        $this->db_3_connection->setAttribute(\PDO::SQLSRV_ATTR_ENCODING, \PDO::SQLSRV_ENCODING_UTF8);
                    }
                }



            } catch (\PDOException $error) {

                if (Environment::get('DEBUG', 'Errors') == 'on') {
                    http_response_code(500);
                    $error_no = 'MSSQL Database Connection Failed';
                    $error_message = $error;
                    Output::printC($error_no);
                    Output::printC($error_message);
                    exit();
                } else {
                    http_response_code(500);
                    Output::printC("Sorry ! Unexpected Error Occurred");
                    exit();
                }
            }

            if ($config['Backup'] === "on") {

                if (!is_dir(DB_BACKUP_PATH . $config['Name'])) {
                    mkdir(DB_BACKUP_PATH . $config['Name']);
                }

                $backup_path = DB_BACKUP_PATH . $config['Name'] . DS;
                $files_list = scandir($backup_path);
                $backup_exist = FALSE;

                $pattern = '/(' . $config["Name"] . '_' . date("Y-m-d") . ')/';

                foreach ($files_list as $file) {
                    if (preg_match($pattern, $file)) {
                        $backup_exist = TRUE;
                    }
                }

                if ($config['BackupTime'] == 'anytime') {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".sql";
                        $command = SQL_CMD_PATH . " -S " . $config['Host'] . " -U " . $config['User'] . " -P " . $config['Password'] . " –Q \"BACKUP DATABASE [" . $config['Name'] . "] TO DISK='" . $backup_path . $backup_name . "'\"";
                        exec($command);
                    }
                }


                if ($config['BackupTime'] == date("H")) {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".sql";
                        $command = SQL_CMD_PATH . " -S " . $config['Host'] . " -U " . $config['User'] . " -P " . $config['Password'] . " –Q \"BACKUP DATABASE [" . $config['Name'] . "] TO DISK='" . $backup_path . $backup_name . "'\"";
                        exec($command);
                    }

                }
            }


            if ($config['Migration'] == 'on') {

                if ($database == 'DATABASE_1') {
                    $migration_file = DB1_MIGRATION_PATH;
                } else if ($database == 'DATABASE_2') {
                    $migration_file = DB2_MIGRATION_PATH;
                } else if ($database == 'DATABASE_3') {
                    $migration_file = DB3_MIGRATION_PATH;
                }

                $migration_command = SQL_CMD_PATH . " -S " . $config['Host'] . " -U " . $config['User'] . " -P " . $config['Password'] . " –Q \"RESTORE DATABASE [" . $config['Name'] . "] FROM DISK='" . $migration_file . "'\"";
                exec($migration_command);
            }

        } else if ($config['Driver'] == "sqlite") {

            if (!extension_loaded('pdo_sqlite')) {

                if (Environment::get('DEBUG', 'Errors') == 'on') {
                    http_response_code(500);

                    $error_no = 'SQLite Driver Error';
                    $error_message = "SQLite Extension 'pdo_sqlite' Not Found";
                    Output::printC($error_no);
                    Output::printC($error_message);
                    exit();
                } else {
                    http_response_code(500);
                    Output::printC("Sorry ! Unexpected Error Occurred");
                    exit();
                }
            }

            try {

                // Creating DB Connection With SQLite3 Extension; Decided to use PDO_SQLite at the End.

                /*if ($database == 'DATABASE_1') {
                    $this->db_1_connection = new \SQLite3(ROOT_PATH . "database" . DS . "SQLite" . DS . $config['Name'] . '.db');;
                } else if ($database == 'DATABASE_2') {
                    $this->db_2_connection = new \SQLite3(ROOT_PATH . "database" . DS . "SQLite" . DS . $config['Name'] . '.db');;
                } else if ($database == 'DATABASE_3') {
                    $this->db_3_connection = new \SQLite3(ROOT_PATH . "database" . DS . "SQLite" . DS . $config['Name'] . '.db');;
                }*/


                $dsn = 'sqlite:' . ROOT_PATH . "database" . DS . "SQLite" . DS . $config['Name'] . '.db';


                if ($database == 'DATABASE_1') {
                    $this->db_1_connection = new Database($dsn);
                    $this->db_1_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else if ($database == 'DATABASE_2') {
                    $this->db_2_connection = new Database($dsn);
                    $this->db_2_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                } else if ($database == 'DATABASE_3') {
                    $this->db_3_connection = new Database($dsn);
                    $this->db_3_connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                }



            } catch (\PDOException $error) {

                if (Environment::get('DEBUG', 'Errors') == 'on') {
                    http_response_code(500);

                    $error_no = 'SQLite Database Connection Failed';
                    $error_message = $error;
                    Output::printC($error_no);
                    Output::printC($error_message);
                    exit();
                } else {
                    http_response_code(500);
                    Output::printC("Sorry ! Unexpected Error Occurred");
                    exit();
                }
            }

            if ($config['Backup'] === "on") {

                if (!is_dir(DB_BACKUP_PATH . $config['Name'])) {
                    mkdir(DB_BACKUP_PATH . $config['Name']);
                }

                $backup_path = DB_BACKUP_PATH . $config['Name'] . DS;
                $files_list = scandir($backup_path);
                $backup_exist = FALSE;

                $pattern = '/(' . $config["Name"] . '_' . date("Y-m-d") . ')/';

                foreach ($files_list as $file) {
                    if (preg_match($pattern, $file)) {
                        $backup_exist = TRUE;
                    }
                }

                if ($config['BackupTime'] == 'anytime') {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".db";
                        $command = SQLITE_CLI_PATH . " " . $config['Name'] . ".db" . " \".backup " . $backup_path . $backup_name . "\"";
                        exec($command);
                    }
                }


                if ($config['BackupTime'] == date("H")) {

                    if ($backup_exist === FALSE) {
                        $backup_name = $config['Name'] . "_" . date("Y-m-d") . "_" . date("h-i-s") . ".db";
                        $command = SQLITE_CLI_PATH . " " . $config['Name'] . ".db" . " \".backup " . $backup_path . $backup_name . "\"";
                        exec($command);
                    }

                }
            }


            if ($config['Migration'] == 'on') {

                if ($database == 'DATABASE_1') {
                    $migration_file = DB1_MIGRATION_PATH;
                } else if ($database == 'DATABASE_2') {
                    $migration_file = DB2_MIGRATION_PATH;
                } else if ($database == 'DATABASE_3') {
                    $migration_file = DB3_MIGRATION_PATH;
                }

                $migration_command = SQLITE_CLI_PATH . " " . $config['Name'] . ".db" . " \".restore " . $migration_file . "\"";
                exec($migration_command);
            }

        } else {
            if (Environment::get('DEBUG', 'Errors') == 'on') {
                http_response_code(500);
                $error_no = $database . ' Connection Failed';
                $error_message = 'Driver Not Found';
                Output::printC($error_no);
                Output::printC($error_message);
                exit();
            } else {
                http_response_code(500);
                Output::printC("Sorry ! Unexpected Error Occurred");
                exit();
            }
        }
    }

    /**
     * Handling Errors
     */
    public function displayErrors()
    {
        http_response_code(403);

        $errors = Red::getErrors();
        foreach ($errors as $value) {
            echo $value . "<br/>";
        }
    }

    /**
     * Set Query Fields
     * @param array $fields
     * @return $this
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Set Query Parameters
     * @param array $parameters
     * @return $this
     */
    public function setParameters($parameters)
    {
        foreach ($parameters as $key => $value) {
            if (array_key_exists($key, $this->fields)) {
                if (Validate::modelValidate($key, $value, $this->fields[$key]) === TRUE) {
                    $this->parameters[$key] = $value;
                }
            }
        }
        return $this;
    }


    /**
     * Set Query Condition Fields
     * @param array $fields
     * @return $this
     */
    public function setConditionFields($fields)
    {
        self::$condition_fields = $fields;
        return $this;
    }


    /**
     * Set Query Condition Parameters
     * @param array $parameters
     * @return $this
     */
    public function setConditionParams($parameters)
    {
        foreach ($parameters as $key => $value) {
            if (array_key_exists($key, self::$condition_fields)) {
                if (Validate::modelValidate($key, $value, self::$condition_fields[$key]) === TRUE) {

                    $this->condition_parameters[$key] = $value;
                }
            }
        }
        return $this;
    }

    /**
     * @param $table
     * @param $method
     * @param string $database
     * @param null $order
     * @param null $offset
     * @param null $limit
     * @return bool
     */
    public function query($table, $method, $database = DB1, $order = NULL, $offset = NULL, $limit = NULL)
    {


        if (count(Red::getErrors()) === 0) {

            if (Environment::get($database,'Driver') == 'mysql') {


                $table = explode(',', $table);

                $table = querySyntax($table, 'backTicket');

                $table = implode(',', $table);

                $table = str_replace(' ', '', $table);


                if ($method === CREATE) {


                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    $parameters = implode(', ', querySyntax(array_keys($this->fields), 'qMark'));

                    $data_set = array_values($this->parameters);


                    $query = "INSERT INTO $table ($fields) VALUES ($parameters)";


                    try {

                        $this->db($database)->prepare($query);

                        $this->db($database)->prepare($query)->execute($data_set);

                        ob_start();

                        $this->db($database)->prepare($query)->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                } else if ($method === READ) {


                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order limit $offset, $limit";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table";
                    }


                    try {
                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }

                        $this->fields = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table";

                        try {

                            $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                            $query_handler->execute();

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];


                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }


                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                } else if ($method === READ_CONDITION) {

                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order limit $offset, $limit";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table WHERE $conditions";
                    }

                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }


                        $this->fields = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table WHERE $conditions";

                        $condition_parameters = array_values($this->condition_parameters);


                        try {

                            $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                            $query_handler->execute($condition_parameters);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];


                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }


                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                    self::$condition_fields = NULL;
                    $this->condition_parameters = NULL;


                } else if ($method === UPDATE) {


                    $update_fields = implode(', ', querySyntax(array_keys($this->fields), 'update'));

                    $query = "UPDATE $table SET $update_fields";

                    try {
                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }
                } else if ($method === UPDATE_CONDITION) {


                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'update'));


                    $condition_fields = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "UPDATE $table SET $fields WHERE " . $condition_fields;


                    $parameters = array_values($this->parameters);
                    $condition_parameters = array_values($this->condition_parameters);

                    $parameters = array_merge($parameters, $condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        }

                    }
                } else if ($method === DELETE) {

                    $query = "DELETE FROM $table";
                    try {

                        $try = $this->db($database)->prepare($query);

                        $try->execute();

                        ob_start();

                        $try->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                } else if ($method === DELETE_CONDITION) {

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));

                    $query = "DELETE FROM $table WHERE " . $conditions;
                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->condition_parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);

                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }


                } else if ($method === COUNT) {

                    $query = "SELECT count(*) As Count FROM $table";


                    try {
                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($this->parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }


                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                } else if ($method === COUNT_CONDITION) {


                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "SELECT count(*) As Count FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                } else if ($method === AVG) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg'));

                    $query = "SELECT $fields FROM $table";


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }


                        $this->fields = NULL;
                        $this->parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                } else if ($method === AVG_CONDITION) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg'));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "SELECT $fields FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                } else {
                    echo 'Error Occured';
                    http_response_code(500);
                    exit();
                }
            }
            else if (Environment::get($database, 'Driver') == 'sqlsrv') {


                if ($method === CREATE) {


                    $fields = implode(', ', array_keys($this->fields));

                    $parameters = implode(', ', querySyntax(array_keys($this->fields), 'qMark'));

                    $data_set = array_values($this->parameters);


                    $query = "INSERT INTO $table ($fields) VALUES ($parameters)";


                    try {

                        $this->db($database)->prepare($query);

                        $this->db($database)->prepare($query)->execute($data_set);

                        ob_start();

                        $this->db($database)->prepare($query)->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                } else if ($method === READ) {


                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', array_keys($this->fields));

                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table";
                    }


                    try {
                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }


                        $this->fields = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table";

                        try {

                            $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                            $query_handler->execute();

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];

                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }

                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                } else if ($method === READ_CONDITION) {

                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', array_keys($this->fields));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition_no_back_ticket'));


                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order OFFSET $offset ROWS FETCH NEXT $limit ROWS ONLY";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table WHERE $conditions";
                    }


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();


                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }


                        $this->fields = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table WHERE $conditions";

                        $condition_parameters = array_values($this->condition_parameters);


                        try {

                            $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                            $query_handler->execute($condition_parameters);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];


                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }


                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                    self::$condition_fields = NULL;
                    $this->condition_parameters = NULL;


                } else if ($method === UPDATE) {


                    $update_fields = implode(', ', querySyntax(array_keys($this->fields), 'update_no_back_ticket'));

                    $query = "UPDATE $table SET $update_fields";

                    try {
                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }
                } else if ($method === UPDATE_CONDITION) {


                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'update_no_back_ticket'));


                    $condition_fields = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition_no_back_ticket'));


                    $query = "UPDATE $table SET $fields WHERE " . $condition_fields;


                    $parameters = array_values($this->parameters);
                    $condition_parameters = array_values($this->condition_parameters);

                    $parameters = array_merge($parameters, $condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        }

                    }
                } else if ($method === DELETE) {

                    $query = "DELETE FROM $table";
                    try {

                        $try = $this->db($database)->prepare($query);

                        $try->execute();

                        ob_start();

                        $try->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                } else if ($method === DELETE_CONDITION) {

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition_no_back_ticket'));

                    $query = "DELETE FROM $table WHERE " . $conditions;
                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->condition_parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);

                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }


                } else if ($method === COUNT) {

                    $query = "SELECT count(*) As Count FROM $table";


                    try {
                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                } else if ($method === COUNT_CONDITION) {


                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition_no_back_ticket'));


                    $query = "SELECT count(*) As Count FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                } else if ($method === AVG) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg_no_back_ticket'));

                    $query = "SELECT $fields FROM $table";


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }


                        $this->fields = NULL;
                        $this->parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                } else if ($method === AVG_CONDITION) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg_no_back_ticket'));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition_no_back_ticket'));


                    $query = "SELECT $fields FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                } else {
                    if (Environment::get('DEBUG', 'Errors') == 'on') {
                        http_response_code(500);
                        $error_no = "Database Query Error !";
                        Output::printC($error_no);
                        exit();
                    } else {
                        http_response_code(500);
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }
                }
            }
            if (Environment::get($database,'Driver') == 'sqlite') {


                $table = explode(',', $table);

                $table = querySyntax($table, 'backTicket');

                $table = implode(',', $table);

                $table = str_replace(' ', '', $table);


                if ($method === CREATE) {


                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    $parameters = implode(', ', querySyntax(array_keys($this->fields), 'qMark'));

                    $data_set = array_values($this->parameters);


                    $query = "INSERT INTO $table ($fields) VALUES ($parameters)";


                    try {

                        $this->db($database)->prepare($query);

                        $this->db($database)->prepare($query)->execute($data_set);

                        ob_start();

                        $this->db($database)->prepare($query)->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                }
                else if ($method === READ) {


                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order limit $offset, $limit";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table";
                    }


                    try {
                        $query_handler = $this->db($database)->prepare($query);


                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }

                        $this->fields = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table";

                        try {

                            $query_handler = $this->db($database)->prepare($query);

                            $query_handler->execute();

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];


                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }


                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                }
                else if ($method === READ_CONDITION) {

                    $pattern = '/^[0-9]{0,999}+$/';
                    if (!preg_match($pattern, $offset)) {
                        Output::printC("Sorry ! Unexpected Error Occurred");
                        exit();
                    }

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'backTicket'));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    if (isset($limit)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order limit $offset, $limit";
                    } else if (isset($order)) {
                        $query = "SELECT $fields FROM $table WHERE $conditions ORDER BY $order";
                    } else {
                        $query = "SELECT $fields FROM $table WHERE $conditions";
                    }

                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                        }


                        $this->fields = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                    if (isset($limit)) {
                        $query = "SELECT count(*) As Count FROM $table WHERE $conditions";

                        $condition_parameters = array_values($this->condition_parameters);


                        try {

                            $query_handler = $this->db($database)->prepare($query, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_SCROLL));

                            $query_handler->execute($condition_parameters);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                            $rows_number = $rows_number[0]['Count'];


                            if ($database == 'DATABASE_1') {
                                $this->db_1_pages_count = ceil($rows_number / $limit);
                            } else if ($database == 'DATABASE_2') {
                                $this->db_2_pages_count = ceil($rows_number / $limit);;
                            } else if ($database == 'DATABASE_3') {
                                $this->db_3_pages_count = ceil($rows_number / $limit);;
                            }


                        } catch (\PDOException $error) {

                            if (Environment::get('DEBUG', 'Errors') == 'on') {
                                http_response_code(500);
                                $error_no = "Database Query Error !";
                                $error_message = $error->getMessage();
                                Output::printC($error_no);
                                Output::printC($error_message);
                                exit();
                            } else {
                                http_response_code(500);
                                Output::printC("Sorry ! Unexpected Error Occurred");
                                exit();
                            }
                        }
                    }

                    self::$condition_fields = NULL;
                    $this->condition_parameters = NULL;


                }
                else if ($method === UPDATE) {


                    $update_fields = implode(', ', querySyntax(array_keys($this->fields), 'update'));

                    $query = "UPDATE $table SET $update_fields";

                    try {
                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }
                }
                else if ($method === UPDATE_CONDITION) {


                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'update'));


                    $condition_fields = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "UPDATE $table SET $fields WHERE " . $condition_fields;


                    $parameters = array_values($this->parameters);
                    $condition_parameters = array_values($this->condition_parameters);

                    $parameters = array_merge($parameters, $condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");

                            ob_start();

                            $query_handler->debugDumpParams();

                            if ($database == 'DATABASE_1') {
                                array_push(self::$db_1_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_2') {
                                array_push(self::$db_2_query_history, ob_get_contents());
                            } else if ($database == 'DATABASE_3') {
                                array_push(self::$db_3_query_history, ob_get_contents());
                            }

                            ob_end_clean();


                            exit();
                        }

                    }
                }
                else if ($method === DELETE) {

                    $query = "DELETE FROM $table";
                    try {

                        $try = $this->db($database)->prepare($query);

                        $try->execute();

                        ob_start();

                        $try->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                    } catch (\PDOException $error) {
                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }
                }
                else if ($method === DELETE_CONDITION) {

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));

                    $query = "DELETE FROM $table WHERE " . $conditions;
                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute(array_values($this->condition_parameters));

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;
                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);

                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }
                    }


                }
                else if ($method === COUNT) {

                    $query = "SELECT count(*) As Count FROM $table";


                    try {
                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($this->parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();

                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);


                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }


                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                }
                else if ($method === COUNT_CONDITION) {


                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "SELECT count(*) As Count FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $rows_number = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $rows_number[0]['Count'];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $rows_number[0]['Count'];
                        }


                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                }
                else if ($method === AVG) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg'));

                    $query = "SELECT $fields FROM $table";


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute();

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }


                        $this->fields = NULL;
                        $this->parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }

                }
                else if ($method === AVG_CONDITION) {

                    $fields = implode(', ', querySyntax(array_keys($this->fields), 'avg'));

                    $conditions = implode(' ', querySyntax(array_keys(self::$condition_fields), 'condition'));


                    $query = "SELECT $fields FROM $table WHERE $conditions";


                    $condition_parameters = array_values($this->condition_parameters);


                    try {

                        $query_handler = $this->db($database)->prepare($query);

                        $query_handler->execute($condition_parameters);

                        ob_start();

                        $query_handler->debugDumpParams();

                        if ($database == 'DATABASE_1') {
                            array_push(self::$db_1_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_2') {
                            array_push(self::$db_2_query_history, ob_get_contents());
                        } else if ($database == 'DATABASE_3') {
                            array_push(self::$db_3_query_history, ob_get_contents());
                        }

                        ob_end_clean();


                        $average = $query_handler->fetchAll(\PDO::FETCH_ASSOC);

                        if ($database == 'DATABASE_1') {
                            $this->db_1_content = $average[0];
                        } else if ($database == 'DATABASE_2') {
                            $this->db_2_content = $average[0];
                        } else if ($database == 'DATABASE_3') {
                            $this->db_3_content = $average[0];
                        }

                        $this->fields = NULL;
                        $this->parameters = NULL;
                        self::$condition_fields = NULL;
                        $this->condition_parameters = NULL;

                    } catch (\PDOException $error) {

                        if (Environment::get('DEBUG', 'Errors') == 'on') {
                            http_response_code(500);
                            $error_no = "Database Query Error !";
                            $error_message = $error->getMessage();
                            Output::printC($error_no);
                            Output::printC($error_message);
                            exit();
                        } else {
                            http_response_code(500);
                            Output::printC("Sorry ! Unexpected Error Occurred");
                            exit();
                        }

                    }


                }
                else {
                    echo 'Error Occurred';
                    http_response_code(500);
                    exit();
                }
            }
        }

        return FALSE;

    }


    /**
     * Free Query Without ORM
     * @param $query
     * @param $method
     * @param $database
     */
    public function freeQuery($query, $method, $database)
    {

        try {

            $query_handler = $this->db($database)->query($query);

            ob_start();

            $query_handler->debugDumpParams();

            if ($database == 'DATABASE_1') {
                array_push(self::$db_1_query_history, ob_get_contents());
            } else if ($database == 'DATABASE_2') {
                array_push(self::$db_2_query_history, ob_get_contents());
            } else if ($database == 'DATABASE_3') {
                array_push(self::$db_3_query_history, ob_get_contents());
            }

            ob_end_clean();

            if ($method == 'READ' || $method == 'READ_CONDITION') {
                if ($database == 'DATABASE_1') {
                    $this->db_1_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                } else if ($database == 'DATABASE_2') {
                    $this->db_2_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                } else if ($database == 'DATABASE_3') {
                    $this->db_3_content = $query_handler->fetchAll(\PDO::FETCH_ASSOC);
                }
            }


        } catch (\PDOException $error) {

            if (Environment::get('DEBUG', 'Errors') == 'on') {
                http_response_code(500);
                $error_no = "Database Query Error !";
                $error_message = $error->getMessage();
                Output::printC($error_no);
                Output::printC($error_message);
                exit();
            } else {
                http_response_code(500);
                Output::printC("Sorry ! Unexpected Error Occurred");
                exit();
            }
        }
    }


    public function getContent($database_slot){
        if ($database_slot == DB1){
            return $this->db_1_content;
        } else if ($database_slot == DB2){
            return $this->db_2_content;
        } else if ($database_slot == DB3){
            return $this->db_3_content;
        } else {
            return FALSE;
        }

    }

    public function getPagesCount($database_slot){
        if ($database_slot == DB1){
            return $this->db_1_pages_count;
        } else if ($database_slot == DB2){
            return $this->db_2_pages_count;
        } else if ($database_slot == DB3){
            return $this->db_3_pages_count;
        } else {
            return FALSE;
        }
    }

    public static function getQueryHistory($database_slot){
        if ($database_slot == DB1){
            return self::$db_1_query_history;
        } else if ($database_slot == DB2){
            return self::$db_2_query_history;
        } else if ($database_slot == DB3){
            return self::$db_3_query_history;
        } else {
            return FALSE;
        }
    }

}