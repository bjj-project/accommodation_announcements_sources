<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 27.12.2018
 * Time: 18:57
 */
 
namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class MakeBookingModel extends I_Query
{
	protected $m_id_user;
    protected $m_id_offer;
    protected $m_date_from;
    protected $m_date_to;

    private $m_was_ok;
    private $m_error_message;
    private $m_error_code;
	
	//SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL create_reservation(".$this->m_id_user.", ".$this->m_id_offer.", '".$this->m_date_from."', '".$this->m_date_to."');";
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


    //SETS
    public function setIdUser($id_user)
    {
        $this->m_id_user = $id_user;
        $this->prepareQuery();
    }

    public function setIdOffer($id_offer)
    {
        $this->m_id_offer = $id_offer;
        $this->prepareQuery();
    }

    public function setDateFrom($date_from)
    {
        $this->m_date_from = $date_from;
        $this->prepareQuery();
    }

    public function setDateTo($date_to)
    {
        $this->m_date_to = $date_to;
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


	//DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}