<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 29.12.2018
 * Time: 21:36
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AccommodationListToConfirmModel extends I_Query
{
    //VARIABLE
    private $m_accommodation_to_confirm_list;


    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "SELECT * FROM get_offer_to_confirm;";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();

        $this->m_accommodation_to_confirm_list = array();
    }


    //GETS
    public function getList()
    {
        return $this->m_accommodation_to_confirm_list;
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $id = 0;
        while($one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC))
        {
            DebugLog::console_log("Query result:", $one_row_of_result_query);

            $this->m_accommodation_to_confirm_list[$id] = new AccommodationByIdModel();
            $this->m_accommodation_to_confirm_list[$id]->setDate($one_row_of_result_query);
            $id++;
        }
        $statement->closeCursor();
    }


    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }

}