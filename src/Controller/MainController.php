<?php
/**
 * Created by PhpStorm.
 * User: bartekd
 * Date: 2018-10-20
 * Time: 14:26
 */

namespace App\Controller;

use App\Model\LoginModel;
use App\Model\AllAccommodationModel;
use App\Model\AccommodationByIdModel;
use App\Model\RegistrationModel;
use App\Database\DatabaseManager;
use Symfony\Component\Form\Forms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use App\Helper\DebugLog;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;


class MainController extends Controller
{
    private $session;

    public function login(Request $request)
    {
        $this->session = new Session(new PhpBridgeSessionStorage());
        $this->session->start();

        $form_login = $this->createFormBuilder(null)
            ->getForm();

        $form_login_2 = $this->createFormBuilder(null)
            ->getForm();

        if($request->isMethod('POST'))
        {
            //SUBMIT login
            if(false == is_null($request->request->get("login")))
            {
                $login = new LoginModel();
                $login->setEmail($request->request->get("username"));
                $login->setPassword($request->request->get("password"));

                $database = new DatabaseManager($this->container);
                $was_ok = $database->execQuery($login);
                if(false == $was_ok)
                {
                    //error system
                }

                DebugLog::console_log('login:', $login);

                if(true == $login->getWasOk())
                {
                    $this->session->set('id_user', $login->getId());
                    $this->session->set('name', $login->getName());
                    $this->session->set('surname', $login->getSurname());
                    $this->session->set('email', $login->getEmail());
                    $this->session->set('admin', $login->getIsAdmin());

                    return $this->profile();
                }
                else
                {
                    return $this->render('base.html.twig', array(
                        'form_login' => $form_login->createView(),
                        'form_login_2' => $form_login_2->createView(),
                        'was_ok_login' => $login->getWasOk(),
                        'was_ok_register' => true,
                        'error_code' => $login->getErrorCode(),
                        'error_message' => $login->getErrorMessage()
                    ));
                }
            }

            //SUBMIT register
            if(false == is_null($request->request->get("register")))
            {
                $register = new RegistrationModel();
                $register->setMail($request->request->get("email"));
                $register->setPassword($request->request->get("password"));
                $register->setName($request->request->get("name"));
                $register->setSurname($request->request->get("surname"));

                $marketing = 'true';
                if(is_null($request->request->get("marketing")))
                {
                    $marketing = 'false';
                }
                $register->setMarketing($marketing);

                $regulations = 'true';
                if(is_null($request->request->get("regulations")))
                {
                    $regulations = 'false';
                }
                $register->setRegulations($regulations);

                $rodo = 'true';
                if(is_null($request->request->get("rodo")))
                {
                    $rodo = 'false';
                }
                $register->setRodo($rodo);

                $register->setIpAddressV4($request->getClientIp());

                $database = new DatabaseManager($this->container);
                $was_ok = $database->execQuery($register);
                if(false == $was_ok)
                {
                    //error system
                }

                DebugLog::console_log('register:', $register);

                return $this->render('base.html.twig', array(
                    'form_login' => $form_login->createView(),
                    'form_login_2' => $form_login_2->createView(),
                    'was_ok_login' => true,
                    'was_ok_register' => $register->getWasOk(),
                    'error_code' => $register->getErrorCode(),
                    'error_message' => $register->getErrorMessage()
                ));
            }
        }

        return $this->render('base.html.twig', array(
            'form_login' => $form_login->createView(),
            'form_login_2' => $form_login_2->createView(),
            'was_ok_login' => true,
            'was_ok_register' => true
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
        return $this->render('profile.html.twig', array(
            'name' => $this->session->get('name'),
            'surname' => $this->session->get('surname'),
            'email' => $this->session->get('email'),
            'admin' => $this->session->get('admin'),
        ));
    }

    public function myOrders()
    {
        return $this->render(
            'orders.html.twig'
        );
    }

    public function offersList()
    {
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

    public function logout()
    {
        $this->session = new Session(new PhpBridgeSessionStorage());
        $this->session->start();

        $this->session->clear();

        return $this->offersList();
    }

    public function myOffers()
    {

    }
}