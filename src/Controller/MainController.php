<?php
/**
 * Created by PhpStorm.
 * User: bartekd
 * Date: 2018-10-20
 * Time: 14:26
 */

namespace App\Controller;

use App\Model\Task;
use App\Model\LoginModel;
use App\Model\AllAccommodationModel;
use App\Model\AccommodationByIdModel;
use App\Database\DatabaseManager;
use Symfony\Component\Form\Forms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Helper\DebugLog;


class MainController extends Controller
{
    public function Login(Request $request)
    {
        $form_login = $this->createFormBuilder(null)
            ->getForm();

        $form_login_2 = $this->createFormBuilder(null)
            ->getForm();

        if($request->isMethod('POST'))
        {
            //SUBMIT login
            if(false == is_null($request->request->get("login")))
            {
                DebugLog::console_log('LOGIN');

                $login = new LoginModel();
                $login->setEmail($request->request->get("username"));
                $login->setPassword($request->request->get("password"));


                //$all_offer = new AllAccommodationModel();
                $offer_by_id = new AccommodationByIdModel();
                $offer_by_id->setIdOffer(1);
                $database = new DatabaseManager($this->container);
                //$was_ok = $database->execQuery($login);
                //$was_ok = $database->execQuery($all_offer);
                $was_ok = $database->execQuery($offer_by_id);
                if(false == $was_ok)
                {
                    //error system
                }

                //DebugLog::console_log('$all_offer->getTitle(0):', $all_offer->getTitle(0));
                DebugLog::console_log('$offer_by_id->getTitle:', $offer_by_id->getTitle());

                DebugLog::console_log('login:', $login);
                DebugLog::console_log('error:', $login->getErrorMessage());
                DebugLog::console_log('was_ok:', $login->getWasOk());
                DebugLog::console_log('code:', $login->getErrorCode());

                return $this->render('base.html.twig', array(
                    'form_login' => $form_login->createView(),
                    'form_login_2' => $form_login_2->createView(),
                    'was_ok' => $login->getWasOk(),
                    'error_code' => $login->getErrorCode(),
                    'error_message' => $login->getErrorMessage()
                ));
            }

            //SUBMIT register
            if(false == is_null($request->request->get("register")))
            {
                DebugLog::console_log('REJESTRACJA');
            }
        }

        return $this->render('base.html.twig', array(
            'form_login' => $form_login->createView(),
            'form_login_2' => $form_login_2->createView(),
            'error_code' => 0,
            'was_ok' => 1
        ));
    }

    public function MainSite()
    {
        return $this->render(
            'base.html.twig'
        );
    }

    public function profile()
    {
        return $this->render(
            'profile.html.twig'
        );
    }

    public function orders()
    {
        return $this->render(
            'orders.html.twig'
        );
    }

    public function offerslist()
    {
        DebugLog::console_log('OFFER LIST');

        $all_offer = new AllAccommodationModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($all_offer);
        if(false == $was_ok)
        {
            //error system
        }


        return $this->render('offerslist.html.twig', array(
            'all_offer' => $all_offer,
        ));
    }

    public function offer($id_offer)
    {
        DebugLog::console_log('ONE OFFER');

        $one_offer = new AccommodationByIdModel();
        $one_offer->setIdOffer($id_offer);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($one_offer);
        if(false == $was_ok)
        {
            //error system
        }


        return $this->render('offer.html.twig', array(
            'offer' => $one_offer,
        ));
    }
}