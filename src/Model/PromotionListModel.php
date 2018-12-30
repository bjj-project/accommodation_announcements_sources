<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 30.12.2018
 * Time: 12:50
 */


namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class PromotionListModel extends I_Query
{
    protected $m_promotion_list;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "SELECT * FROM get_all_promotions;";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();

        $this->m_promotion_list = array();
    }


    //GETS
    public function getList()
    {
        return $this->m_promotion_list;
    }


    //SET FROM QUERY RESULT
    final public function setQueryResult($statement)
    {
        $id = 0;
        while($one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC))
        {
            DebugLog::console_log("Query result:", $one_row_of_result_query);

            $this->m_promotion_list[$id] = new PromotionByIdModel();
            $this->m_promotion_list[$id]->setDate($one_row_of_result_query);
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