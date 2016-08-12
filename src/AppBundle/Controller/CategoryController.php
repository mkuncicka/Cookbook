<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Category Controller
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/new", name="category_new")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $category = new Category();
        $form = $this->createForm(new CategoryType(), $category);
        $form->add('submit', 'submit', ['label' => 'Dodaj kategorię']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute("category_show_all");
        }

        return ['new_cat_form' => $form->createView()];
    }

    /**
     * @Route("/", name="category_show_all")
     * @Template()
     */
    public function showAllAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findAll();

        return ['categories' => $categories];
    }

    /**
     * @Route("/admin/category/new")
     * @Method("POST")
     * @Template()
     */
    public function createAction()
    {

    }

    /**
     * @Route("/admin/category/edit/{id}")
     * @Method("GET")
     * @Template()
     */
    public function editAction()
    {

    }

    /**
     * @Route("/admin/category/edit/{id}")
     * @Method("PUT")
     * @Template()
     */
    public function updateAction()
    {

    }

    /**
     * @Route("/admin/category/delete/{id}")
     * @Method("DELETE")
     * @Template()
     */
    public function deleteAction()
    {

    }
}
