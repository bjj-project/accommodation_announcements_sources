<?php
/**
 * Created by PhpStorm.
 * User: Jarek
 * Date: 28.12.2018
 * Time: 18:56
 */

namespace App\Model;

use \PDO;
use App\Helper\DebugLog;
use App\Database\I_Query;

class ClientToConfirmModel
{
    //VARIABLE
    private $m_id_client;
    private $m_name;
    private $m_surname;
    private $m_mail;
    private $m_password;
    private $m_agreement_for_marketing;
    private $m_agreement_for_rodo;
    private $m_agreement_for_regulations;
    private $m_ip_address_v4;
    private $m_creation_date;

    //GETS
    public function getIdClient()
    {
        return $this->m_id_client;
    }

    public function getName()
    {
        return $this->m_name;
    }

    public function getSurname()
    {
        return $this->m_surname;
    }

    public function getMail()
    {
        return $this->m_mail;
    }

    public function getPassword()
    {
        return $this->m_password;
    }

    public function getMarketing()
    {
        return $this->m_agreement_for_marketing;
    }

    public function getRODO()
    {
        return $this->m_agreement_for_rodo;
    }

    public function getRegulations()
    {
        return $this->m_agreement_for_regulations;
    }

    public function getIPv4()
    {
        return $this->m_ip_address_v4;
    }

    public function getCreationDate()
    {
        return $this->m_creation_date;
    }

    public function setDate($date)
    {
        if (array_key_exists('id_client_registration', $date)) {
            $this->m_id_client = $date['id_client_registration'];
        }

        if (array_key_exists('name', $date)) {
            $this->m_name = $date['name'];
        }

        if (array_key_exists('surname', $date)) {
            $this->m_surname = $date['surname'];
        }

        if (array_key_exists('mail', $date)) {
            $this->m_mail = $date['mail'];
        }

        if (array_key_exists('password', $date)) {
            $this->m_password = $date['password'];
        }

        if (array_key_exists('agreement_for_marketing', $date)) {
            $this->m_agreement_for_marketing = $date['agreement_for_marketing'];
        }

        if (array_key_exists('agreement_for_rodo', $date)) {
            $this->m_agreement_for_rodo = $date['agreement_for_rodo'];
        }

        if (array_key_exists('agreement_for_regulations', $date)) {
            $this->m_agreement_for_regulations = $date['agreement_for_regulations'];
        }

        if (array_key_exists('ip_address_v4', $date)) {
            $this->m_ip_address_v4 = $date['ip_address_v4'];
        }

        if (array_key_exists('creation_date', $date)) {
            $this->m_creation_date = $date['creation_date'];
        }
    }

    //DEBUG
    public function debug()
    {
        return json_encode( get_object_vars($this));
    }



}