<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Recipe;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\Ingredient;
use AppBundle\Form\IngredientType;

/**
 * Ingredient controller.
 *
 * @Route("/ingredient")
 */
class IngredientController extends Controller
{

    /**
     * Lists all Ingredient entities.
     *
     * @Route("/", name="ingredient")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ingredients = $em->getRepository('AppBundle:Ingredient')->findAll();

        return array(
            'ingredients' => $ingredients,
        );
    }
    /**
     * Creates a new Ingredient entity.
     *
     * @Route("/{recId}", name="ingredient_create")
     * @Method("POST")
     * @Template("AppBundle:Ingredient:new.html.twig")
     */
    public function createAction(Request $request, $recId)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('AppBundle:Recipe')->find($recId);
        $ingredient = new Ingredient();
        $form = $this->createCreateForm($ingredient, $recId);
        $form->handleRequest($request);
        $ingredient->setRecipe($recipe);

        if ($form->isValid()) {
            $em->persist($ingredient);
            $em->flush();

            return $this->redirect($this->generateUrl('ingredient_new', array('id' => $ingredient->getRecipe()->getId())));
        }

        return array(
            'ingredient' => $ingredient,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Ingredient entity.
     *
     * @param Ingredient $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Ingredient $ingredient, $recId)
    {
        $form = $this->createForm(new IngredientType(), $ingredient, array(
            'action' => $this->generateUrl('ingredient_create', array('recId' => $recId)),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Dodaj składnik'));

        return $form;
    }

    /**
     * Displays a form to create a new Ingredient entity.
     *
     * @Route("/new", name="ingredient_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $recipe = $em->getRepository('AppBundle:Recipe')->find($request->query->get('id'));
        $ingredient = new Ingredient();
        $form   = $this->createCreateForm($ingredient, $request->query->get('id'));
        $ingredients = $em->getRepository('AppBundle:Ingredient')->findBy(['recipe' => $recipe]);

        return array(
            'ingredient' => $ingredient,
            'form'   => $form->createView(),
            'recipe' => $recipe,
            'ingredients' => $ingredients,
        );
    }

    /**
     * Finds and displays a Ingredient entity.
     *
     * @Route("/{id}", name="ingredient_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ingredient = $em->getRepository('AppBundle:Ingredient')->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Unable to find Ingredient entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'ingredient'      => $ingredient,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Ingredient entity.
     *
     * @Route("/{id}/edit", name="ingredient_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $ingredient = $em->getRepository('AppBundle:Ingredient')->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Unable to find Ingredient entity.');
        }

        $editForm = $this->createEditForm($ingredient);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'ingredient'      => $ingredient,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Ingredient entity.
    *
    * @param Ingredient $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Ingredient $ingredient)
    {
        $form = $this->createForm(new IngredientType(), $ingredient, array(
            'action' => $this->generateUrl('ingredient_update', array('id' => $ingredient->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Zapisz składnik'));

        return $form;
    }
    /**
     * Edits an existing Ingredient entity.
     *
     * @Route("/{id}", name="ingredient_update")
     * @Method("PUT")
     * @Template("AppBundle:Ingredient:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $ingredient = $em->getRepository('AppBundle:Ingredient')->find($id);

        if (!$ingredient) {
            throw $this->createNotFoundException('Unable to find Ingredient entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($ingredient);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('recipe_edit', array('id' => $ingredient->getRecipe()->getId())));
        }

        return array(
            'ingredient'      => $ingredient,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Ingredient entity.
     *
     * @Route("/{id}", name="ingredient_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $ingredient = $em->getRepository('AppBundle:Ingredient')->find($id);
            $recipeId = $ingredient->getRecipe()->getId();

            if (!$ingredient) {
                throw $this->createNotFoundException('Unable to find Ingredient entity.');
            }

            $em->remove($ingredient);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('recipe_show', ['id' => $recipeId]));
    }

    /**
     * Creates a form to delete a Ingredient entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('ingredient_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Usuń składnik'))
            ->getForm()
        ;
    }
}
