<?php
/** Red Framework
 * Extracting Environment Variables From JSON File
 *
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\EnvironmentProvider;


class Environment
{
    private static $config = array();

    public static function initialize(){

        self::$config = json_decode(file_get_contents(ROOT_PATH . 'Environment.json'), 1);
    }

    public static function get($section = NULL, $variable = NULL){

        if ($section == NULL && $variable == NULL) {
            return self::$config;
        } else if ($section != NULL && $variable == NULL){
            return self::$config[$section];
        }

        if (isset(self::$config[$section][$variable])){
            return self::$config[$section][$variable];
        } else {
            return FALSE;
        }
    }

    public static function set($section, $variable, $value){
        self::$config[$section][$variable] = $value;
    }

}