<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 27.12.2018
 * Time: 18:56
 */
 
namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class MakeAccommodationModel extends I_Query
{
    protected $m_id_user;
    protected $m_id_promotion;
    protected $m_title;
    protected $m_description;
    protected $m_cost_per_day;
    protected $m_date_validity_from;
    protected $m_date_validity_to;

    protected $m_was_ok;
    protected $m_error_code;
    protected $m_error_message;

	//SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL create_offer(".$this->m_id_user.", ".$this->m_id_promotion.", '".$this->m_title."', '".$this->m_description."', ".$this->m_cost_per_day.", '".$this->m_date_validity_from."', '".$this->m_date_validity_to."');";
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

    public function setIdPromotion($id_promotion)
    {
        $this->m_id_promotion = $id_promotion;
        $this->prepareQuery();
    }

    public function setTitle($title)
    {
        $this->m_title = $title;
        $this->prepareQuery();
    }

    public function setDescription($description)
    {
        $this->m_description = $description;
        $this->prepareQuery();
    }

    public function setCostPerDay($cost_per_day)
    {
        $this->m_cost_per_day = $cost_per_day;
        $this->prepareQuery();
    }

    public function setDateValidityFrom($date)
    {
        $this->m_date_validity_from = $date;
        $this->prepareQuery();
    }

    public function setDateValidityTo($date)
    {
        $this->m_date_validity_to= $date;
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