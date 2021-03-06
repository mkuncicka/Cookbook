<?php

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use AppBundle\Entity\User;
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

        $recipes = $em->getRepository('AppBundle:Recipe')->findBy([], ['date' => 'DESC']);

        return array(
            'recipes' => $recipes,
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
        $recipe = new Recipe();
        $form = $this->createCreateForm($recipe);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $recipe->setAuthor($this->getUser());

            $photo = $form->get('photo')->getData();
            if ($photo) {
                $photoName = uniqid() .'.'.$photo->guessExtension();
                $photo->move("images", $photoName);
                $recipe->setPhoto("/images/" . $photoName);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($recipe);
            $em->flush();

            return $this->redirect($this->generateUrl('ingredient_new', array('id' => $recipe->getId())));
        }

        return array(
            'recipe' => $recipe,
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
    private function createCreateForm(Recipe $recipe)
    {
        $form = $this->createForm(new RecipeType(), $recipe, array(
            'action' => $this->generateUrl('recipe_create'),
            'method' => 'POST',
        ));

        $form->add('photo', 'file', array('label' => 'Zdjęcie', 'mapped' => false, 'required' => false));
        $form->add('submit', 'submit', array('label' => 'Dodaj przepis'));

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
        $recipe = new Recipe();
        $form   = $this->createCreateForm($recipe);

        return array(
            'recipe' => $recipe,
            'recipe_form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays all Recipes added by chosen user.
     *
     * @Route("/all/{id}", name="recipe_show_all_by_user")
     * @Method("GET")
     * @Template()
     */
    public function showAllByUser($id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle:User')->find($id);
        $recipes = $em->getRepository('AppBundle:Recipe')->findBy(['author' => $user], ['date' => 'DESC']);

        return array(
            'recipes'      => $recipes,
            'username' => $user->getUsername(),
        );
    }

    /**
     * Finds and displays all Recipes added by followed user.
     *
     * @Route("/followed", name="recipe_show_all_followed_users")
     * @Method("GET")
     * @Template()
     */
    public function showAllByFollowedUsersAction()
    {
        $user = $this->getUser();
        $em = $this->getDoctrine()->getManager();
        $followedUsers = $user->getFollowedUsers();
        $recipes = [];
        foreach ($followedUsers as $us) {
            $rec = $em->getRepository('AppBundle:Recipe')->findBy(['author' => $us]);
            foreach ($rec as $elem) {
                $recipes[] = $elem;
            }
        }

        return ['recipes' => $recipes];
    }

    /**
     * @Route("/category/{id}", name="recipe_show_all_by_category")
     * @Template()
     */
    public function showAllByCategoryAction(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);
        $recipes = $this->getDoctrine()->getRepository('AppBundle:Recipe')->findBy(["category" => $category], ['date' => 'DESC']);

        return ['recipes' => $recipes, 'category' => $category];
    }

    /**
     * Finds and displays a Recipe entity.
     *
     * @Route("/{id}", name="recipe_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }
        $ingredients = $recipe->getIngredients();
        $recipeDeleteForm = $this->createDeleteForm($id);

        return array(
            'recipe'      => $recipe,
            'recipe_delete_form' => $recipeDeleteForm->createView(),
            'ingredients' => $ingredients,
        );
    }

    /**
     * Displays a form to edit an existing Recipe entity.
     *
     * @Route("/edit/{id}", name="recipe_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $recipe = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }

        $ingredients = $em->getRepository('AppBundle:Ingredient')->findBy(['recipe' => $recipe]);

        $editForm = $this->createEditForm($recipe);
        $recipeDeleteForm = $this->createDeleteForm($id);

        return array(
            'recipe'      => $recipe,
            'edit_form'   => $editForm->createView(),
            'recipe_delete_form' => $recipeDeleteForm->createView(),
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
    private function createEditForm(Recipe $recipe)
    {
        $form = $this->createForm(new RecipeType(), $recipe, array(
            'action' => $this->generateUrl('recipe_update', array('id' => $recipe->getId())),
            'method' => 'PUT',
        ));

        $form->add('photo', 'file', array('label' => 'Zmień zdjęcie', 'mapped' => false, 'required' => false));
        $form->add('submit', 'submit', array('label' => 'Zapisz przepis'));

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

        $recipe = $em->getRepository('AppBundle:Recipe')->find($id);

        if (!$recipe) {
            throw $this->createNotFoundException('Unable to find Recipe entity.');
        }
        $oldPhotoDir = "../web" . $recipe->getPhoto();
        $recipeDeleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($recipe);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $photo = $editForm->get('photo')->getData();
            if ($photo) {
                $photoName = uniqid() . '.' . $photo->guessExtension();
                $photo->move("images", $photoName);
                $recipe->setPhoto("/images/" . $photoName);
                if ($oldPhotoDir != "../web/images/default.png") {
                    unlink($oldPhotoDir);
                }
            }

            $em->flush();

            return $this->redirect($this->generateUrl('recipe_edit', array('id' => $id)));
        }

        return array(
            'recipe'      => $recipe,
            'edit_form'   => $editForm->createView(),
            'recipe_delete_form' => $recipeDeleteForm->createView(),
        );
    }
    /**
     * Deletes a Recipe entity.
     *
     * @Route("/{id}", name="recipe_delete")
     * @Template()
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $recipe = $em->getRepository('AppBundle:Recipe')->find($id);
            $ingredients = $recipe->getIngredients();

            if (!$recipe) {
                throw $this->createNotFoundException('Unable to find Recipe entity.');
            }
            foreach ($ingredients as $ingredient) {
                $em->remove($ingredient);
            }
            $em->remove($recipe);
            $em->flush();

            return $this->redirect($this->generateUrl('app_user_mainpage'));
        }

        return ['form' => $form->createView()];
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
            ->add('submit', 'submit', array('label' => 'Usuń przepis'))
            ->getForm()
        ;
    }
}
