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

        $this->setDate($one_row_of_result_query);

        $statement->closeCursor();
    }

    public function setDate($date)
    {
        if (array_key_exists('id_offer', $date)) {
            $this->m_id_offer = $date['id_offer'];
        }

        if (array_key_exists('id_user', $date)) {
            $this->m_id_user = $date['id_user'];
        }

        if (array_key_exists('id_promotion', $date)) {
            $this->m_id_promotion = $date['id_promotion'];
        }

        if (array_key_exists('name', $date)) {
            $this->m_promotion_name = $date['name'];
        }

        if (array_key_exists('price_reduction', $date)) {
            $this->m_promotion_reduction = $date['price_reduction'];
        }

        if (array_key_exists('price_per_day', $date)) {
            $this->m_price_per_day = $date['price_per_day'];
        }

        if (array_key_exists('promotion_price_per_day', $date)) {
            $this->m_promotion_price_per_day = $date['promotion_price_per_day'];
        }

        if (array_key_exists('title', $date)) {
            $this->m_title = $date['title'];
        }

        if (array_key_exists('description', $date)) {
            $this->m_description = $date['description'];
        }

        if (array_key_exists('best', $date)) {
            $this->m_best = $date['best'];
        }

        if (array_key_exists('date_validity_from', $date)) {
            $this->m_date_validity_from = $date['date_validity_from'];
        }

        if (array_key_exists('date_validity_to', $date)) {
            $this->m_date_validity_to = $date['date_validity_to'];
        }

        if (array_key_exists('active', $date)) {
            $this->m_active = $date['active'];
        }

        if (array_key_exists('confirmation', $date)) {
            $this->m_confirmation = $date['confirmation'];
        }

        $this->m_images = "";

        //TODO: dodanie pobieranie zdjęcia oraz generacja kodu HTML z nimi
        //TODO: zdjęcia będa z Base64 - trzeba dekodować
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }

}