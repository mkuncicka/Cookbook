<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Recipe;
use AppBundle\Entity\Ingredient;
use AppBundle\Form\RecipeType;

/**
 * Recipe controller.
 *
 * @Route("/recipe")
 */
class RecipeController extends Controller
{

    /**
     * Lists all Recipe entities.
     *
     * @Route("/", name="recipe")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Recipe')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new Recipe entity.
     *
     * @Route("/", name="recipe_create")
     * @Method("POST")
     * @Template("AppBundle:Recipe:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Recipe();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        $entity->setAuthor($this->getUser());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('ingredient_new', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Recipe entity.
     *
     * @param Recipe $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Recipe $entity)
    {
        $form = $this->createForm(new RecipeType(), $entity, array(
            'action' => $this->generateUrl('recipe_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new Recipe entity.
     *
     * @Route("/new", name="recipe_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Recipe();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Recipe entity.
     *
     * @Route("/all", name="recipe_show_all")
     * @Method("GET")
     * @Template()
     */
    public function showAllByUser()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('AppBundle:Recipe')->findBy(['author' => $this->getUser()]);

        return array(
            'entities'      => $entities,
        );
    }

    /**
     * Finds and displays a Recipe entity.
     *
     * @Route("/{id}", name="recipe_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }
        $ingredients = $entity->getIngredients();
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
            'ingredients' => $ingredients,
        );
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/{id}/edit", name="recipe_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $ingredients = $em->getRepository('AppBundle:Ingredient')->findBy(['recipe' => $entity]);

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
            'ingredients' => $ingredients,
        );
    }

    /**
    * Creates a form to edit a Recipe entity.
    *
    * @param Recipe $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Recipe $entity)
    {
        $form = $this->createForm(new RecipeType(), $entity, array(
            'action' => $this->generateUrl('recipe_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing Recipe entity.
     *
     * @Route("/{id}", name="recipe_update")
     * @Method("PUT")
     * @Template("AppBundle:Recipe:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('recipe_edit', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Recipe entity.
     *
     * @Route("/{id}", name="recipe_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('AppBundle:Recipe')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find Recipe entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('app_user_mainpage'));
    }

    /**
     * Creates a form to delete a Recipe entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('recipe_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
