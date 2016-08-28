<?php

namespace AppBundle\Controller;

use AppBundle\Form\IngredientType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
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
        $users = $em->getRepository("AppBundle:User")->findAll();
        return ['users' => $users, 'user' => $this->getUser(),];
    }

    /**
     *
     * @Route("/about", name="about")
     * @Template("AppBundle::about.html.twig")
     */
    public function aboutAction()
    {

    }

    /**
     * @Route("/follow/{user_to_follow_id}", name="follow")
     */
    public function followAction($user_to_follow_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user_to_follow = $em->getRepository("AppBundle:User")->find($user_to_follow_id);
        $user->addFollowedUser($user_to_follow);

        $em->flush();

        return $this->redirectToRoute("app_user_mainpage");
    }

    /**
     * @Route("/stopfollow/{user_to_follow_id}", name="stop_follow")
     */
    public function stopFollowAction($user_to_follow_id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $user_to_follow = $em->getRepository("AppBundle:User")->find($user_to_follow_id);
        $user->removeFollowedUser($user_to_follow);

        $em->flush();

        return $this->redirectToRoute("app_user_mainpage");
    }
}
