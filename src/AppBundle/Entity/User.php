<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Comment;
use AppBundle\Entity\Recipe;
use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Recipe", mappedBy="author")
     */
    private $recipes;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Comment", mappedBy="user")
     */
    private $comments;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", inversedBy="theyFollowUser")
     */
    private $followedUsers;

    /**
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\User", mappedBy="followedUsers")
    */
    private $theyFollowUser;

    public function __construct()
    {
        parent::__construct();
        $this->comments = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->followedUsers = new ArrayCollection();
        $this->theyFollowUser = new ArrayCollection();
    }

    /**
     * Add recipe
     *
     * @param Recipe $recipe
     * @return User
     */
    public function addRecipe(Recipe $recipe)
    {
        $this->recipes[] = $recipe;

        return $this;
    }

    /**
     * Remove recipe
     *
     * @param Recipe $recipe
     */
    public function removeRecipe(Recipe $recipe)
    {
        $this->recipes->removeElement($recipe);
    }

    /**
     * Get recipe
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRecipes()
    {
        return $this->recipes;
    }

    /**
     * Add comment
     *
     * @param Comment $comment
     * @return User
     */
    public function addComment(Comment $comment)
    {
        $this->comments[] = $comment;

        return $this;
    }

    /**
     * Remove comment
     *
     * @param \AppBundle\Entity\Comment $comment
     */
    public function removeComment(Comment $comment)
    {
        $this->comments->removeElement($comment);
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add followedUser
     *
     * @param User $followedUser
     * @return User
     */
    public function addFollowedUser(User $followedUser)
    {
        $this->followedUsers[] = $followedUser;

        return $this;
    }

    /**
     * Remove followedUser
     *
     * @param User $followedUser
     */
    public function removeFollowedUser(User $followedUser)
    {
        $this->followedUsers->removeElement($followedUser);
    }

    /**
     * Get $followedUsers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFollowedUsers()
    {
        return $this->followedUsers;
    }

    /**
     * Get $theyFollowUser
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTheyFollowUser()
    {
        return $this->theyFollowUser;
    }
}
