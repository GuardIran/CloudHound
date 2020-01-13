<?php
/** Red Framework
 * Red Base Class
 * @author REDCODER
 * http://redframework.ir
 */

namespace Red;


use Red\FilterService\Filter;
use Red\MailService\Mail;
use Red\SanitizeService\Sanitize;
use Red\ValidateService\Validate;

class Red
{

    private static $php_config = FALSE;
    private static $errors = array();

    public static function getErrors(){
        return self::$errors;
    }

    public static function pushError($error){
        array_push(self::$errors, $error);
        return TRUE;
    }


    public static function filter($string, $method){
        Filter::filter($string, $method);
    }

    public static function sanitize($string, $method){
        Sanitize::sanitize($string, $method);
    }

    public static function validate($string, $attribute){
        Validate::validate($string, $attribute);
    }

    public static function sendMail($subject, $target, $body, $flag = NULL){
        Mail::send($subject, $target, $body, $flag);
    }


    public static function getPhpConfig()
    {
        return self::$php_config;
    }

    public static function setPhpConfig($php_config)
    {
        self::$php_config = $php_config;
    }



}