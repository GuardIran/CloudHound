<?php
/** Red Framework
 * Filter Class
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\FilterService;


class Filter
{

    private static $rules = array();

    public static function initialize(){
        Rules::rules();
    }

    public static function filter($string, $method)
    {
        if ($method === 'digit') {

            return preg_replace('/[^0-9\n ]/', '', $string);

        } else if ($method === 'en') {

            return preg_replace('/[^a-zA-Z\n ]/', '', $string);

        } else if ($method === 'en_digit') {

            return preg_replace('/[^a-zA-Z0-9\n ]/', '', $string);

        } else if ($method === 'fa') {

            return preg_replace('/[^\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]/', '', $string);

        } else if ($method === 'fa_digit') {

            return preg_replace('/[^\n0-9 پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]/', '', $string);

        } else if ($method === 'mix') {

            return preg_replace('/[^a-zA-Z0-9\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأء]/', '', $string);

        } else if ($method === 'text') {

            return preg_replace('/[^a-zA-Z0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأء]/', '', $string);

        } else if ($method === 'text_en') {

            return preg_replace('/[^a-zA-Z0-9\.\-_,;!?@\n ]/', '', $string);

        } else if ($method === 'text_fa') {

            return preg_replace('/[^0-9\.\-_,،;؛!؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأء]/', '', $string);

        } else if ($method === 'email') {

            return preg_replace('/[^a-zA-Z0-9@]/', '', $string);

        } else if ($method === 'IP') {

            return preg_replace('/[^0-9.]/', '', $string);

        } else if ($method === 'username') {

            return preg_replace('/[^a-zA-Z0-9\.\-_]/', '', $string);

        } else if ($method === 'password') {

            return preg_replace('/[^a-zA-Z0-9\.*@]/', '', $string);

        } else if ($method === 'phone') {

            return preg_replace('/[^0-9+]/', '', $string);

        } else if ($method === 'address') {

            return preg_replace('/[^a-zA-Z0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأء]/', '', $string);

        } else if ($method === 'address_en') {

            return preg_replace('/[^a-zA-Z0-9\-\nّ ]/', '', $string);

        } else if ($method === 'address_fa') {

            return preg_replace('/[^0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأء]/', '', $string);

        }

        foreach (self::$rules as $rule){
            if ($rule['rule'] == $method){
                $result = call_user_func_array($rule['callback'], [$string]);
                return $result;
            }
        }

        return FALSE;
    }


    public static function addRule($rule, $callback)
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