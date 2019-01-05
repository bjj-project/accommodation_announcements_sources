<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 05.01.2019
 * Time: 11:49
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class AllPaymentsModel extends I_Query
{
    private $m_payment_list;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "SELECT * FROM get_all_payments;";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();

        $this->m_payment_list = array();
    }


    //GETS
    public function getList()
    {
        return $this->m_payment_list;
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $id = 0;
        while($one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC))
        {
            DebugLog::console_log("Query result:", $one_row_of_result_query);

            $this->m_payment_list[$id] = new PaymentByIdModel();
            $this->m_payment_list[$id]->setDate($one_row_of_result_query);
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