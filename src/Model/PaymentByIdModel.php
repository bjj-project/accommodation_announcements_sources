<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 05.01.2019
 * Time: 11:53
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class PaymentByIdModel extends I_Query
{
    protected $m_id_payment;
    protected $m_id_type_payment_status;
    protected $m_name_type_payment_status;
    protected $m_date_change_payment_status;
    protected $m_to_pay;
    protected $m_paid;
    protected $m_date_validity;



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
    public function getIdPayment()
    {
        return $this->m_id_payment;
    }

    public function getIdTypePaymentStatus()
    {
        return $this->m_id_type_payment_status;
    }

    public function getNameTypePaymentStatus()
    {
        return $this->m_name_type_payment_status;
    }

    public function getDateChangePaymentStatus()
    {
        return $this->m_date_change_payment_status;
    }

    public function getToPay()
    {
        return $this->m_to_pay;
    }

    public function getPaid()
    {
        return $this->m_paid;
    }

    public function getDateValidity()
    {
        return $this->m_date_validity;
    }


    //SET
    public function setIdPayment($id_payment)
    {
        $this->m_id_payment = $id_payment;
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
        if (array_key_exists('id_payment', $date)) {
            $this->m_id_payment = $date['id_payment'];
        }

        if (array_key_exists('id_type_payment_status', $date)) {
            $this->m_id_type_payment_status = $date['id_type_payment_status'];
        }

        if (array_key_exists('name', $date)) {
            $this->m_name_type_payment_status = $date['name'];
        }

        if (array_key_exists('date_change_payment_status', $date)) {
            $this->m_date_change_payment_status = $date['date_change_payment_status'];
        }

        if (array_key_exists('to_pay', $date)) {
            $this->m_to_pay = $date['to_pay'];
        }

        if (array_key_exists('paid', $date)) {
            $this->m_paid = $date['paid'];
        }

        if (array_key_exists('date_validity', $date)) {
            $this->m_date_validity = $date['date_validity'];
        }
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }

}