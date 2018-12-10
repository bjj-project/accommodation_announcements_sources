<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 08.12.2018
 * Time: 20:36
 */

namespace App\Database;

class I_Query
{
    protected $m_sql_query_text = '';

    final public function getQueryText()
    {
        return $this->m_sql_query_text;
    }

    public function setQueryResult($result_query)
    {
        //override this method
    }

    public function debug()
    {
        //override this method
    }


}