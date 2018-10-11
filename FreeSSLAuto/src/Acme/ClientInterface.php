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

interface ClientInterface
{
    /**
     * Constructor.
     *
     * @param string $base the ACME API base all relative requests are sent to
     */
    public function __construct($base);

    /**
     * Send a POST request.
     *
     * @param string $url  URL to post to
     * @param array  $data fields to sent via post
     *
     * @return array|string the parsed JSON response, raw response on error
     */
    public function post($url, $data);

    /**
     * @param string $url URL to request via get
     *
     * @return array|string the parsed JSON response, raw response on error
     */
    public function get($url);

    /**
     * Returns the Replay-Nonce header of the last request.
     *
     * if no request has been made, yet. A GET on $base/directory is done and the
     * resulting nonce returned
     *
     * @return mixed
     */
    public function getLastNonce();

    /**
     * Return the Location header of the last request.
     *
     * returns null if last request had no location header
     *
     * @return null|string
     */
    public function getLastLocation();

    /**
     * Return the HTTP status code of the last request.
     *
     * @return int
     */
    public function getLastCode();

    /**
     * Get all Link headers of the last request.
     *
     * @return string[]
     */
    public function getLastLinks();
}
