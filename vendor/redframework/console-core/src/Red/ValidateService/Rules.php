<?php
/** Red Framework
 * Validation Rules
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\ValidateService;



class Rules
{
    public static function rules()
    {

        /* Example:
        Validate::addRule("test", function ($no, $min, $max, $string) {
            // DO Your RegEX Based on Info then Return True or False Result (This Callback will be used For Standard Validation)
        },
            function ($no, $min, $max, $string) {
            // DO Your RegEX Based on Info then push your Error and Return True or False Result (This Callback will be used For Model Validation)
            });*/

        Validate::addRule("domain", function ($no, $min, $max, $string) {
            $result = preg_match("^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$^", $string);

            if ($no == "optional" && $string == null){
                return true;
            } else {
                return $result;
            }


        }, function ($no, $min, $max, $string) {
            // Model Validation - Which is Undefined for this Role .
        });
    }

}