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

class cloudflare
{
    private $api_base = 'https://api.cloudflare.com';
    private $api_email;
    private $api_key;

    /**
     * Initiates the cloudflare class.
     *
     * @param array $provider
     */
    public function __construct($provider)
    {
        $adminFactory = new AdminFactory();

        $this->api_email = $provider['api_identifier'];
        $this->api_key = $adminFactory->decryptText($provider['api_credential']);
        $this->logger = new Logger();
    }

    /**
     * set TXT record.
     *
     * @param string $domain_name
     * @param string $txt_name
     * @param string $txt_value
     *
     * @return array
     */
    public function setTxt($domain_name, $txt_name, $txt_value)
    {
        $data = [
            'type' => 'TXT',
            'name' => $txt_name,
            'content' => $txt_value,
            'ttl' => 600,
        ];

        $curl = new DnsCurl('Cloudflare', $this->api_email, $this->api_key);

        //Get all domains/zones and id
        $result = $curl->connect('GET', $this->api_base.'/client/v4/zones');

        //Remove domain records other than $domain_name
        $domain = array_reduce($result['body']['result'], function ($v, $w) use (&$domain_name) {
            return $v ? $v : ($w['name'] === $domain_name ? $w : false);
        });

        //Get all TXT records for $domain_name
        $result = $curl->connect('GET', $this->api_base.'/client/v4/zones/'.$domain['id'].'/dns_records?type=TXT');

        $txt_record_name = $txt_name.'.'.$domain_name;

        //Remove domain records other than $txt_record_name
        $txt_details = array_reduce($result['body']['result'], function ($v, $w) use (&$txt_record_name) {
            return $v ? $v : ($w['name'] === $txt_record_name ? $w : false);
        });

        if (!$txt_details) {
            //NO data exist for $txt_record_name. Make new TXT data entry
            $result = $curl->connect('POST', $this->api_base.'/client/v4/zones/'.$domain['id'].'/dns_records', $data);
        } else {
            //TXT data exist for $txt_record_name update/replace the record
            $result = $curl->connect('PUT', $this->api_base.'/client/v4/zones/'.$domain['id'].'/dns_records/'.$txt_details['id'], $data);
        }

        //Check status code
        if (200 === $result['http_code']) {
            //Success
            $this->logger->log('Congrats! TXT record added successfully.');
        }

        return $result;
    }

    /**
     * @param string $domain
     *
     * @return string
     */
    public function fetchAll($domain)
    {
        return $domain;
    }
}
