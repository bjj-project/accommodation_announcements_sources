<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 08.12.2018
 * Time: 20:28
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class LoginModel extends I_Query
{
    //VARIABLE
    private $m_was_ok;
    private $m_error_message;
    private $m_error_code;
    private $m_id;
    private $m_name;
    private $m_surname;
    private $m_is_admin;
    private $m_email;
    private $m_password;


    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL login ('". $this->m_email ."', '". $this->m_password ."');";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
    public function getId()
    {
        return $this->m_id;
    }

    public function getWasOk()
    {
        return $this->m_was_ok;
    }

    public function getErrorMessage()
    {
        return $this->m_error_message;
    }

    public function getErrorCode()
    {
        return $this->m_error_code;
    }

    public function getName()
    {
        return $this->m_name;
    }

    public function getSurname()
    {
        return $this->m_surname;
    }

    public function getIsAdmin()
    {
        return $this->m_is_admin;
    }

    public function getEmail()
    {
        return $this->m_email;
    }

    public function getPassword()
    {
        return $this->m_password;
    }


    //SETS
    public function setEmail($email)
    {
        $this->m_email = $email;
        $this->prepareQuery();
    }

    public function setPassword($password)
    {
        $this->m_password = $password;
        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    final public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->m_was_ok         = $one_row_of_result_query['was_ok'];
        $this->m_error_message  = $one_row_of_result_query['message'];
        $this->m_error_code     = $one_row_of_result_query['code'];
        $this->m_id             = $one_row_of_result_query['id_user'];
        $this->m_name           = $one_row_of_result_query['name'];
        $this->m_surname        = $one_row_of_result_query['surname'];
        $this->m_is_admin       = $one_row_of_result_query['admin'];

        $statement->closeCursor();
    }

    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}