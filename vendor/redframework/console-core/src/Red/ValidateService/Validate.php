<?php
/** Red Framework
 * Validation Class
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\ValidateService;

use Red\LanguageService\Language;
use Red\Red;

/**
 * Class validate
 * @package App\Red\Middlewares
 */
class Validate
{
    private static $rules = array();


    public static function initialize(){
        Rules::rules();
    }

    public static function validate($string, $attribute)
    {


        $attribute = explode('|', $attribute);

        $max = 0;
        $min = 0;

        if (isset($attribute[3])) {

            $sanitize_space = substr($attribute[3], strpos($attribute[3], ':') + 1);

            if ($sanitize_space == 'yes') {
                $string = preg_replace('/([\s\n])+/', '', $string);
            }
        }


        if ($attribute[0] == 'required') {


            if ($string == '' && $string == NULL) {
                return FALSE;
            }


            if (isset($attribute[1])) {
                preg_match('/:.*/', $attribute[1], $valid);

                $valid = ltrim($valid[0], ':');

                $valid = explode('-', $valid);

                if (isset($attribute[2])) {
                    preg_match('/:.*/', $attribute[2], $limit);
                    $limit = explode('-', $limit[0]);
                    $min = ltrim($limit[0], ':');
                    $max = $limit[1];
                }


                if (in_array('digit', $valid)) {

                    $pattern = '/^([0-9\n ]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else if (in_array('en', $valid)) {

                    $pattern = "/^([a-zA-Z\n ]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }

                } else if (in_array('en_digit', $valid)) {

                    $pattern = "/^([a-zA-Z0-9\n ]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }

                } else if (in_array('fa', $valid)) {


                    $pattern = '/^([\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                } else if (in_array('fa_digit', $valid)) {


                    $pattern = '/^([\n0-9 پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                } else if (in_array('mix', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                } else if (in_array('text', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_en', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,;!?@\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_fa', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('email', $valid)) {


                    $string = preg_replace('/\n\s+/', '', $string);


                    if (filter_var($string, FILTER_VALIDATE_EMAIL) == TRUE) {

                        if (mb_strlen($string) < $min OR mb_strlen($string) > $max) {

                            return FALSE;
                        } else {
                            return TRUE;
                        }

                    } else {
                        return FALSE;
                    }
                } else if (in_array('IP', $valid)) {


                    $string = preg_replace('/\n\s+/', '', $string);


                    if (filter_var($string, FILTER_VALIDATE_IP) == TRUE) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }
                } else if (in_array('username', $valid)) {

                    $string = preg_replace('/\n\s+/', '', $string);


                    $pattern = "/^([a-zA-Z0-9\.\-_]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }

                } else if (in_array('password', $valid)) {

                    $string = preg_replace('/\n\s+/', '', $string);


                    $pattern = "/^([a-zA-Z0-9\.*@]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }

                } else if (in_array('phone', $valid)) {

                    $string = preg_replace('/\n\s+/', '', $string);


                    $pattern = '/^([+]{0,1})([0-9]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $string, $compare)) {

                        if (mb_strlen($compare[0]) < $min || mb_strlen($compare[0]) > $max) {
                            return FALSE;
                        } else {
                            return TRUE;
                        }
                    } else {
                        return FALSE;
                    }
                } else if (in_array('address', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                } else if (in_array('address_en', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\-\n ]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                } else if (in_array('address_fa', $valid)) {

                    $pattern = '/^([0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $string, $compare)) {
                        return TRUE;
                    } else {
                        return FALSE;
                    }


                }


            }

        } else if ($attribute[0] == 'optional') {


            if (isset($attribute[1])) {


                preg_match('/:.*/', $attribute[1], $valid);

                $valid = ltrim($valid[0], ':');

                $valid = explode('-', $valid);

                if (isset($attribute[2])) {
                    preg_match('/:.*/', $attribute[2], $limit);
                    $limit = explode('-', $limit[0]);
                    $min = ltrim($limit[0], ':');
                    $max = $limit[1];
                }


                if (in_array('digit', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([0-9\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('en', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = "/^([a-zA-Z\n ]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('en_digit', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = "/^([a-zA-Z0-9\n ]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('fa', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }

                } else if (in_array('fa_digit', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([\n0-9 پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('mix', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([a-zA-Z0-9\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_en', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,;!?@\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_fa', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $pattern = '/^([0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('email', $valid)) {
                    if ($string != '' && $string != NULL) {

                        $string = preg_replace('/\n\s+/', '', $string);


                        if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
                            if (mb_strlen($string) < $min OR mb_strlen($string) > $max) {

                                return FALSE;
                            } else {
                                return TRUE;
                            }

                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('IP', $valid)) {

                    if ($string != '' && $string != NULL) {


                        $string = preg_replace('/\n\s+/', '', $string);


                        if (filter_var($string, FILTER_VALIDATE_IP) == TRUE) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('username', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $string = preg_replace('/\n\s+/', '', $string);


                        $pattern = "/^([a-zA-Z0-9\.\-_]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('password', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $string = preg_replace('/\n\s+/', '', $string);


                        $pattern = "/^([a-zA-Z0-9\.*@]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('phone', $valid)) {

                    if ($string != '' && $string != NULL) {

                        $string = preg_replace('/\n\s+/', '', $string);


                        $pattern = '/^([+]{0,1})([0-9]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $string, $compare)) {

                            if (mb_strlen($compare[0]) < $min || mb_strlen($compare[0]) > $max) {
                                return FALSE;
                            } else {
                                return TRUE;
                            }
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('address', $valid)) {
                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([a-zA-Z0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }


                } else if (in_array('address_en', $valid)) {
                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([a-zA-Z0-9\-\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }


                } else if (in_array('address_fa', $valid)) {
                    if ($string != '' && $string != NULL) {

                        $pattern = '/^([0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $string, $compare)) {
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }


                }


            }
        } else if ($attribute[0] === 'any') {
            return TRUE;
        }

        foreach (self::$rules as $rule){
            if ($rule['rule'] == $valid[0]){
                $result = call_user_func_array($rule['standard_callback'], [$attribute[0], $min, $max, $string]);
                if ($result == TRUE){
                    return TRUE;
                } else {
                    return FALSE;
                }
            }
        }

        return FALSE;
    }


    /**
     * @param $field
     * @param $parameter
     * @param $attribute
     * @return bool
     */
    public static function modelValidate($field, $parameter, $attribute)
    {


        $attribute = explode('|', $attribute);

        $min = 0;
        $max = 0;


        if (isset($attribute[3])) {
            $sanitize_space = substr($attribute[3], strpos($attribute[3], ':') + 1);
            if ($sanitize_space == 'yes') {
                $parameter = preg_replace('/([\s\n])+/', '', $parameter);
            }
        }


        if ($attribute[0] == 'required') {


            if ($parameter == '' && $parameter == NULL) {
                Red::pushError(sprintf(Language::get('validate', 'empty'), $field));
                return FALSE;
            }


            if (isset($attribute[1])) {


                preg_match('/:.*/', $attribute[1], $valid);

                $valid = ltrim($valid[0], ':');

                $valid = explode('-', $valid);


                if (isset($attribute[2])) {
                    preg_match('/:.*/', $attribute[2], $limit);
                    $limit = explode('-', $limit[0]);
                    $min = ltrim($limit[0], ':');
                    $max = $limit[1];
                }


                if (in_array('digit', $valid)) {

                    $pattern = '/^([0-9\n ]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_digit'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('en', $valid)) {

                    $pattern = "/^([a-zA-Z\n ]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_lang_en'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }

                } else if (in_array('en_digit', $valid)) {

                    $pattern = "/^([a-zA-Z0-9\n ]){" . $min . "," . $max . "}+$/";


                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_lang_enDigit'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }

                } else if (in_array('fa', $valid)) {


                    $pattern = '/^([\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_lang_fa'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }


                } else if (in_array('fa_digit', $valid)) {


                    $pattern = '/^([\n0-9 پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_lang_faDigit'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }


                } else if (in_array('mix', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_lang_mix'), $field, $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }


                } else if (in_array('text', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_en', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,،;؛!?؟@\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text_en'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_fa', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text_fa'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('email', $valid)) {


                    $parameter = preg_replace('/\n\s+/', '', $parameter);


                    if (filter_var($parameter, FILTER_VALIDATE_EMAIL) == TRUE) {

                        if (mb_strlen($parameter) < $min OR mb_strlen($parameter) > $max) {

                            $error = sprintf(Language::get('validate', 'invalid_email'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        } else {
                            return TRUE;
                        }

                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_email'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('IP', $valid)) {


                    $$parameter = preg_replace('/\n\s+/', '', $parameter);


                    if (filter_var($parameter, FILTER_VALIDATE_IP) == TRUE) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_IP'));
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('username', $valid)) {

                    $parameter = preg_replace('/\n\s+/', '', $parameter);


                    $pattern = "/^([a-zA-Z0-9\.\-_]){" . $min . "," . $max . "}+$/";

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_username'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }

                } else if (in_array('password', $valid)) {

                    $parameter = preg_replace('/\n\s+/', '', $parameter);


                    $pattern = "/^([a-zA-Z0-9\.*@]){" . $min . "," . $max . "}+$/";


                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_password'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }

                } else if (in_array('phone', $valid)) {

                    $parameter = preg_replace('/\n\s+/', '', $parameter);

                    $pattern = '/^([+]{0,1})([0-9]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $parameter, $compare)) {
                        if (mb_strlen($compare[0]) < $min || mb_strlen($compare[0]) > $max) {
                            return FALSE;
                        } else {
                            return TRUE;
                        }
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_phone'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('address', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_address'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('address_en', $valid)) {

                    $pattern = '/^([a-zA-Z0-9\-\n ]){' . $min . ',' . $max . '}+$/';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_address_en'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                } else if (in_array('address_fa', $valid)) {

                    $pattern = '/^([0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]){' . $min . ',' . $max . '}+$/u';

                    if (preg_match($pattern, $parameter, $compare)) {
                        return TRUE;
                    } else {
                        $error = sprintf(Language::get('validate', 'invalid_address_fa'), $min, $max);
                        Red::pushError($error);
                        return FALSE;
                    }
                }


            }

        } else if ($attribute[0] == 'optional') {


            if (isset($attribute[1])) {


                preg_match('/:.*/', $attribute[1], $valid);

                $valid = ltrim($valid[0], ':');

                $valid = explode('-', $valid);

                if (isset($attribute[2])) {
                    preg_match('/:.*/', $attribute[2], $limit);
                    $limit = explode('-', $limit[0]);
                    $min = ltrim($limit[0], ':');
                    $max = $limit[1];
                }

                if (in_array('digit', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $pattern = '/^([0-9\n ]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_digit'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('en', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $pattern = "/^([a-zA-Z\n ]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_lang_en'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }

                } else if (in_array('en_digit', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $pattern = "/^([a-zA-Z0-9\n ]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_lang_enDigit'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }

                } else if (in_array('fa', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $pattern = '/^([\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_lang_fa'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }

                } else if (in_array('fa_digit', $valid)) {


                    if ($parameter != '' && $parameter != NULL) {

                        $pattern = '/^([\n0-9 پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_lang_faDigit'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('mix', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_lang_mix'), $field, $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_en', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\.\-_,;!?@\n ]{' . $min . ',' . $max . '})+$/';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text_en'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('text_fa', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([0-9\.\-_,،;؛!?؟@\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_text_fa'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('email', $valid)) {
                    if ($parameter != '' && $parameter != NULL) {

                        $parameter = preg_replace('/\n\s+/', '', $parameter);

                        if (filter_var($parameter, FILTER_VALIDATE_EMAIL)) {
                            if (mb_strlen($parameter) < $min OR mb_strlen($parameter) > $max) {

                                $error = sprintf(Language::get('validate', 'invalid_email'), $min, $max);
                                Red::pushError($error);
                            } else {
                                return TRUE;
                            }

                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_email'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('IP', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $parameter = preg_replace('/\n\s+/', '', $parameter);


                        if (filter_var($parameter, FILTER_VALIDATE_IP) == TRUE) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_IP'));
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }

                } else if (in_array('username', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $parameter = preg_replace('/\n\s+/', '', $parameter);


                        $pattern = "/^([a-zA-Z0-9\.\-_]){" . $min . "," . $max . "}+$/";

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_username'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('password', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $parameter = preg_replace('/\n\s+/', '', $parameter);


                        $pattern = "/^([a-zA-Z0-9\.*@]){" . $min . "," . $max . "}+$/";


                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_password'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }

                    } else {
                        return TRUE;
                    }
                } else if (in_array('phone', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {

                        $parameter = preg_replace('/\n\s+/', '', $parameter);

                        $pattern = '/^([+]{0,1})([0-9]){' . $min . ',' . $max . '}+$/';

                        if (preg_match($pattern, $parameter, $compare)) {

                            if (mb_strlen($compare[0]) < $min || mb_strlen($compare[0]) > $max) {
                                return FALSE;
                            } else {
                                return TRUE;
                            }
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_phone'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('address', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_address'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('address_en', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([a-zA-Z0-9\-\n ]{' . $min . ',' . $max . '})+$/';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_address_en'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                } else if (in_array('address_fa', $valid)) {

                    if ($parameter != '' && $parameter != NULL) {


                        $pattern = '/^([0-9\-\n پچجحخهعغفقثصضشسیبلاتنمکگوئدذرزطظژؤإأءًٌٍَُِّ]{' . $min . ',' . $max . '})+$/u';

                        if (preg_match($pattern, $parameter, $compare)) {
                            return TRUE;
                        } else {
                            $error = sprintf(Language::get('validate', 'invalid_address_fa'), $min, $max);
                            Red::pushError($error);
                            return FALSE;
                        }
                    } else {
                        return TRUE;
                    }
                }

            }
        } else if ($attribute[0] === 'any') {
            return TRUE;
        }

        foreach (self::$rules as $rule){
            if ($rule['rule'] == $valid[0]){
                $result = call_user_func_array($rule['model_callback'], [$attribute[0], $min, $max, $parameter]);
                return $result;
            }
        }

        return FALSE;
    }

    /**
     * @return array
     */
    public static function getRules()
    {
        return self::$rules;
    }

    /**
     * @param string $rule
     * @param callable $standard_callback
     * @param callable $model_callback
     * @return boolean
     */
    public static function addRule($rule, $standard_callback, $model_callback)
    {
        array_push(self::$rules, ['rule' => $rule, 'standard_callback' => $standard_callback,
            'model_callback' => $model_callback
            ]);
        return TRUE;
    }


}