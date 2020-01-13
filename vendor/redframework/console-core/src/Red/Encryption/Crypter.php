<?php

/** Red Framework
 * Crypter Class
 *
 * This Class Will EnCrypt And DeCrypt Data With Special Algorithm Salted With Secret Key
 * @author RedCoder
 * http://redframework.ir
 */

namespace Red\Encryption;


use Red\EnvironmentProvider\Environment;

/**
 * Class Crypter
 * @package App\Red\Middlewares
 */
class Crypter
{

    const SEPARATOR1 = ' /=/ ';
    const SEPARATOR2 = ' /~/ ';

    public static $openssl_config; // Filename of the openssl.cnf config file.
    public static $RSA_key_length;


    public static function encrypt($data)
    {

        $secret_key = Environment::get('PROJECT', 'SecretKey');

        if (is_array($data)) {
            foreach ($data as $variable => $value) {
                if (!is_array($value)) {

                    // Set a random salt
                    $salt = openssl_random_pseudo_bytes(16);

                    $salted = '';
                    $dx = '';
                    // Salt the key(32) and iv(16) = 48
                    while (strlen($salted) < 48) {
                        $dx = hash('sha256', $dx . $secret_key . $salt, true);
                        $salted .= $dx;
                    }

                    $key = substr($salted, 0, 32);
                    $iv = substr($salted, 32, 16);

                    $encrypted_data[$variable] = openssl_encrypt($value, 'AES-256-CBC', $key, true, $iv);
                    $encrypted_data[$variable] = base64_encode($salt . $encrypted_data[$variable]);
                } else {
                    foreach ($value as $two_d_array_name => $two_d_array_value) {
                        if (!is_array($two_d_array_value)) {

                            // Set a random salt
                            $salt = openssl_random_pseudo_bytes(16);

                            $salted = '';
                            $dx = '';
                            // Salt the key(32) and iv(16) = 48
                            while (strlen($salted) < 48) {
                                $dx = hash('sha256', $dx . $secret_key . $salt, true);
                                $salted .= $dx;
                            }

                            $key = substr($salted, 0, 32);
                            $iv = substr($salted, 32, 16);

                            $encrypted_data[$two_d_array_name] = openssl_encrypt($two_d_array_value, 'AES-256-CBC', $key, true, $iv);
                            $encrypted_data[$two_d_array_name] = base64_encode($salt . $encrypted_data[$two_d_array_name]);
                        } else {
                            foreach ($two_d_array_value as $three_d_array_name => $three_d_array_value) {
                                if (!is_array($three_d_array_value)) {

                                    // Set a random salt
                                    $salt = openssl_random_pseudo_bytes(16);

                                    $salted = '';
                                    $dx = '';
                                    // Salt the key(32) and iv(16) = 48
                                    while (strlen($salted) < 48) {
                                        $dx = hash('sha256', $dx . $secret_key . $salt, true);
                                        $salted .= $dx;
                                    }

                                    $key = substr($salted, 0, 32);
                                    $iv = substr($salted, 32, 16);


                                    $encrypted_data[$two_d_array_name][$three_d_array_name] = openssl_encrypt($three_d_array_value, 'AES-256-CBC', $key, true, $iv);
                                    $encrypted_data[$two_d_array_name][$three_d_array_name] = base64_encode($salt . $encrypted_data[$two_d_array_name][$three_d_array_name]);

                                }
                            }
                        }
                    }
                }
            }
            return $encrypted_data;

        } else {
            // Set a random salt
            $salt = openssl_random_pseudo_bytes(16);

            $salted = '';
            $dx = '';
            // Salt the key(32) and iv(16) = 48
            while (strlen($salted) < 48) {
                $dx = hash('sha256', $dx . $secret_key . $salt, true);
                $salted .= $dx;
            }

            $key = substr($salted, 0, 32);
            $iv = substr($salted, 32, 16);

            $encrypted_data = openssl_encrypt($data, 'AES-256-CBC', $key, true, $iv);
            return base64_encode($salt . $encrypted_data);
        }
    }

    public static function decrypt($data)
    {

        $secret_key = Environment::get('PROJECT', 'SecretKey');

        if (is_array($data)) {
            foreach ($data as $variable => $value) {
                if (!is_array($value)) {

                    $data = base64_decode($value);
                    $salt = substr($data, 0, 16);
                    $ct = substr($data, 16);

                    $rounds = 3; // depends on key length
                    $data00 = $secret_key . $salt;
                    $hash = array();
                    $hash[0] = hash('sha256', $data00, true);
                    $result = $hash[0];
                    for ($i = 1; $i < $rounds; $i++) {
                        $hash[$i] = hash('sha256', $hash[$i - 1] . $data00, true);
                        $result .= $hash[$i];
                    }
                    $key = substr($result, 0, 32);
                    $iv = substr($result, 32, 16);

                    if (openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv) == TRUE) {
                        $decrypted_data[$variable] = openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv);
                    } else {
                        $decrypted_data[$variable] = $value;
                    }
                } else {
                    foreach ($value as $two_d_array_name => $two_d_array_value) {
                        if (!is_array($two_d_array_value)) {

                            $data = base64_decode($two_d_array_value);
                            $salt = substr($data, 0, 16);
                            $ct = substr($data, 16);

                            $rounds = 3; // depends on key length
                            $data00 = $secret_key . $salt;
                            $hash = array();
                            $hash[0] = hash('sha256', $data00, true);
                            $result = $hash[0];
                            for ($i = 1; $i < $rounds; $i++) {
                                $hash[$i] = hash('sha256', $hash[$i - 1] . $data00, true);
                                $result .= $hash[$i];
                            }
                            $key = substr($result, 0, 32);
                            $iv = substr($result, 32, 16);

                            if (openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv) == TRUE) {
                                $decrypted_data[$variable][$two_d_array_name] = openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv);
                            } else {
                                $decrypted_data[$variable][$two_d_array_name] = $value;
                            }
                        } else {
                            foreach ($two_d_array_value as $three_d_array_name => $three_d_array_value) {
                                if (!is_array($three_d_array_value)) {

                                    $data = base64_decode($three_d_array_value);
                                    $salt = substr($data, 0, 16);
                                    $ct = substr($data, 16);

                                    $rounds = 3; // depends on key length
                                    $data00 = $secret_key . $salt;
                                    $hash = array();
                                    $hash[0] = hash('sha256', $data00, true);
                                    $result = $hash[0];
                                    for ($i = 1; $i < $rounds; $i++) {
                                        $hash[$i] = hash('sha256', $hash[$i - 1] . $data00, true);
                                        $result .= $hash[$i];
                                    }
                                    $key = substr($result, 0, 32);
                                    $iv = substr($result, 32, 16);

                                    if (openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv) == TRUE) {
                                        $decrypted_data[$variable][$two_d_array_name][$three_d_array_name] = openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv);
                                    } else {
                                        $decrypted_data[$variable][$two_d_array_name][$three_d_array_name] = $value;
                                    }
                                }
                            }


                        }
                    }
                }
            }
        } else {

            $data = base64_decode($data);
            $salt = substr($data, 0, 16);
            $ct = substr($data, 16);

            $rounds = 3; // depends on key length
            $data00 = $secret_key . $salt;
            $hash = array();
            $hash[0] = hash('sha256', $data00, true);
            $result = $hash[0];
            for ($i = 1; $i < $rounds; $i++) {
                $hash[$i] = hash('sha256', $hash[$i - 1] . $data00, true);
                $result .= $hash[$i];
            }
            $key = substr($result, 0, 32);
            $iv = substr($result, 32, 16);

            $decrypted_data = openssl_decrypt($ct, 'AES-256-CBC', $key, true, $iv);
        }


        return $decrypted_data;

    }

}