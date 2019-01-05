<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 23.12.2018
 * Time: 12:15
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class ClientDataByIdModel extends I_Query
{
    private $m_id_client;
    private $m_id_permission;
    private $m_permission_name;
    private $m_name;
    private $m_surname;
    private $m_mail;
    private $m_password;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL get_user_by_id(". $this->m_id_client .");";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
    public function getIdClient()
    {
        return $this->m_id_client;
    }

    public function getIdPermission()
    {
        return $this->m_id_permission;
    }

    public function getPermissionName()
    {
        return $this->m_permission_name;
    }

    public function getName()
    {
        return $this->m_name;
    }

    public function getSurname()
    {
        return $this->m_surname;
    }

    public function getEmail()
    {
        return $this->m_mail;
    }

    public function getPassword()
    {
        return $this->m_password;
    }


    //SETS
    public function setIdClient($id_client)
    {
        $this->m_id_client = $id_client;

        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->setDate($one_row_of_result_query);

        $statement->closeCursor();
    }

    public function setDate($date)
    {
        if (array_key_exists('id_user', $date)) {
            $this->m_id_client = $date['id_user'];
        }

        if (array_key_exists('id_permission', $date)) {
            $this->m_id_permission = $date['id_permission'];
        }

        if (array_key_exists('permission_name', $date)) {
            $this->m_permission_name = $date['permission_name'];
        }

        if (array_key_exists('name', $date)) {
            $this->m_name = $date['name'];
        }

        if (array_key_exists('surname', $date)) {
            $this->m_surname = $date['surname'];
        }

        if (array_key_exists('mail', $date)) {
            $this->m_mail = $date['mail'];
        }

        if (array_key_exists('password', $date)) {
            $this->m_password = $date['password'];
        }
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}