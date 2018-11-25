<?php
/**
 * Created by PhpStorm.
 * User: bartekd
 * Date: 2018-10-20
 * Time: 14:26
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class MainController extends Controller
{
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