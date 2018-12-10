<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 10.12.2018
 * Time: 23:14
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AllAccommodationModel extends I_Query
{
    protected $m_id_offer;
    protected $m_id_user;
    protected $m_id_promotion;
    protected $m_promotion_name;
    protected $m_promotion_reduction;
    protected $m_price_per_day;
    protected $m_promotion_price_per_day;
    protected $m_title;
    protected $m_description;
    protected $m_best;
    protected $m_date_validity_from;
    protected $m_date_validity_to;

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


    //GETS
    public function getSize()
    {
        return count($this->m_id_offer);
    }

    public function getIdOffer($id)
    {
        return $this->m_id_offer[$id];
    }

    public function getIdUser($id)
    {
        return $this->m_id_user[$id];
    }

    public function getIdPromotion($id)
    {
        return $this->m_id_promotion[$id];
    }

    public function getPromotionName($id)
    {
        return $this->m_promotion_name[$id];
    }

    public function getPromotionReduction($id)
    {
        return $this->m_promotion_reduction[$id];
    }

    public function getPricePerDay($id)
    {
        return $this->m_price_per_day[$id];
    }

    public function getPromotionPricePerDay($id)
    {
        return $this->m_promotion_price_per_day[$id];
    }

    public function getTitle($id)
    {
        return $this->m_title[$id];
    }

    public function getDescription($id)
    {
        return $this->m_description[$id];
    }

    public function getBest($id)
    {
        return $this->m_best[$id];
    }

    public function getDateValidityFrom($id)
    {
        return $this->m_date_validity_from[$id];
    }

    public function getDateValidityTo($id)
    {
        return $this->m_date_validity_to[$id];
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
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