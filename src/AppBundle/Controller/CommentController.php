<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Comment;
use AppBundle\Form\CommentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @package AppBundle\Controller
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * @Route("/new/{recipeId}", name="comment_new")
     * @Template()
     */
    public function newAction(Request $request, $recipeId)
    {
        $recipe = $this->getDoctrine()->getRepository("AppBundle:Recipe")->find($recipeId);
        $comment = new Comment();
        $form = $this->createForm(new CommentType(), $comment);
        $form->add('submit', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $comment->setUser($this->getUser());
            $comment->setRecipe($recipe);
            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirectToRoute("recipe_show", ['id' => $recipeId]);
        }

        return ['form' => $form->createView()];
    }

    /**
     * @Route("/show/{recipeId}", name="comments_show")
     * @Template()
     */
    public function showAction($recipeId)
    {
        $recipe = $this->getDoctrine()->getRepository("AppBundle:Recipe")->find($recipeId);
        $comments = $this->getDoctrine()->getRepository("AppBundle:Comment")->findBy(['recipe' => $recipe]);

        return ['comments' => $comments];
    }

    /**
     * @Route("/edit/{id}", name="comment_edit")
     * @Template()
     */
    public function editAction(Request $request, $id)
    {
        $comment = $this->getDoctrine()->getRepository("AppBundle:Comment")->find($id);
        $form = $this->createForm(new CommentType(), $comment);
        $form->add('submit', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute("recipe_show", ['id' => $comment->getRecipe()->getId()]);
        }

        return ['form' => $form->createView()];

    }

    /**
     * Deletes a Comment entity.
     *
     * @Route("/delete/{id}", name="comment_delete")
     * @Template()
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $comment = $em->getRepository('AppBundle:Comment')->find($id);

        if (!$comment) {
            throw $this->createNotFoundException('Unable to find Comment entity.');
        }

        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->remove($comment);
            $em->flush();
            return $this->redirectToRoute("recipe_show", ['id' => $comment->getRecipe()->getId() ]);

        }

        return ['form' => $form->createView()];
    }

    /**
     * Creates a form to delete a Comment entity by id.
     *
     * @param mixed $id The comment id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('comment_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Usuń komentarz'))
            ->getForm()
            ;
    }
}
