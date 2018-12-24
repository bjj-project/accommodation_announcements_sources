<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 23.12.2018
 * Time: 13:04
 */


namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class ChangeClientDataModel extends I_Query
{
    private $m_id_client;
    private $m_name;
    private $m_surname;
    private $m_mail;
    private $m_password;

    private $m_was_ok;
    private $m_code;
    private $m_message;

    //SET QUERY
    private function prepareQuery()
    {
        //jeśli parametr jest NULL wtedy nie podlega zmian
        $this->m_sql_query_text = "CALL modification_user(". $this->m_id_client .", NULL, '". $this->m_name ."', '". $this->m_surname ."', '". $this->m_mail ."', '". $this->m_password ."')";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
    public function getWasOk()
    {
        return $this->m_was_ok;
    }

    public function getErrorCode()
    {
        return $this->m_code;
    }

    public function getErrorMessage()
    {
        return $this->m_message;
    }

    //SETS
    public function setIdClient($id_client)
    {
        $this->m_id_client = $id_client;

        $this->prepareQuery();
    }

    public function setName($name)
    {
        $this->m_name = $name;

        $this->prepareQuery();
    }

    public function setSurname($surname)
    {
        $this->m_surname = $surname;

        $this->prepareQuery();
    }

    public function setMail($mail)
    {
        $this->m_mail = $mail;

        $this->prepareQuery();
    }

    public function setPassword($password)
    {
        $this->m_password = $password;

        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->m_was_ok = $one_row_of_result_query['was_ok'];
        $this->m_code = $one_row_of_result_query['code'];
        $this->m_message = $one_row_of_result_query['message'];

        $statement->closeCursor();
    }


    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}