<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 10.12.2018
 * Time: 23:38
 */
namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AccommodationByClientModel extends AllAccommodationModel
{
    private $id_user_by;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "";
    }


    //CONSTRUCTOR
    public function __construct()
    {

        $this->prepareQuery();

        $this->m_id_offer = array();
        $this->m_id_user = array();
        $this->m_id_promotion = array();
        $this->m_promotion_name = array();
        $this->m_promotion_reduction = array();
        $this->m_price_per_day = array();
        $this->m_promotion_price_per_day = array();
        $this->m_title = array();
        $this->m_description = array();
        $this->m_best = array();
        $this->m_date_validity_from = array();
        $this->m_date_validity_to = array();
    }

    //SET
    public function SetUserBy($user_by)
    {
        $this->id_user_by = $user_by;
    }


    //GET - from base class


    //SET FROM QUERY RESULT
    final public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        //TODO: set from database

        DebugLog::console_log("Query result:", $one_row_of_result_query);
    }


    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }

}