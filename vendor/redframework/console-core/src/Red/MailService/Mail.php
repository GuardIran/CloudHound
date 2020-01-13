<?php
/**
 * RED FRAMEWORK
 * Mail Sender - Using SMTP
 * this Class is a Wrapper to Communicate PEAR Mail
 * @author REDCODER
 * http://redframework.ir
 */

namespace Red\MailService;


use Red\EnvironmentProvider\Environment;

class Mail
{
    const HTML_Header = 1;
    private static $pear_instance = NULL;

    private static function initialize()
    {

        if (Environment::get())
            if (self::$pear_instance == NULL) {

                $server_config = Environment::get("MAIL_SERVER");

                self::$pear_instance = \Mail::factory('smtp',
                    [
                        'host' => $server_config['Host'],
                        'port' => $server_config['Port'],
                        'auth' => true,
                        'username' => $server_config['Username'],
                        'password' => $server_config['Password']
                    ]);

            }
    }

    public static function send($subject, $target, $body, $flag = NULL)
    {

        $target = "<" . $target . ">";
        $sender = "<" . Environment::get("PROJECT", "Name") . ">";

        if ($flag == self::HTML_Header) {
            $headers = [
                'From' => $sender,
                'To' => $target,
                'Subject' => $subject,
                'MIME-Version' => 1,
                'Content-type' => 'text/html;charset=utf-8'
            ];
        } else {
            $headers = [
                'From' => $sender,
                'To' => $target,
                'Subject' => $subject,
            ];
        }


        self::initialize();
        $result = self::$pear_instance->send($target, $headers, $body);

        if (\PEAR::isError($result)) {
            return $result->getMessage();
        } else {
            return TRUE;
        }

    }

}