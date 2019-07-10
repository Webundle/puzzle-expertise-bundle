<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Testimonial
 *
 * @ORM\Table(name="puzzle_expertise_testimonial")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\TestimonialRepository")
 */
class Testimonial
{
    use Timestampable, Blameable;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(name="message", type="text", nullable=true)
     * @var string
     */
    private $message;
    
    /**
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @var string
     * @ORM\Column(name="author", type="string", length=255)
     */
    private $author;

    /**
     * @var string
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;
    
    /**
     * @var string
     * @ORM\Column(name="position", type="string", length=255)
     */
    private $position;
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function setMessage($message) :self {
        $this->message = $message;
        return $this;
    }
    
    public function getMessage() :?string {
        return $this->message;
    }
    
    public function setPicture($picture) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setAuthor($author) : self {
    	$this->author = $author;
        return $this;
    }

    public function getAuthor() :? string {
    	return $this->author;
    }

    public function setCompany($company) : self {
        $this->company = $company;
        return $this;
    }

    public function getCompany() :? string {
        return $this->company;
    }

    public function setPosition($position) : self {
        $this->position = $position;
        return $this;
    }

    public function getPosition() :? string {
        return $this->position;
    }
}
