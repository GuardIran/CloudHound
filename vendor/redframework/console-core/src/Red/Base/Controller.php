<?php
/** Red Framework
 * Controller Class
 * Create a Model Instance Automatically
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\Base;
/**
 * Class Controller
 * @package app
 */
class Controller
{
    /**
     * @var Model $model
     */
    protected $model;

    public function __construct()
    {

        $controller_name = get_class($this);

        $controller_name = str_replace('Controller', '', $controller_name);

        $controller_name = substr($controller_name, strrpos($controller_name, '\\') + 1);

        if(file_exists(ROOT_PATH . 'app' . DS . 'Models' . DS . $controller_name . 'Model' . '.php')){

            $model = 'App' . "\\". 'Models' . "\\" . $controller_name . 'Model';

            $this->model = new $model();

        }

    }


    public static function generateRandomString($length = 10)
    {
        return substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length / strlen($x)))), 1, $length);
    }

}
