<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;

/**
 * @author qwincy
 * 
 * Project
 *
 * @ORM\Table(name="puzzle_expertise_project")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Project
{
    use Timestampable, Blameable, Sluggable;
    
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
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     * @var string
     */
    private $shortDescription;

    /**
     * @ORM\Column(name="client", type="string", length=255, nullable=true)
     * @var string
     */
    private $client;

    /**
     * @ORM\Column(name="location", type="string", length=255, nullable=true)
     * @var string
     */
    private $location;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    private $startedAt;
    
    /**
     * @var \DateTime
     * @ORM\Column(name="ended_at", type="datetime", nullable=true)
     */
    private $endedAt;
    
    /**
     * @ORM\Column(name="pictures", type="simple_array", nullable=true)
     * @var array
     */
    private $pictures;
    
    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="projects")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id")
     */
    private $service;
    
    public function getSluggableFields() {
        return ['name'];
    }
    
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
    
    public function setShortDescription($shortDescription) :self {
        $this->shortDescription = $shortDescription;
        return $this;
    }
    
    public function getShortDescription() :? string {
        return $this->shortDescription;
    }
    
    public function setClient(string $client) :self {
        $this->client = $client;
        return $this;
    }

    public function getClient() :? string {
        return $this->client;
    }

    public function setLocation(string $location = null) :self {
        $this->location = $location;
        return $this;
    }

    public function getLocation() :? string {
        return $this->location;
    }
    
    public function setStartedAt($startedAt = null) :self {
        if ($startedAt) {
            $this->startedAt = is_string($startedAt) === true ? new \DateTime($startedAt) : $startedAt;
        }
        
        return $this;
    }
    
    public function getStartedAt() :?\DateTime {
        return $this->startedAt;
    }
    
    public function setEndedAt($endedAt = null) :self {
        if ($endedAt) {
            $this->endedAt = is_string($endedAt) === true ? new \DateTime($endedAt) : $endedAt;
        }
        
        return $this;
    }
    
    public function getEndedAt() :?\DateTime {
        return $this->endedAt;
    }

    public function setService(Service $service = null) : self {
        $this->service = $service;
        return $this;
    }

    public function getService() :? Service {
        return $this->service;
    }
    
    public function setPictures($pictures) :self {
        $this->pictures = $pictures;
        return $this;
    }
    
    public function addPicture($picture) :self {
        $this->pictures = array_unique(array_merge($this->pictures, [$picture]));
        return $this;
    }
    
    public function removePicture($picture) :self {
        $this->pictures = array_diff($this->pictures, [$picture]);
        return $this;
    }
    
    public function getPictures() :?array {
        return $this->pictures;
    }
}
