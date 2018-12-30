<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 30.12.2018
 * Time: 12:11
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AccommodationConfirmByIdModel extends I_Query
{
    //VARIABLE
    private $m_was_ok;
    private $m_error_message;
    private $m_error_code;
    private $m_id_offer;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL confirm_offer(". $this->m_id_offer .");";
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

    public function getErrorMessage()
    {
        return $this->m_error_message;
    }

    public function getErrorCode()
    {
        return $this->m_error_code;
    }


    //SETS
    public function setIdOffer($id_offer)
    {
        $this->m_id_offer = $id_offer;
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

        $statement->closeCursor();
    }

    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}