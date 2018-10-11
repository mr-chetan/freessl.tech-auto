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

namespace FreeSslDotTech\FreeSSLAuto;

//you can use any logger according to Psr\Log\LoggerInterface
class Logger
{
    public function __call($name, $arguments)
    {
        echo date('Y-m-d H:i:s')." [${name}] ${arguments[0]}<br />\n";
    }

    public function log($message)
    {
        $this->info($message);
    }
}
