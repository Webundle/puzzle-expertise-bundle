<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Puzzle\UserBundle\Traits\PrimaryKeyTrait;
use Puzzle\MediaBundle\Traits\Pictureable;
use Puzzle\AdminBundle\Traits\DatetimeTrait;
use Puzzle\UserBundle\Traits\UserTrait;
use Puzzle\AdminBundle\Traits\Nameable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Partner
 *
 * @ORM\Table(name="puzzle_expertise_partner")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\PartnerRepository")
 */
class Partner
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
     * @ORM\Column(name="name", type="string", length=255)
     * @var string
     */
    private $name;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @var string
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     */
    private $location;
    
    /**
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @var array
     * @ORM\Column(name="tags", type="array", nullable=true)
     */
    private $tags;
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setDescription($description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setPicture($picture) :self {
        $this->picture = $picture;
        return $this;
    }
    
    public function getPicture() :?string {
        return $this->picture;
    }
    
    public function setLocation($location) : self {
        $this->location = $location;
        return $this;
    }
    
    public function getLocation() :? string {
        return $this->location;
    }
    
    public function setTags($tags = null) : self {
        $this->tags = $tags;
        return $this;
    }
    
    public function addTag($tag) : self {
        $this->tags = array_unique(array_merge($this->tags, [$tag]));
        return $this;
    }
    
    public function removeTag($tag) : self {
        $this->tags = array_diff($this->tags, [$tag]);
        return $this;
    }
    
    public function getTags(){
        return $this->tags;
    }

}
