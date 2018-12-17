<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 17.12.2018
 * Time: 21:03
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AccommodationByIdModel extends I_Query
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
    protected $m_active;
    protected $m_confirmation;
    protected $m_images;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL get_offer_by_id(". $this->m_id_offer .");";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
    public function getIdOffer()
    {
        return $this->m_id_offer;
    }

    public function getIdUser()
    {
        return $this->m_id_user;
    }

    public function getIdPromotion()
    {
        return $this->m_id_promotion;
    }

    public function getPromotionName()
    {
        return $this->m_promotion_name;
    }

    public function getPromotionReduction()
    {
        return $this->m_promotion_reduction;
    }

    public function getPricePerDay()
    {
        return $this->m_price_per_day;
    }

    public function getPromotionPricePerDay()
    {
        return $this->m_promotion_price_per_day;
    }

    public function getTitle()
    {
        return $this->m_title;
    }

    public function getDescription()
    {
        return $this->m_description;
    }

    public function getBest()
    {
        return $this->m_best;
    }

    public function getDateValidityFrom()
    {
        return $this->m_date_validity_from;
    }

    public function getDateValidityTo()
    {
        return $this->m_date_validity_to;
    }

    public function getActive()
    {
        return $this->m_active;
    }

    public function getConfirmation()
    {
        return $this->m_confirmation;
    }

    public function getImages()
    {
        return $this->m_images;
    }


    //SET
    public function setIdOffer($id_offer)
    {
        $this->m_id_offer = $id_offer;
        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC);

        DebugLog::console_log("Query result:", $one_row_of_result_query);

        $this->m_id_user                   = $one_row_of_result_query['id_user'];
        $this->m_id_promotion              = $one_row_of_result_query['id_promotion'];
        $this->m_promotion_name            = $one_row_of_result_query['name'];
        $this->m_promotion_reduction       = $one_row_of_result_query['price_reduction'];
        $this->m_price_per_day             = $one_row_of_result_query['price_per_day'];
        $this->m_promotion_price_per_day   = $one_row_of_result_query['promotion_price_per_day'];
        $this->m_title                     = $one_row_of_result_query['title'];
        $this->m_description               = $one_row_of_result_query['description'];
        $this->m_best                      = $one_row_of_result_query['best'];
        $this->m_date_validity_from        = $one_row_of_result_query['date_validity_from'];
        $this->m_date_validity_to          = $one_row_of_result_query['date_validity_to'];
        $this->m_active                    = $one_row_of_result_query['active'];
		$this->m_confirmation              = $one_row_of_result_query['confirmation'];
        $this->m_images = "";

		//TODO: dodanie pobieranie zdjęcia oraz generacja kodu HTML z nimi
        //TODO: zdjęcia będa z Base64 - trzeba dekodować

        $statement->closeCursor();
    }


    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }

}