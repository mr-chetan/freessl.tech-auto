<?php

/**
 * PHP version 5.4
 *
 * @author     Anindya Sundar Mandal <anindya@SpeedUpWebsite.info>
 * @copyright  2018 Anindya Sundar Mandal; Copyright (c) 2015, Stanislav Humplik <sh@analogic.cz>
 * @license    https://opensource.org/licenses/BSD-3-Clause  BSD 3-Clause "New" or "Revised" License
 * @version    1.0.0
 * @link       https://SpeedUpWebsite.info
 * @since      Class available since Release 1.0.0
 *
 */

namespace FreeSslDotTech\FreeSSLAuto\Acme;

use FreeSslDotTech\FreeSSLAuto\Logger;

class Client implements ClientInterface
{
    public $lastHeader;

    private $lastCode;
    private $base;
    private $logger;

    public function __construct($base)
    {
        $this->base = $base;
        $this->logger = new Logger();
    }

    public function curl($method, $url, $data = null)
    {
        $headers = ['Accept: application/json', 'Content-Type: application/jose+json'];
        $handle = curl_init();
        curl_setopt($handle, CURLOPT_URL, preg_match('~^https~', $url) ? $url : $this->base.$url);
        curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_HEADER, true);

        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                curl_setopt($handle, CURLOPT_POST, true);
                curl_setopt($handle, CURLOPT_POSTFIELDS, $data);

                break;
        }
        $response = curl_exec($handle);

        if (curl_errno($handle)) {
            throw new \RuntimeException('Curl: '.curl_error($handle));
        }

        $header_size = curl_getinfo($handle, CURLINFO_HEADER_SIZE);

        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        $this->lastHeader = $header;
        $this->lastCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);

        $data = json_decode($body, true);

        $result = null === $data ? $body : $data;

        //Valid / expected status code
        $expected_status_codes = [200, 201, 202, 204];

        //Check status code
        if (!\in_array($this->lastCode, $expected_status_codes, true)) {
            //Failed
            $this->logger->log("Sorry, Let's Encrypt server response (".$this->lastCode.') is unexpected. Complete server response given below.');
            echo '<pre>';
            print_r($result);
            echo '</pre>';
        }

        return $result;
    }

    public function post($url, $data)
    {
        return $this->curl('POST', $url, $data);
    }

    public function get($url)
    {
        return $this->curl('GET', $url);
    }

    //get Let's Encrypt API URLs
    public function getUrl($key)
    {
        $dir_array = $this->get($this->base.'/directory');

        return $dir_array[$key];
    }

    public function getLastNonce()
    {
        if (preg_match('~Replay\-Nonce: (.+)~i', $this->lastHeader, $matches)) {
            return trim($matches[1]);
        }

        $this->curl('GET', '/directory');

        return $this->getLastNonce();
    }

    public function getLastLocation()
    {
        if (preg_match('~Location: (.+)~i', $this->lastHeader, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public function getLastCode()
    {
        return $this->lastCode;
    }

    public function getLastLinks()
    {
        preg_match_all('~Link: <(.+)>;rel="up"~', $this->lastHeader, $matches);

        return $matches[1];
    }
}
