<?php

namespace StatsBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use StatsBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Security("is_granted('IS_AUTHENTICATED_FULLY')")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig', ['user' => $this->getUser()]);
    }

    public function viewUserAction(User $user)
    {

    }
}
