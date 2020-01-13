<?php
/** Red Framework Controller
 * Generated By Red Analytics
 *
 * Date: 10/01/2020
 * Time: 04:08:15
 * @author RedCoder, RT3N
 */

namespace App\Controllers;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use Red\Base\Controller;
use Red\ValidateService\Validate;

class WhoisController extends Controller
{
    private $api_service = "http://api.guardiran.org/cloudhound/";
    private $hostname;
    private $connection;
    private $data;

    public function whois($hostname)
    {

        $hostname = str_replace("https://", "", $hostname);
        $hostname = str_replace("http://", "", $hostname);
        $hostname = str_replace("www.", "", $hostname);
        $hostname = trim($hostname, "/");

        $this->hostname = $hostname;


        if (!Validate::validate($hostname, "required|method:domain|limit:1-63") && !Validate::validate($hostname, "required|method:IP|limit:0-0")){
            echo PHP_EOL . "    \e[1;31;40m[-] Wrong Hostname !\e[1;37;40m" . PHP_EOL;
            return false;
        }

        if (!$this->isConnected()) {
            echo PHP_EOL . "    \e[1;31;40m[-] No Internet Connection !\e[1;37;40m" . PHP_EOL;
            return false;
        }

        if (!$this->serverUp()) {
            echo PHP_EOL . "    \e[1;31;40m[-] API Server is Down Temporary , Please Try Again Later .\e[1;37;40m" . PHP_EOL;
            return false;
        }

        $result = $this->fetchData($hostname);

        if ($result !== "false" && $result !== false) {
            if ($this->data["status"] == "success") {

                if ($this->data['query'] == null || $this->data['query'] == false) {
                    $ip = "-";
                } else {
                    $ip = $this->data["query"];
                }

                if ($this->data["org"] == null || $this->data["org"] == false) {
                    $org = "-";
                } else {
                    $org = $this->data["org"];
                }

                if ($this->data["country"] == null || $this->data["country"] == false) {
                    $country = "-";
                } else {
                    $country = $this->data["country"];
                }

                if ($this->data["city"] == null || $this->data["city"] == false) {
                    $city = "-";
                } else {
                    $city = $this->data["city"];
                }

                if ($this->data["isp"] == null || $this->data["isp"] == false) {
                    $isp = "-";
                } else {
                    $isp = $this->data["isp"];
                }

                echo "
        \e[1;31;40m[+] IP \e[1;36;40m: " . $ip . "
    
        \e[1;31;40m[+] Organization \e[1;36;40m: " . $org . "
                
        \e[1;31;40m[+] Country \e[1;36;40m: " . $country . "
                
        \e[1;31;40m[+] City \e[1;36;40m: " . $city . "
                
        \e[1;31;40m[+] ISP \e[1;36;40m: " . $isp . "\e[1;37;40m" . PHP_EOL;

                return true;
            } else {
                echo PHP_EOL . "    \e[1;31;40m[-] Whois Lookup Failed - Check Your Hostname Again !\e[1;37;40m" . PHP_EOL;
                return false;
            }

        } else {
            echo PHP_EOL . "    \e[1;31;40m[-] Whois Lookup Failed - Check Your Hostname Again !\e[1;37;40m" . PHP_EOL;
            return false;
        }


    }


    public function fetchData($hostname)
    {

        $this->connection = new Client(['base_uri' => $this->api_service]);

        $headers = ["content-type" => "application/json;charset=UTF-8",
            "API" => "guardiran"];


        try {
            $this->data = $this->connection->request('GET', "v1/whois/" . $hostname,
                ['headers' => $headers]);
        } catch (BadResponseException $e) {
            $this->data = "false";
        }


        if ($this->data == "false") {
            return false;
        } else {
            $this->data = $this->data->getBody();
            $this->data = json_decode($this->data, true);
            return $this->data;
        }

    }


    public function getOrganization()
    {
        if(isset($this->data['org'])){
            return $this->data['org'];
        } else {
            return false;
        }
    }


    public function isConnected()
    {
        $connected = @fsockopen("www.google.com", 80);
        if ($connected) {
            fclose($connected);
            return true;
        }
        return false;
    }


    public function serverUp()
    {
        if ($this->isConnected()) {
            $connected = @fsockopen("www.guardiran.org", 80);
            if ($connected) {
                fclose($connected);
                return true;
            }
            return false;
        }
    }


}