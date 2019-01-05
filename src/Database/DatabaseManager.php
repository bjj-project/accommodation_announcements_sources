<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 08.12.2018
 * Time: 19:45
 */

namespace App\Database;

use \PDO;
use \PDO\PDOException;

use App\Helper\DebugLog;

class DatabaseManager
{
    public $m_connection;
    public $m_is_connected = false;
    public $m_last_error = '';

    public function __construct($container)
    {
        $was_ok = $this->connect($container);
        if(false === $was_ok)
        {
            echo $this->getLastError();
        }
    }

    public function connect($container)
    {
        $driver = $container->getParameter('database_driver');
        $host = $container->getParameter('database_host');
        $port = $container->getParameter('database_port');
        $db_name = $container->getParameter('database_name');
        $connection_string = $driver . ':' . 'dbname=' . $db_name . ';host=' . $host . ';port=' . $port.';charset=utf8';
        $password = $container->getParameter('database_password');
        $login = $container->getParameter('database_user');

        DebugLog::console_log('Connect string:', $connection_string);
        DebugLog::console_log('Password', $password);
        DebugLog::console_log('Login', $login);

        try
        {
            $this->m_connection = new PDO($connection_string, $login, $password);
            $this->m_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->m_is_connected = true;
        }
        catch(PDOException $e)
        {
            $this->m_is_connected = false;
            $this->m_last_error = "Connection failed:" . $e->getMessage();
            DebugLog::console_log('Last error:', $this->m_last_error);
        }

        return $this->m_is_connected;
    }

    public function isConnected()
    {
        return $this->m_is_connected;
    }

    public function close()
    {
        $this->m_connection = null;
        $this->m_is_connected = false;
        $this->m_last_error = "";
    }

    public function getLastError()
    {
        return $this->m_last_error;
    }

    public function execQuery(&$query)
    {
        if(false == $this->m_is_connected)
        {
            return false;
        }

        try
        {
            DebugLog::console_log('Query:', $query->getQueryText());
            $statement = $this->m_connection->prepare($query->getQueryText());
            if (false == $statement->execute()) {
                $this->m_last_error = $statement->errorInfo();
                DebugLog::console_log('Error:', $this->m_last_error);
                return false;
            }
        }
        catch (PDOException $pdo_exception)
        {
            $this->m_last_error = $pdo_exception->getMessage();
            DebugLog::console_log('Error:', $pdo_exception->getMessage());
            return false;
        }
        catch (Exception $e)
        {
            $this->m_last_error = $e->getMessage();
            DebugLog::console_log('Error:', $e->getMessage());
            return false;
        }

        $query->setQueryResult($statement);
        return true;
    }
}