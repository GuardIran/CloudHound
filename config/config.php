<?php

use Red\EnvironmentProvider\Environment;
use Red\Red;


    /*
    |--------------------------------------------------------------------------
    | Using PHP Config File
    |--------------------------------------------------------------------------
    |
    | If You Set Value of This Variable TRUE it will Use This Config
    | Else it will Use Environment Variables at Environment.json File
    |
    */

    Red::setPhpConfig(FALSE);


    /*
    |--------------------------------------------------------------------------
    | Project Name
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('PROJECT', 'Name', 'RedFramework');


    /*
    |--------------------------------------------------------------------------
    | Project State
    | State Can Be Production / Maintenance / Break
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('PROJECT', 'State', 'Production');


    /*
    |--------------------------------------------------------------------------
    | Project Language
    |--------------------------------------------------------------------------
    |
    | If No Language Been Set by setLanguage Function
    | This Language Will be Used
    | Languages are Stored at Resources->Language
    |
    */

    Environment::set('PROJECT', 'Language', 'en');


    /*
    |--------------------------------------------------------------------------
    | Project Programmer
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('PROJECT', 'Programmer', 'RedCoder');


    /*
    |--------------------------------------------------------------------------
    | Project Secret Key
    |--------------------------------------------------------------------------
    |
    | Do not Forget to Set your Unique Secret Key
    | it Will be Salted with Encryption Algorithm
    |
    */

    Environment::set('PROJECT', 'SecretKey', 'RedCoder_FrameWorK762');



    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | Your Timezone will Be Set at Start of Application
    | So in Any Controller You Can access Time on your Zone
    |
    */

    Environment::set('PROJECT', 'Timezone', 'Asia/Tehran');


    /*
    |--------------------------------------------------------------------------
    | Timezone
    |--------------------------------------------------------------------------
    |
    | Framework Caching Including Template Engine Cache
    |
    */

    Environment::set('PROJECT', 'Cache', 'off');


    /*
    |--------------------------------------------------------------------------
    | Display Errors
    |--------------------------------------------------------------------------
    |
    | Setting this Option On will Display All Errors
    | be Careful with this Option !
    | Use this For Development Mode.
    |
    */

    Environment::set('DEBUG', 'Errors', 'on');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Status
    |--------------------------------------------------------------------------
    |
    | Red Framework is Available to Use 2 Database in a Same Time.
    | to Turn Database_1 , Set Status to On.
    |
    */

    Environment::set('DATABASE_1', 'Status', 'off');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Driver
    |--------------------------------------------------------------------------
    |
    | Red Framework is Supporting 3 Famous Database: MYSQL, SQL Server and SQLite Embedded Database
    | To use MYSQL driver Set Driver to mysql
    | To use MS SQL Driver Set driver to sqlsrv
    | To use SQLite Driver Set driver to sqlite
    |
    */

    Environment::set('DATABASE_1', 'Driver', 'mysql');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Host
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'Host', 'localhost');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Port
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'Port', '3306');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Name
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'Name', 'red');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Username
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'User', 'root');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Password
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'Password', 'red');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Charset
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_1', 'Charset', 'utf8');

    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Backup
    |--------------------------------------------------------------------------
    |
    | You Should Set your Timezone before Setting Backup Time to make Time Correct
    | Backup will be Stored at database->backup->dbname
    |
    */

    Environment::set('DATABASE_1', 'Backup', 'off');
    Environment::set('DATABASE_1', 'BackupTime', '23');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_1 Migration
    |--------------------------------------------------------------------------
    |
    | Before Setting Migration on You should Set your SQL File at database->migration->migration_of_db1.sql
    |
    */

    Environment::set('DATABASE_1', 'Migration', 'off');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Status
    |--------------------------------------------------------------------------
    |
    | Red Framework is Available to Use 2 Database in a Same Time.
    | to Turn Database_1 , Set Status to On.
    |
    */

    Environment::set('DATABASE_2', 'Status', 'off');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Driver
    |--------------------------------------------------------------------------
    |
    | Red Framework is Supporting 3 Famous Database: MYSQL, SQL Server and SQLite Embedded Database
    | To use MYSQL driver Set Driver to mysql
    | To use MS SQL Driver Set driver to sqlsrv
    | To use SQLite Driver Set driver to sqlite
    |
    */

    Environment::set('DATABASE_2', 'Driver', 'sqlsrv');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Host
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'Host', 'localhost');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Port
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'Port', '3306');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Name
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'Name', 'red');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Username
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'User', 'root');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Password
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'Password', 'red');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Charset
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_2', 'Charset', 'utf8');

    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Backup
    |--------------------------------------------------------------------------
    |
    | You Should Set your Timezone before Setting Backup Time to make Time Correct
    | Backup will be Stored at database->backup->dbname
    |
    */

    Environment::set('DATABASE_2', 'Backup', 'off');
    Environment::set('DATABASE_2', 'BackupTime', '23');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_2 Migration
    |--------------------------------------------------------------------------
    |
    | Before Setting Migration on You should Set your SQL File at database->migration->migration_of_db1.sql
    |
    */

    Environment::set('DATABASE_2', 'Migration', 'off');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Status
    |--------------------------------------------------------------------------
    |
    | Red Framework is Available to Use 3 Database in a Same Time.
    | to Turn Database_3 , Set Status to On.
    |
    */

    Environment::set('DATABASE_3', 'Status', 'off');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Driver
    |--------------------------------------------------------------------------
    |
    | Red Framework is Supporting 3 Famous Database: MYSQL, SQL Server and SQLite Embedded Database
    | To use MYSQL driver Set Driver to mysql
    | To use MS SQL Driver Set driver to sqlsrv
    | To use SQLite Driver Set driver to sqlite
    |
    */

    Environment::set('DATABASE_3', 'Driver', 'sqlite');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Name
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_3', 'Name', 'red');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Charset
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('DATABASE_3', 'Charset', 'utf8');

    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Backup
    |--------------------------------------------------------------------------
    |
    | You Should Set your Timezone before Setting Backup Time to make Time Correct
    | Backup will be Stored at database->backup->dbname
    |
    */

    Environment::set('DATABASE_3', 'Backup', 'off');
    Environment::set('DATABASE_3', 'BackupTime', '23');


    /*
    |--------------------------------------------------------------------------
    | DATABASE_3 Migration
    |--------------------------------------------------------------------------
    |
    | Before Setting Migration on You should Set your SQL File at database->migration->migration_of_db1.sql
    |
    */

    Environment::set('DATABASE_3', 'Migration', 'off');


    /*
    |--------------------------------------------------------------------------
    | SMTP Mail Server Status
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('MAIL_SERVER', 'Status', 'off');


    /*
    |--------------------------------------------------------------------------
    | SMTP Mail Server Host
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('MAIL_SERVER', 'Host', 'smtp.localhost');


    /*
    |--------------------------------------------------------------------------
    | SMTP Mail Server Port
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('MAIL_SERVER', 'Host', '2456');


    /*
    |--------------------------------------------------------------------------
    | SMTP Mail Server Username
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('MAIL_SERVER', 'Username', 'red@localhost');


    /*
    |--------------------------------------------------------------------------
    | SMTP Mail Server Password
    |--------------------------------------------------------------------------
    |
    |
    */

    Environment::set('MAIL_SERVER', 'Password', 'red');

