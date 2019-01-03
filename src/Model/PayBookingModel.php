<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 27.12.2018
 * Time: 18:53
 */
 
namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class PayBookingModel extends I_Query
{
	protected $m_id_reservation;
	
	protected $m_was_ok;
    protected $m_error_code;
    protected $m_error_message;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL pay_reservation(". $this->m_id_reservation .");";
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
        return $this->m_error_code;
    }
	
	public function getErrorMessage()
    {
        return $this->m_error_message;
    }


    //SET
    public function setReservationId($reservation_id)
    {
        $this->m_id_reservation = $reservation_id;

        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->m_was_ok         = $one_row_of_result_query['was_ok'];
        $this->m_error_message  = $one_row_of_result_query['message'];
        $this->m_error_code     = $one_row_of_result_query['code'];

        $statement->closeCursor();
    }


    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}