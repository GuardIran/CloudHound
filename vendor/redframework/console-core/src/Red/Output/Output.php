<?php
/** Red Framework
 * Output Class
 * Handle Console OutPut
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\Output;


class Output
{
    const black = '0;30';
    const dark_gray = '1;30';
    const blue = '0;34';
    const light_blue = '1;34';
    const green = '0;32';
    const light_green = '1;32';
    const cyan = '0;36';
    const light_cyan = '1;36';
    const red = '0;31';
    const light_red = '1;31';
    const purple = '0;35';
    const light_purple = '1;35';
    const brown = '0;33';
    const yellow = '1;33';
    const light_gray = '0;37';
    const white = '1;37';

    public static function printC($string, $color = null){

        if ($color == null){
            $color = self::light_red;
        }

        echo "\e[" . $color . ";40m" . "    --> " . $string;
        echo "\e[" . self::white . ";40m";
    }


}