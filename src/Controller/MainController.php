<?php
/**
 * Created by PhpStorm.
 * User: bartekd
 * Date: 2018-10-20
 * Time: 14:26
 */

namespace App\Controller;

use App\Model\Task;
use Symfony\Component\Form\Forms;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


class MainController extends Controller
{
    public function Login(Request $request)
    {
        // just setup a fresh $task object (remove the dummy data)
        $task = new Task();

        $form = $this->createFormBuilder($task)
            ->add('task', TextType::class)
            ->add('dueDate', DateType::class)
            ->add('save', SubmitType::class, array('label' => 'Create Task'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            // for example, if Task is a Doctrine entity, save it!
            // $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($task);
            // $entityManager->flush();

            return $this->render('index.html.twig', array(
                'form' => $form->createView()
            ));

            //return $this->redirectToRoute('task_success');
        }

        return $this->render('base.html.twig', array(
            'form' => $form->createView()
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