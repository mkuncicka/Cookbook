<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Category;
use AppBundle\Form\CategoryType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/", name="category_show_all")
     * @Template()
     */
    public function showAllAction()
    {
        $categories = $this->getDoctrine()->getRepository('AppBundle:Category')->findBy([], ['name' => 'ASC']);

        return ['categories' => $categories];
    }

    /**
     * @Route("/edit/{id}", name="category_edit")
     * @Template("AppBundle:Category:new.html.twig")
     */
    public function editAction(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);
        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $form = $this->createForm(new CategoryType(), $category);
        $form->add('submit', 'submit', ['label' => 'Zapisz kategorię']);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("category_show_all");
        }

        return ['form' => $form->createView()];
    }

    /**
     * Deletes a Category entity.
     *
     * @Route("/delete/{id}", name="category_delete")
     */
    public function deleteAction(Request $request, $id)
    {

        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        if (!$category) {
            throw $this->createNotFoundException('Unable to find Category entity.');
        }
        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        return new Response("Skasowano kategorię: " . $category->getName());
//        return $this->redirectToRoute('category_show_all');
    }

    /**
     * Creates a form to delete a Category entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('category_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Usuń kategorię'))
            ->getForm();
    }
}
