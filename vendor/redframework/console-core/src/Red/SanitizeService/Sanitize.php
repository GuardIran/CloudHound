<?php
/** Red Framework
 * Filter Class
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\SanitizeService;


class Sanitize
{

    private static $rules = array();

    public static function initialize(){
        Rules::rules();
    }

    public static function sanitize($string, $method)
    {
        if ($method == 'digit') {
            return preg_replace('/([0-9])+/', '', $string);
        } else if ($method == 'space') {
            $string = preg_replace('/([\s\n])+/', '', $string);
            return $string;
        }

        foreach (self::$rules as $rule){
            if ($rule['rule'] == $method){
                $result = call_user_func_array($rule['callback'], [$string]);
                return $result;
            }
        }

        return FALSE;
    }

    /**
     * @param string $role
     * @param callable $callback
     * @return bool
     */
    public static function addRole($rule, $callback)
    {

        array_push(self::$rules, ['rule' => $rule, 'callback' => $callback]);
        return TRUE;
    }

    /**
     * @return mixed
     */
    public static function getRules()
    {
        return self::$rules;
    }


}