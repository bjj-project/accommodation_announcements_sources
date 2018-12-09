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

        if(is_array($data))
        {
            echo 'console.log("' . $message . ' type: Array");';
            echo 'console.log(' . json_encode( $data ) . ');';
        }
        else if(is_object($data))
        {
            echo 'console.log("' . $message . ' type: Object");';
            echo 'console.log(' . $data->debug() . ');';
        }
        else
        {
            echo 'console.log("' . $message . ' ' . $data . '");';
        }

        echo '</script>';
    }

    public static function message_log($message, $data = '')
    {
        echo '<script>';
        echo 'alert( "'. $message . ' " + ' . json_encode( $data ) .')';
        echo '</script>';
    }
}