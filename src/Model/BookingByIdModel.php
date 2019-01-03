<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 27.12.2018
 * Time: 18:49
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class BookingByIdModel extends I_Query
{
	protected $m_id_reservation;
	protected $m_id_user;
	protected $m_id_offer;
	protected $m_title;
	protected $m_id_promotion;
	protected $m_promotion_name;
	protected $m_price_reduction;
	protected $m_creation_date;
	protected $m_date_from;
	protected $m_date_to;
	protected $m_to_pay;
	protected $m_paid;
	protected $m_payment_name;
	
	
	//SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }


    //GETS
	public function getIdReservation()
	{
		return $this->m_id_reservation;
	}
	
	public function getIdUser()
	{
		return $this->m_id_user;
	}
	
	public function getIdOffer()
	{
		return $this->m_id_offer;
	}
	
	public function getTitle()
	{
		return $this->m_title;
	}
	
	public function getIdPromotion()
	{
		return $this->m_id_promotion;
	}
	
	public function getPromotionName()
	{
		return $this->m_promotion_name;
	}
	
	public function getPriceReduction()
	{
		return $this->m_price_reduction;
	}
	
	public function getCreationDate()
	{
		return $this->m_creation_date;
	}
	
	public function getDateFrom()
	{
		return $this->m_date_from;
	}
	
	public function getDateTo()
	{
		return $this->m_date_to;
	}
	
	public function getToPay()
	{
		return $this->m_to_pay;
	}
	
	public function getPaid()
	{
		return $this->m_paid;
	}
	
	public function getPaymentName()
	{
		return $this->m_payment_name;
	}
	
	
	//SETS
	public function setIdReservation($id_reservation)
	{
		$this->m_id_reservation = $id_reservation;
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
        if (array_key_exists('id_reservation', $date)) {
            $this->m_id_reservation = $date['id_reservation'];
        }
		
		if (array_key_exists('id_user', $date)) {
            $this->m_id_user = $date['id_user'];
        }
		
		if (array_key_exists('id_offer', $date)) {
            $this->m_id_offer = $date['id_offer'];
        }
		
		if (array_key_exists('title', $date)) {
            $this->m_title = $date['title'];
        }
		
		if (array_key_exists('id_promotion', $date)) {
            $this->m_id_promotion = $date['id_promotion'];
        }
		
		if (array_key_exists('promotion_name', $date)) {
            $this->m_promotion_name = $date['promotion_name'];
        }
		
		if (array_key_exists('price_reduction', $date)) {
            $this->m_price_reduction = $date['price_reduction'];
        }
		
		if (array_key_exists('creation_date', $date)) {
            $this->m_creation_date = $date['creation_date'];
        }
		
		if (array_key_exists('date_from', $date)) {
            $this->m_date_from = $date['date_from'];
        }
		
		if (array_key_exists('date_to', $date)) {
            $this->m_date_to = $date['date_to'];
        }
		
		if (array_key_exists('to_pay', $date)) {
            $this->m_to_pay = $date['to_pay'];
        }
		
		if (array_key_exists('paid', $date)) {
            $this->m_paid = $date['paid'];
        }
		
		if (array_key_exists('payment_name', $date)) {
            $this->m_payment_name = $date['payment_name'];
        }
	}
	
}