<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    /**
     * @Route("/")
     * @Template("AppBundle::main.html.twig")
     */
    public function mainPageAction()
    {
        return ['user' => $this->getUser()];
    }

    /**
     * @Route("/users", name="user_show_all")
     * @Template()
     */
    public function showAll()
    {
        $em = $this->getDoctrine()->getManager();
//        $ids = $em->createQuery('SELECT author_id r FROM AppBundle:Recipe r');
        $users = $em->getRepository("AppBundle:User")->findAll();
        return ['users' => $users, 'user' => $this->getUser(),];
    }
}
