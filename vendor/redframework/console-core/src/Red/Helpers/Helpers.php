<?php

/** Red Framework
 * Helper Functions
 * @author RedCoder
 * http://redframework.ir
 */

use Red\Base\Model;


    /**
     * @param $fields
     * @param $method
     * @return array|bool
     */
    function querySyntax($fields, $method)
    {
        if (is_array($fields)) {
            $counter = 1;
            $fields_count = count($fields);
            $result = array();
            foreach ($fields as $key => $value) {
                if ($method === 'backTicket') {
                    $result[] = '`' . $value . '`';
                } else if ($method == 'qMark') {
                    $result[] = '?';
                } else if ($method === 'update') {
                    $result[] = '`' . $value . '`' . ' = ?';
                } else if ($method === 'update_no_back_ticket') {
                    $result[] = $value . ' = ?';
                } else if ($method === 'condition') {
                    //where username = :username AND ....
                    $attribute = explode('|', Model::$condition_fields[$value]);
                    if ($attribute[0] == 'any') {

                        $sign = $attribute[1];
                        if ($sign == 'equal') {
                            $sign = '=';
                        } else if ($sign == 'smaller') {
                            $sign = '<';
                        } else if ($sign == 'greater') {
                            $sign = '>';
                        } else if ($sign == 'like') {
                            $sign = 'LIKE';
                        } else if ($sign == 'not_like') {
                            $sign = 'NOT LIKE';
                        }

                        if ($counter == $fields_count) {

                            $result[] = '`' . $value . '`' . ' ' . $sign . ' ?';
                        } else {
                            $attribute = substr($attribute[2], strpos($attribute[2], ':'));
                            $attribute = ltrim($attribute, ':');

                            $result[] = '`' . $value . '`' . ' ' . $sign . ' ?' . ' ' . $attribute;
                        }
                    } else if ($attribute[0] == 'required' or 'optional') {


                        $sign = $attribute[4];
                        if ($sign == 'equal') {
                            $sign = '=';
                        } else if ($sign == 'smaller') {
                            $sign = '<';
                        } else if ($sign == 'greater') {
                            $sign = '>';
                        }  else if ($sign == 'like') {
                            $sign = 'LIKE';
                        } else if ($sign == 'not_like') {
                            $sign = 'NOT LIKE';
                        }

                        if ($counter == $fields_count) {

                            $result[] = '`' . $value . '`' . ' ' . $sign . ' ?';
                        } else {
                            $attribute = substr($attribute[5], strpos($attribute[5], ':'));
                            $attribute = ltrim($attribute, ':');

                            $result[] = '`' . $value . '`' . ' ' . $sign . ' ?' . ' ' . $attribute;
                        }

                    }

                } else if ($method === 'condition_no_back_ticket') {
                    //where username = :username AND ....
                    $attribute = explode('|', Model::$condition_fields[$value]);
                    if ($attribute[0] == 'any') {

                        $sign = $attribute[1];
                        if ($sign == 'equal') {
                            $sign = '=';
                        } else if ($sign == 'smaller') {
                            $sign = '<';
                        } else if ($sign == 'greater') {
                            $sign = '>';
                        }  else if ($sign == 'like') {
                            $sign = 'LIKE';
                        } else if ($sign == 'not_like') {
                            $sign = 'NOT LIKE';
                        }

                        if ($counter == $fields_count) {

                            $result[] = $value . ' ' . $sign . ' ?';
                        } else {
                            $attribute = substr($attribute[2], strpos($attribute[2], ':'));
                            $attribute = ltrim($attribute, ':');

                            $result[] = $value . ' ' . $sign . ' ?' . ' ' . $attribute;
                        }
                    } else if ($attribute[0] == 'required' or 'optional') {


                        $sign = $attribute[4];
                        if ($sign == 'equal') {
                            $sign = '=';
                        } else if ($sign == 'smaller') {
                            $sign = '<';
                        } else if ($sign == 'greater') {
                            $sign = '>';
                        }  else if ($sign == 'like') {
                            $sign = 'LIKE';
                        } else if ($sign == 'not_like') {
                            $sign = 'NOT LIKE';
                        }

                        if ($counter == $fields_count) {

                            $result[] = $value . ' ' . $sign . ' ?';
                        } else {
                            $attribute = substr($attribute[5], strpos($attribute[5], ':'));
                            $attribute = ltrim($attribute, ':');

                            $result[] = $value . ' ' . $sign . ' ?' . ' ' . $attribute;
                        }

                    }

                } else if ($method == 'avg'){
                    $result[] = 'avg(`' . $value . '`) As ' . $value;
                } else if ($method == 'avg_no_back_ticket'){
                    $result[] = 'avg(' . $value . ') As ' . $value;
                }

                $counter++;

            }

            return $result;

        }
        return FALSE;
    }