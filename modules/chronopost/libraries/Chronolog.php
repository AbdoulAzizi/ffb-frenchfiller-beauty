<?php

/**
 * 2008-2018 Chronopost SAS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to chronopost@adexos.fr so we can send you a copy immediately.
 *
 * @author    Adexos for Chronopost <chronopost@adexos.fr>
 * @copyright 2008-2018 Chronopost SAS
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

// Compatibilité avec Chronofood
if (!class_exists('Chronolog')) {

    class Chronolog
    {
        const enabled = false;
        public static $level_value = array(
            0 => 'DEBUG',
            1 => 'INFO',
            2 => 'WARNING',
            3 => 'ERROR',
        );
        const DEBUG = 0;
        const INFO = 1;
        const WARNING = 2;
        const ERROR = 3;
        const filename = 'chrono';


        /**
         * Write the message in the log file
         *
         * @param string message
         * @param level
         *
         * @return bool
         */
        public static function log($message, $level = 0)
        {
            if (!self::enabled) {
                return false;
            }

            if (!is_string($message)) {
                $message = "JSON : " . json_encode($message, true);
            }
            $formatted_message = '*' . self::$level_value[$level] . '* ' . "\t" . date('Y/m/d - H:i:s') . ': ' . $message . "\r\n";

            try {
                file_put_contents(self::getFilename(), $formatted_message, FILE_APPEND);
                return true;
            } catch (Exception $e) {
                // Can't log, but keep quiet
            }

            return false;
        }

        /**
         * @return string
         * @throws Exception
         */
        public static function getFilename()
        {
            $logDirLegacy = realpath(dirname(__FILE__) . '/../../../log') . '/';
            $logDir = realpath(dirname(__FILE__) . '/../../../var/logs') . '/';
            if (!is_writable($logDir)) {
                if (is_writable($logDirLegacy)) {
                    $logDir = $logDirLegacy;
                } else {
                    throw new Exception("Log directory not writable : $logDir");
                }
            }

            return $logDir . self::filename . '-' . date('Y-m-d') . '.log';
        }
    }

}
