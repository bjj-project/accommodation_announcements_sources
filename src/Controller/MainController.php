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
        $login = new LoginModel();

        $form = $this->createFormBuilder($login)
            ->add('email', TextType::class)
            ->add('password', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $login = $form->getData();

            $database = new DatabaseManager($this->container);
            $was_ok = $database->execQuery($login);
            if(false == $was_ok)
            {
                //error system
            }

            DebugLog::console_log('login:', $login);
            DebugLog::console_log('error:', $login->getErrorMessage());
            DebugLog::console_log('was_ok:', $login->getWasOk());

            return $this->render('base.html.twig', array(
                'form' => $form->createView(),
                'was_ok' => $login->getWasOk(),
                'error_message' => $login->getErrorMessage()
            ));

        }

        return $this->render('base.html.twig', array(
            'form' => $form->createView(),
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