<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Service
 *
 * @ORM\Table(name="puzzle_expertise_feature")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\FeatureRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Feature
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
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     * @var string
     */
    private $icon;
    
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
    
    public function setIcon($icon) :self {
        $this->icon = $icon;
        return $this;
    }

    public function getIcon() :?string {
        return $this->icon;
    }
}
