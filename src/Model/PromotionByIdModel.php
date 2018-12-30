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

class PromotionByIdModel extends I_Query
{
    protected $m_id_promotion;
    protected $m_name;
    protected $m_price_reduction;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "TODO";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
    public function getIdPromotion()
    {
        return $this->m_id_promotion;
    }

    public function getName()
    {
        return $this->m_name;
    }

    public function getPriceReduction()
    {
        return $this->m_price_reduction;
    }


    //SETS
    public function setIdPromotion($id_promotion)
    {
        $this->m_id_promotion = $id_promotion;
        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    final public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->setDate($one_row_of_result_query);

        $statement->closeCursor();
    }


    public function setDate($date)
    {
        if (array_key_exists('id_promotion', $date)) {
            $this->m_id_promotion = $date['id_promotion'];
        }

        if (array_key_exists('promotion_name', $date)) {
            $this->m_name = $date['promotion_name'];
        }

        if (array_key_exists('price_reduction', $date)) {
            $this->m_price_reduction = $date['price_reduction'];
        }
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}