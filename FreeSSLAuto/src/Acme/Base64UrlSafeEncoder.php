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

class Base64UrlSafeEncoder
{
    public static function encode($input)
    {
        return str_replace('=', '', strtr(base64_encode($input), '+/', '-_'));
    }

    public static function decode($input)
    {
        $remainder = \strlen($input) % 4;
        if ($remainder) {
            $padlen = 4 - $remainder;
            $input .= str_repeat('=', $padlen);
        }

        return base64_decode(strtr($input, '-_', '+/'), true);
    }
}
