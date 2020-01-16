<p style="text-align: center;"><img src="https://development.guardiran.org/public/Images/cloudhound/cloudhound_logo.png" alt="CloudHound" /></p>
  
                                                                                           
## CloudHound - Intro

[CloudHound][1] is a Console Application Written in PHP Which allows Attacker to Bypass ~~CloudFlare~~ System and Detect Original Server .
in order to do this, CloudHound use Several Private Methods Such as Cross-Site Port Attacking, Query in Guardiran DNS History wide-range Database and etc


## Usage

Run `application` File in Root Folder via PHP or open terminal and use `php application` Command ; application file is bootstrapper of this script .

Since App is Running You have 2 Operational Commands in front of your Self :


        [+] detect guardiran.org

        [+] whois guardiran.org

        [+] help

        [+] exit


#### detect
> detect command receives 1 parameter and it should be target hostname ;
> after executing this commands system runs several methods to analyze and detect the target .

#### whois
> whois command recives 1 parameter and it should be target hostname or IP ;
> it will gather info about registered domain for attacker .

## Methods

| Method | Description |
| ------ | ----------- |
| DNS History   | Using Guardiran Private Database (Up to +2M Records) to Detect Original IP |
| Cross-Site Port Attack    | Wide-Range XSPA |
| Subdomain Scan    | +10K Common Subdomains (Scan Range is Pretty Flexible) |

## Credits

* **RedCoder** (Admin in Guardiran Sec. Team)
* **RT3N** (Admin in Guardiran Sec. Team)

## About Us

<div><img src="https://guardiran.org/uploads/logo.png" alt="Guardiran Sec. Team" /></div>

We are Guardiran Security Team
\
https://www.guardiran.org 

[1]: https://github.com/guardiran/cloudhound
