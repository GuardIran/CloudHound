<?php
/** RedCoder Framework
 * Bootstrap Class
 *
 * This Class Will Run Application
 * @author RedCoder
 * http://redframework.ir
 */

namespace App\Bootstrap;

use Red\CommanderService\Commander;
use Red\FilterService\Filter;
use Red\Output\Output;
use Red\EnvironmentProvider\Environment;
use Red\Red;
use Red\SanitizeService\Sanitize;
use Red\ValidateService\Validate;

/**
 * Class Bootstrap
 * @package App
 */
class Bootstrap
{
    public static $start_time;
    private static $singleton = 0;

    public function __construct()
    {
        if (self::$singleton != 0) {
            if (Environment::get('DEBUG', 'Errors') == 'on') {
                http_response_code(500);
                Output::printC("Singleton Class 'Bootstrap' instanced twice !");
                exit();
            } else {
                http_response_code(500);
                Output::printC("Some Error Occurred");
                exit();
            }
        } else {
            self::$singleton = 1;
        }

        self::$start_time = microtime(true);

        Environment::initialize();

        if (Red::getPhpConfig() === TRUE) {
            require_once ROOT_PATH . 'config' . DS . 'config.php';
        }

        return TRUE;
    }

    public function run()
    {

        if (Environment::get('DEBUG', 'Errors') == 'on') {
            //Errors

            ini_set('display_errors', 'on');
            error_reporting(E_ALL);
        } else {
            ini_set('display_errors', 'off');
        }

        date_default_timezone_set(Environment::get('PROJECT', 'Timezone'));

        // Set Up Custom Validation Roles
        Validate::initialize();

        // Set Up Custom Sanitization Roles
        Sanitize::initialize();

        // Set Up Custom Filter Roles
        Filter::initialize();

        require_once ROOT_PATH . 'commands' . DS . 'commands.php';

        $application_theme = Output::light_cyan;

        Commander::initialize($application_theme);

    }

}
