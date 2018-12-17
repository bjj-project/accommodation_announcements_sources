<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 10.12.2018
 * Time: 23:49
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class RegistrationModel extends I_Query
{
    //VARIABLE
    private $m_name;
    private $m_surname;
    private $m_mail;
    private $m_password;
    private $m_marketing;
    private $m_rodo;
    private $m_regulations;
    private $m_ip_address_v4;
    private $m_was_ok;
    private $m_code;
    private $m_message;


    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "";
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

    public function getCode()
    {
        return $this->m_code;
    }

    public function getMessage()
    {
        return $this->m_message;
    }


    //SETS
    public function setName($name)
    {
        $this->m_name = $name;
    }

    public function setSurname($surname)
    {
        $this->m_surname = $surname;
    }

    public function setMail($mail)
    {
        $this->m_mail = $mail;
    }

    public function setPassword($password)
    {
        $this->m_password = $password;
    }

    public function setMarketing($marketing)
    {
        $this->m_marketing = $marketing;
    }

    public function setRodo($rodo)
    {
        $this->m_rodo = $rodo;
    }

    public function setRegulations($regulations)
    {
        $this->m_regulations = $regulations;
    }

    public function setIpAddressV4($ip_address_v4)
    {
        $this->m_ip_address_v4= $ip_address_v4;
    }


    //SET FROM QUERY RESULT
    final public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->m_was_ok   = $one_row_of_result_query['was_ok'];
        $this->m_message  = $one_row_of_result_query['message'];
        $this->m_code     = $one_row_of_result_query['code'];

        $statement->closeCursor();
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}