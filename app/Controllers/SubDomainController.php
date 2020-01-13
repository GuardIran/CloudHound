<?php
/** Red Framework Controller
 * Generated By Red Analytics
 *
 * Date: 10/01/2020
 * Time: 03:14:32
 * @author RedCoder, RT3N
 */

namespace App\Controllers;


use Red\Base\Controller;
use App\Models\SubDomainModel;

class SubDomainController extends Controller
{

    private $hostname;
    private $subdomains;
    private $sqlite_extension;
    private $connection_status;
    /**
     * @var SubDomainModel $this ->model
     */

    protected $model;

    public function __construct($hostname)
    {

        if (!extension_loaded('pdo_sqlite') && !extension_loaded('php_pdo_sqlite')) {
            $this->sqlite_extension = false;
            return;
        } else {
            $this->sqlite_extension = true;
        }

        parent::__construct();
        $this->hostname = $hostname;
        $this->subdomains = $this->model->fetchSubDomainList();

    }

    public function subDomainCheckUp($hostname)
    {

        if ($this->sqlite_extension == false) {
            echo PHP_EOL . PHP_EOL . "    \e[1;31;40m[!] pdo_sqlite Extension is Disabled On your PHP Server ." . PHP_EOL . PHP_EOL .
                "\tin Order to Access Subdomain List from SQLite Database you Have to Go to PHP Folder and Modify php.ini" . PHP_EOL .
                "\tAnd add the Following Line :" . PHP_EOL .
                "\textension=pdo_sqlite" . PHP_EOL;
            return false;
        }


        $sub_domain_count = count($this->subdomains);
        $trigger = 100;
        $i = 0;
        $this->counter($i, $sub_domain_count);

        foreach ($this->subdomains as $val) {

            if ($i == $trigger) {
                $this->counter($i, $sub_domain_count);
                $trigger += 100;
            }

            if ($sub_domain_count - 1 == $i) {
                $this->counter($i, $sub_domain_count);

                if (!$this->isConnected()) {
                    $this->connection_status = false;
                } else {
                    $this->connection_status = true;
                }
            }

            $url = $val['subdomain'] . "." . $hostname;

            $dns_records = @dns_get_record($url, DNS_A);

            if ($dns_records) {
                if (isset($dns_records[0]['ip'])) {

                    $cloudflare_status = ProtectionController::cloudFlareIP($dns_records[0]['ip']);

                    if ($cloudflare_status) {
                        continue;
                    } else if ($cloudflare_status == "private_ip") {
                        $dns_records['server_ip'] = "Private IP (" . $dns_records[0]['ip'] . ")";
                        $dns_records['subdomain'] = $url;
                        return $dns_records;
                    } else if ($cloudflare_status == false) {
                        $dns_records['server_ip'] = $dns_records[0]['ip'];
                        $dns_records['subdomain'] = $url;
                        return $dns_records;
                    }
                }
            }


            $i++;
        }

        return false;
    }

    public function counter($counter, $total)
    {

        $text = "
    [" . $counter . "/ " . $total . "] \r
    ";

        echo $text;

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

    /**
     * @return mixed
     */
    public function getConnectionStatus()
    {
        return $this->connection_status;
    }

}