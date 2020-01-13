<?php


/*
 |--------------------------------------------------------------------------
 | Register Commands
 |--------------------------------------------------------------------------
 |
 | Example
 | Commander::register("command param1 ?param2 ?param3", , "MainController@Run")
 |
 | Call Closure Object
 | Commander::register("print name", function($name) {
 | echo 'Your Name:' . $name;
 | });
 |
 |
 */


use Red\CommanderService\Commander;


Commander::register("detect guardiran.org", "DetectController@detect");
Commander::register("whois guardiran.org", "WhoisController@whois");
Commander::register("help", "HelpController@help");
Commander::register("exit", function () {
    exit(927);
});


