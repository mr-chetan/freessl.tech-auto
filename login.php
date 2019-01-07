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

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            die("Unfortunately, this app is not compatible with Windows. It works on Linux hosting.");
        }

        //Display all error
        error_reporting(E_ALL);

        //Set display_errors ON if it is OFF by default
        if (!ini_get('display_errors')) {
            ini_set('display_errors', '1');
        }

        if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50400) {
            die("You need at least PHP 5.4.0\n");
        }

        if (!extension_loaded('openssl')) {
            die("You need OpenSSL extension enabled with PHP\n");
        }

        if (!extension_loaded('curl')) {
            die("You need Curl extension enabled with PHP\n");
        }

        if (!extension_loaded('mysqli')) {
            die("You need Mysqli extension enabled with PHP\n");
        }
        
        if (!ini_get('allow_url_fopen')) {
            die("You need to set PHP directive allow_url_fopen = On. Please contact your web hosting company for help.");
        }

        // Define Directory Separator to make the default DIRECTORY_SEPARATOR short
        define('DS', DIRECTORY_SEPARATOR);

        $config_file_path = __DIR__.DS.'config'.DS.'config.php';

        // Check if wp-config.php has been created
        if (!file_exists($config_file_path)) {
            header('location: install.php');
        }

        //start session
        if (!session_id()) {
            session_start();
        }

        //Include config file
        require_once $config_file_path;

        // Composer autoloading
        include __DIR__.DS.'vendor'.DS.'autoload.php';

        use FreeSslDotTech\FreeSSLAuto\Admin\Layout;
        use FreeSslDotTech\FreeSSLAuto\Admin\Login;

        $layout = new Layout();

        $action = isset($_GET['action']) ? $_GET['action'] : 'login';

        $action = filter_var($action, FILTER_SANITIZE_STRING);

        global $mysqli;

        $login = new Login($config_file_path, $mysqli);

        //add the page as per request
    switch ($action) {
        case 'login':
            $login->login();

            break;
        case 'logout':
            $login->logout();

            break;
        case 'forgot-password':
            $login->forgotPassword();

            break;
        case 'reset-password':
            $login->resetPassword();

            break;
        default:
            $login->pageNotFound();
    }

    $layout->headerLogin();
    $layout->message();
    $layout->footerLogin();
