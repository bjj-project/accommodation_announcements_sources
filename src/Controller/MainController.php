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

                $database = new DatabaseManager($this->container);
                $was_ok = $database->execQuery($login);
                if(false == $was_ok)
                {
                    //error system
                }

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
}