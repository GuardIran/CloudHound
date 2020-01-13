<?php

    /** Red Framework
    * Path Variables
    * http://redframework.ir
    */



    /*
    |--------------------------------------------------------------------------
    | DIRECTORY_SEPARATOR
    |--------------------------------------------------------------------------
    |
    | DS Separates Directories Smart; At Linux it Will Use  '/' For Separating Directories
    | DS Separates Directories Smart; At Windows it Will Use  '\' For Separating Directories
    |
    */

    define('DS', DIRECTORY_SEPARATOR);


    /*
    |--------------------------------------------------------------------------
    | ROOT_PATH
    |--------------------------------------------------------------------------
    |
    | Root Path is A Constant Which Gives RealPath Address of Root
    |
    */

    define('ROOT_PATH', dirname(dirname(__FILE__)) . DS);


    /*
    |--------------------------------------------------------------------------
    | LANGUAGE_PATH
    |--------------------------------------------------------------------------
    |
    | Languages Addresses; Default Folder is Resource->Language
    |
    */

    define('LANGUAGE_PATH', ROOT_PATH . 'resources' . DS . 'Language' . DS);


    /*
    |--------------------------------------------------------------------------
    | DB_BACKUP_PATH
    |--------------------------------------------------------------------------
    |
    | Keeps the Address for Backup Process
    | Default is database->backup->dbname
    |
    */

    define('DB_BACKUP_PATH', ROOT_PATH . 'database' . DS . 'Backup' . DS);


    /*
    |--------------------------------------------------------------------------
    | DB_1MIGRATION_PATH
    |--------------------------------------------------------------------------
    |
    | Keeps the Address of Migration File of Database 1
    |
    */

    define('DB1_MIGRATION_PATH', ROOT_PATH . 'database' . DS . 'Migration' . DS . 'migration_of_db1.sql');


    /*
    |--------------------------------------------------------------------------
    | DB2_MIGRATION_PATH
    |--------------------------------------------------------------------------
    |
    | Keeps the Address of Migration File of Database 2
    |
    */

    define('DB2_MIGRATION_PATH', ROOT_PATH . 'database' . DS . 'Migration' . DS . 'migration_of_db2.sql');


    /*
    |--------------------------------------------------------------------------
    | DB3_MIGRATION_PATH
    |--------------------------------------------------------------------------
    |
    | Keeps the Address of Migration File of Database 3
    |
    */

    define('DB3_MIGRATION_PATH', ROOT_PATH . 'database' . DS . 'Migration' . DS . 'migration_of_db3.sql');


    /*
    |--------------------------------------------------------------------------
    | ROOT_DIRECTORY
    |--------------------------------------------------------------------------
    |
    | If Your Application is In PUBLIC_HTML Root Directory You dont Have to Fill this
    | Else it is Very Important for Processing
    |
    */

    if (isset($php_development_server)) {
        define('ROOT_DIRECTORY', '/');
    } else {
        define('ROOT_DIRECTORY', '/RedFramework/');
    }


    /*
    |--------------------------------------------------------------------------
    | MYSQL_PATH
    |--------------------------------------------------------------------------
    |
    | Important For MYSQL Processing; Fill it If You Are Using a MYSQL Database
    |
    */

    //define('MYSQL_PATH', 'D:\Wamp\bin\mysql\mysql5.7.24\bin\mysql');


    /*
    |--------------------------------------------------------------------------
    | MYSQL_DUMP_PATH
    |--------------------------------------------------------------------------
    |
    | Important For MYSQL Backup and Migrate Processes; Fill it If You Are Using a MYSQL Database
    |
    */

    //define('MYSQL_DUMP_PATH', 'D:\Wamp\bin\mysql\mysql5.7.24\bin\mysqldump');


    /*
    |--------------------------------------------------------------------------
    | SQL_CMD_PATH
    |--------------------------------------------------------------------------
    |
    | Important For Microsoft SQL Server Backup and Migrate Processes; Fill it If You Are Using a MS SQL Database
    |
    */

    //define('SQL_CMD_PATH', '"C:\Program Files\Microsoft SQL Server\Client SDK\ODBC\130\Tools\Binn\SQLCMD.exe"');


    /*
    |--------------------------------------------------------------------------
    | SQLITE_CLI_PATH
    |--------------------------------------------------------------------------
    |
    | SQLite CLI (Command Line Interface) Path
    | Important For SQLite Backup and Migrate Processes; Fill it If You Are Using a SQLite Database
    |
    */

    //define('SQLITE_CLI_PATH', ROOT_PATH . "database" . DS . "SQLite" . DS . 'sqlite3.exe');