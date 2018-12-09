<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 09.12.2018
 * Time: 00:33
 */

namespace App\Helper;

class DebugLog
{
    public static function console_log($message, $data = '')
    {
        echo '<script>';
        echo 'console.log("'. $message . ' " + ' . json_encode( $data ) .')';
        echo '</script>';
    }

    public static function message_log($message, $data = '')
    {
        echo '<script>';
        echo 'alert( "'. $message . ' " + ' . json_encode( $data ) .')';
        echo '</script>';
    }
}