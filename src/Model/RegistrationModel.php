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
        $this->m_sql_query_text = "CALL registration('". $this->m_name ."', '". $this->m_surname ."', '". $this->m_mail ."', '". $this->m_password ."', ". $this->m_marketing .", ". $this->m_rodo .", ". $this->m_regulations .", '". $this->m_ip_address_v4 ."'); ";
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

    public function setMarketing($marketing)
    {
        if($marketing)
        {
            $this->m_marketing = 'true';
        }
        else
        {
            $this->m_marketing = 'false';
        }

        $this->prepareQuery();
    }

    public function setRodo($rodo)
    {
        if($rodo)
        {
            $this->m_rodo = 'true';
        }
        else
        {
            $this->m_rodo = 'false';
        }

        $this->prepareQuery();
    }

    public function setRegulations($regulations)
    {
        if($regulations)
        {
            $this->m_regulations = 'true';
        }
        else
        {
            $this->m_regulations = 'false';
        }

        $this->prepareQuery();
    }

    public function setIpAddressV4($ip_address_v4)
    {
        $this->m_ip_address_v4= $ip_address_v4;
        $this->prepareQuery();
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