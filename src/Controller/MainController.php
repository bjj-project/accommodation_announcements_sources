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
use App\Model\AccommodationByClientModel;
use App\Model\MakeAccommodationModel;
use App\Model\AccommodationListToConfirmModel;
use App\Model\AccommodationConfirmByIdModel;
use App\Model\RegistrationModel;
use App\Model\ClientListToConfirmModel;
use App\Model\ClientConfirmByIdModel;
use App\Model\PromotionListModel;
use App\Model\MakeBookingModel;
use App\Model\BookingByClientModel;
use App\Model\CancelBookingModel;
use App\Model\PayBookingModel;



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

    public function __construct()
    {
        session_start();
        $this->session = new Session(new PhpBridgeSessionStorage());
        $this->session->start();
    }

    public function home()
    {
        return $this->render('home.html.twig', array());
    }

    public function login(Request $request)
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
                'show_message_login' => true,
                'show_message_register' => false,
                'was_ok' => $login->getWasOk(),
                'error_code' => $login->getErrorCode(),
                'error_message' => $login->getErrorMessage()
            ));
        }
    }

    public function register(Request $request)
    {
        $register = new RegistrationModel();
        $register->setMail($request->request->get("email"));
        $register->setPassword($request->request->get("password"));
        $register->setName($request->request->get("name"));
        $register->setSurname($request->request->get("surname"));

        $marketing = false;
        if(!is_null($request->request->get("marketing")))
        {
            if($request->request->get("marketing") == "on")
            {
                $marketing = true;
            }
            else
            {
                $marketing = false;
            }
        }
        $register->setMarketing($marketing);

        $regulations = false;
        if(!is_null($request->request->get("regulations")))
        {
            if($request->request->get("regulations") == "on")
            {
                $regulations = true;
            }
            else
            {
                $regulations = false;
            }
        }
        $register->setRegulations($regulations);

        $rodo = false;
        if(!is_null($request->request->get("rodo")))
        {
            if($request->request->get("rodo") == "on")
            {
                $rodo = true;
            }
            else
            {
                $rodo = false;
            }
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
            'show_message_login' => false,
            'show_message_register' => true,
            'was_ok' => $register->getWasOk(),
            'error_code' => $register->getErrorCode(),
            'error_message' => $register->getErrorMessage()
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
        if(null != $this->session->get('id_user'))
        {
            return $this->render('profile.html.twig', array(
                'name' => $this->session->get('name'),
                'surname' => $this->session->get('surname'),
                'email' => $this->session->get('email'),
                'admin' => $this->session->get('admin'),
            ));
        }

        return $this->render('base.html.twig', array(
            'show_message_login' => false,
            'show_message_register' => false
        ));
    }

    public function myOrders()
    {
        //Jeśli wygasła sesja wróć do listy ofert
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        $book_list_per_user = new BookingByClientModel();
        $book_list_per_user->setUserId($this->session->get('id_user'));
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($book_list_per_user);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('my_orders.html.twig', array(
            'book_list_per_user' => $book_list_per_user,
            'show_message' => false,
        ));
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
            'show_message' => false
        ));
    }

    public function makeBooking(Request $request)
    {
        //Jeśli wygasła sesja wróć do listy ofert
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        //Pobranie danych
        $id_user = $this->session->get('id_user');
        $id_offer = $request->request->get("id_offer");
        $date_from = $request->request->get("data-od");
        $date_to = $request->request->get("data-do");

        DebugLog::console_log('makeBooking - data od:', $date_from);

        //Utworzenie rezerwacji
        $make_booking = new MakeBookingModel();
        $make_booking->setIdUser($id_user);
        $make_booking->setIdOffer($id_offer);
        $make_booking->setDateFrom($date_from);
        $make_booking->setDateTo($date_to);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($make_booking);
        if(false == $was_ok)
        {
            //error system
        }

        //Gdy jest OK przekieruj do listy zamowień
        if(true == $make_booking->getWasOk())
        {
            return $this->myOrders();
        }
        else //W innym przypadku powróć do offer.html.twig z błędem
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
                'show_message' => true,
                'was_ok' => $make_booking->getWasOk(),
                'error_message' => $make_booking->getErrorMessage(),
                'error_code' => $make_booking->getErrorCode()
            ));
        }
    }

    public function logout()
    {
        $this->session->clear();

        return $this->offersList();
    }

    public function myOffers()
    {
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        $id_user = $this->session->get('id_user');

        //generowanie listy ofert per user
        $offer_list_per_user = new AccommodationByClientModel();
        $offer_list_per_user->setUserId($id_user);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($offer_list_per_user);
        if(false == $was_ok)
        {
            //error system
        }

        //generowanie listy promocji
        $promotion_list= new PromotionListModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($promotion_list);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('my_offers.html.twig', array(
            'show_message' => false,
            'offer_list_per_user' => $offer_list_per_user,
            'promotion_list' => $promotion_list
        ));
    }

    public function createNewOffer(Request $request)
    {
        //Jeśli wygasła sesja wróć do listy ofert
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        $id_user = $this->session->get('id_user');

        //Tworzenie nowej oferty
        $new_offer = new MakeAccommodationModel();
        $new_offer->setIdUser($id_user);
        $new_offer->setIdPromotion($request->request->get("promotion"));
        $new_offer->setTitle($request->request->get("tytul"));
        $new_offer->setDescription($request->request->get("opis"));
        $new_offer->setDateValidityFrom($request->request->get("data-od"));
        $new_offer->setDateValidityTo($request->request->get("data-do"));

        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($new_offer);
        if(false == $was_ok)
        {
            //error system
        }

        DebugLog::console_log('new_offer:', $new_offer);

        //generowanie listy ofert per user
        $offer_list_per_user = new AccommodationByClientModel();
        $offer_list_per_user->setUserId($id_user);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($offer_list_per_user);
        if(false == $was_ok)
        {
            //error system
        }

        //generowanie listy promocji
        $promotion_list= new PromotionListModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($promotion_list);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('my_offers.html.twig', array(
            'show_message' => true,
            'was_ok' => $new_offer->getWasOk(),
            'error_code' => $new_offer->getErrorCode(),
            'error_message' => $new_offer->getErrorMessage(),
            'offer_list_per_user' => $offer_list_per_user,
            'promotion_list' => $promotion_list
        ));
    }

    public function pojedynczaOferta()
    {
        return $this->render(
            'offer.html.twig'
        );
    }
	
	public function offersToConfirm()
    {
        //generowanie listy ofert do potwierdzenia
        $offer_list_to_confirm = new AccommodationListToConfirmModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($offer_list_to_confirm);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('offers_to_confirm.html.twig', array(
            'offer_list_to_confirm' => $offer_list_to_confirm,
            'show_message' => false
        ));
    }
	
	public function activeOffers()
    {
        return $this->render(
            'active_offers.html.twig'
        );
    }
	
	public function usersToConfirm()
    {
        $client_list_to_confirm = new ClientListToConfirmModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($client_list_to_confirm);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('users_to_confirm.html.twig', array(
            'client_list_to_confirm' => $client_list_to_confirm,
            'show_message' => false,
        ));
    }
	
	public function users()
    {
        return $this->render(
            'users.html.twig'
        );
    }
	
	public function payments()
    {
        return $this->render(
            'payments.html.twig'
        );
    }
	
	public function reservations()
    {
        return $this->render(
            'reservations.html.twig'
        );
    }

    public function confirmClient($id_client)
    {
        $confirm = new ClientConfirmByIdModel();
        $confirm->setIdClient($id_client);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($confirm);
        if(false == $was_ok)
        {
            //error system
        }

        $client_list_to_confirm = new ClientListToConfirmModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($client_list_to_confirm);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('users_to_confirm.html.twig', array(
            'client_list_to_confirm' => $client_list_to_confirm,
            'show_message' => true,
            'was_ok' => $confirm->getWasOk(),
            'error_code' => $confirm->getErrorCode(),
            'error_message' => $confirm->getErrorMessage()
        ));
    }

    public function confirmOffer($id_offer)
    {
        //potwierdzanie oferty
        $confirm = new AccommodationConfirmByIdModel();
        $confirm->setIdOffer($id_offer);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($confirm);
        if(false == $was_ok)
        {
            //error system
        }

        //generowanie listy ofert do potwierdzenia
        $offer_list_to_confirm = new AccommodationListToConfirmModel();
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($offer_list_to_confirm);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('offers_to_confirm.html.twig', array(
            'offer_list_to_confirm' => $offer_list_to_confirm,
            'show_message' => true,
            'was_ok' => $confirm->getWasOk(),
            'error_code' => $confirm->getErrorCode(),
            'error_message' => $confirm->getErrorMessage()
        ));
    }

    public function deleteOffer($id_offer)
    {

    }

    public function deleteBooking($id_book)
    {
        //Jeśli wygasła sesja wróć do listy ofert
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        //Usunięcie rezerwacji
        $delete_booking = new CancelBookingModel();
        $delete_booking->setReservationId($id_book);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($delete_booking);
        if(false == $was_ok)
        {
            //error system
        }

        //Wygenerowanie listy rezerwacji per klient
        $book_list_per_user = new BookingByClientModel();
        $book_list_per_user->setUserId($this->session->get('id_user'));
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($book_list_per_user);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('my_orders.html.twig', array(
            'book_list_per_user' => $book_list_per_user,
            'show_message' => true,
            'was_ok' => $delete_booking->getWasOk(),
            'error_code' => $delete_booking->getErrorCode(),
            'error_message' => $delete_booking->getErrorMessage(),
        ));
    }

    public function confirmBooking($id_book)
    {
        //Jeśli wygasła sesja wróć do listy ofert
        if(null == $this->session->get('id_user'))
        {
            return $this->offersList();
        }

        //potwierdzenie rezerwacji po przez płatność
        $pay_booking = new PayBookingModel();
        $pay_booking->setReservationId($id_book);
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($pay_booking);
        if(false == $was_ok)
        {
            //error system
        }

        //Wygenerowanie listy rezerwacji per klient
        $book_list_per_user = new BookingByClientModel();
        $book_list_per_user->setUserId($this->session->get('id_user'));
        $database = new DatabaseManager($this->container);
        $was_ok = $database->execQuery($book_list_per_user);
        if(false == $was_ok)
        {
            //error system
        }

        return $this->render('my_orders.html.twig', array(
            'book_list_per_user' => $book_list_per_user,
            'show_message' => true,
            'was_ok' => $pay_booking->getWasOk(),
            'error_code' => $pay_booking->getErrorCode(),
            'error_message' => $pay_booking->getErrorMessage(),
        ));
    }
}