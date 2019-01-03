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

class BookingByClientModel extends I_Query
{
	private $m_booking_list_per_client;

    private $id_user;

    //SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL get_reservation_by_client_id(". $this->id_user .");";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();

        $this->m_booking_list_per_client = array();
    }


    //GETS
    public function getList()
    {
        return $this->m_booking_list_per_client;
    }


    //SET
    public function setUserId($user_id)
    {
        $this->id_user = $user_id;

        $this->prepareQuery();
    }


    //SET FROM QUERY RESULT
    public function setQueryResult($statement)
    {
        $id = 0;
        while($one_row_of_result_query = $statement->fetch(PDO::FETCH_ASSOC))
        {
            DebugLog::console_log("Query result:", $one_row_of_result_query);

            $this->m_booking_list_per_client[$id] = new BookingByIdModel();
            $this->m_booking_list_per_client[$id]->setDate($one_row_of_result_query);
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