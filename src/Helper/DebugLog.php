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
        echo 'console.log("' . $message . '");';

        if(is_array($data))
        {
            echo 'console.log("Array");';
            echo 'console.log(' . json_encode( $data ) . ');';
            echo '</script>';
            return;
        }
        else if(is_object($data))
        {
            echo 'console.log("Object");';
            echo 'console.log(' . $data->debug() . ');';
            echo '</script>';
            return;
        }
        else
        {
            echo 'console.log("'. $data .'");';
            echo '</script>';
            return;
        }
    }

    public static function message_log($message, $data = '')
    {
        echo '<script>';
        echo 'alert( "'. $message . ' " + ' . json_encode( $data ) .')';
        echo '</script>';
    }
}