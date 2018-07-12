<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PostRepository")
 * @ORM\Table(name="post")
 */
class Post
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     */
    private $author;

    /**
     * @ORM\Column(type="string")
     */
    private $title;

    /**
     * @ORM\Column(type="string")
     */
    private $postBody;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Labels")
     */
    private $label;

    public function __construct()
    {
        return $this->label = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getPostBody()
    {
        return $this->postBody;
    }

    /**
     * @param mixed $postBody
     */
    public function setPostBody($postBody): void
    {
        $this->postBody = $postBody;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param mixed $author
     */
    public function setAuthor($author): void
    {
        $this->author = $author;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     */
    public function addLabel($label): void
    {
        $this->label[] = $label;
    }

    public function removeLabel($label)
    {
        $this->label->removeElement($label);
    }

}