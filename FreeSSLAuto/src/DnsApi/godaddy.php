<?php

/**
 * PHP version 5.4.
 *
 * @author     Anindya Sundar Mandal <anindya@SpeedUpWebsite.info>
 * @copyright  2018 Anindya Sundar Mandal
 * @license    https://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause "New" or "Revised" License
 *
 * @version    1.0.0
 *
 * @see       https://SpeedUpWebsite.info
 * @since      Class available since Release 1.0.0
 */

namespace FreeSslDotTech\FreeSSLAuto\DnsApi;

use FreeSslDotTech\FreeSSLAuto\Admin\Factory as AdminFactory;
use FreeSslDotTech\FreeSSLAuto\Logger;

class godaddy
{
    private $api_base = 'https://api.godaddy.com';
    private $api_key;
    private $api_secret;

    /**
     * Initiates the godaddy class.
     *
     * @param array $provider
     */
    public function __construct($provider)
    {
        $adminFactory = new AdminFactory();

        $this->api_key = $provider['api_identifier'];
        $this->api_secret = $adminFactory->decryptText($provider['api_credential']);
        $this->logger = new Logger();
    }

    /**
     * Set DNS TXT record.
     *
     * @param string $domain
     * @param string $txt_name
     * @param string $txt_value
     *
     * @return array
     */
    public function setTxt($domain, $txt_name, $txt_value)
    {
        $data = [
            [
                'data' => $txt_value,
                'ttl' => 600,
            ],
        ];

        $curl = new DnsCurl('GoDaddy', $this->api_key, $this->api_secret);

        //Add or Replace (if $txt_name already exist) the DNS Records for the specified Domain with the specified Type and Name
        $result = $curl->connect('PUT', $this->api_base.'/v1/domains/'.$domain.'/records/TXT/'.$txt_name, $data);

        //Check status code
        if (200 === $result['http_code']) {
            //Success
            $this->logger->log('Congrats! TXT record added successfully.');
        }

        //Add new record -  but this does NOT replace
        //$result = $curl->connect('PATCH', $this->api_base."/v1/domains/".$domain."/records", $data);

        return $result;
    }

    /**
     * Fetch all DNS records.
     *
     * @param string $domain
     *
     * @return array
     */
    public function fetchAll($domain)
    {
        $curl = new DnsCurl('GoDaddy', $this->api_key, $this->api_secret);
        //Fetch all record
        return $curl->connect('GET', $this->api_base."/v1/domains/${domain}/records");
    }
}
