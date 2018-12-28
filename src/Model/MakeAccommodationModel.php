<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 27.12.2018
 * Time: 18:56
 */
 
namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class MakeAccommodationModel extends I_Query
{
	protected $m_id_user;
    protected $m_id_promotion;
	
	//SET QUERY
    private function prepareQuery()
    {
        $this->m_sql_query_text = "CALL get_offer_by_id();";
    }


    //CONSTRUCTOR
    public function __construct()
    {
        $this->prepareQuery();
    }
	
	//DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }
}