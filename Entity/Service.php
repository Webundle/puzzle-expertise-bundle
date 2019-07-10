<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Knp\DoctrineBehaviors\Model\Tree\Node;
use Knp\DoctrineBehaviors\Model\Tree\NodeInterface;

/**
 * Service
 *
 * @ORM\Table(name="puzzle_expertise_service")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\ServiceRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Service implements NodeInterface
{
    use Timestampable, Blameable, Sluggable, Node;
    
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
     * @ORM\Column(name="short_description", type="string", length=255, nullable=true)
     * @var string
     */
    private $shortDescription;
    
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     */
    private $description;
    
    /**
     * @ORM\Column(name="picture", type="string", length=255, nullable=true)
     * @var string
     */
    private $picture;
    
    /**
     * @ORM\Column(name="class_icon", type="string", length=255, nullable=true)
     * @var string
     */
    private $classIcon;
    
    /**
     * @ORM\Column(name="ranking", type="integer", nullable=true)
     * @var int
     */
    private $ranking;
    
    /**
     * @var string
     * @ORM\Column(name="materializedPath", type="string", nullable=true)
     */
    protected $materializedPath = '';

    /**
     * @ORM\OneToMany(targetEntity="Project", mappedBy="service")
     */
    private $projects;
    
    /**
     * @ORM\OneToMany(targetEntity="Staff", mappedBy="service")
     */
    private $staffs;
    
    /**
     * @ORM\OneToMany(targetEntity="Contact", mappedBy="service")
     */
    private $contacts ;
    
    /**
     * @ORM\OneToMany(targetEntity="Service", mappedBy="parentNode")
     */
    private $childNodes;
    
    /**
     * @ORM\ManyToOne(targetEntity="Service", inversedBy="childNodes")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
     */
    private $parentNode;
    
    
    /**
     * Constructor
     */
    public function __construct() {
        $this->projects = new \Doctrine\Common\Collections\ArrayCollection();
        $this->contacts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->staffs = new \Doctrine\Common\Collections\ArrayCollection();
    }
	
    public function getSluggableFields() {
        return ['name'];
    }
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function getNodeId() {
        return is_null($this->id) ? -1 : $this->id;
    }
    
    public function setName($name) {
        $this->name = $name;
        return $this;
    }
    
    public function getName() {
        return $this->name;
    }
    
    public function setShortDescription($shortDescription) :self {
        $this->shortDescription = $shortDescription;
        return $this;
    }
    
    public function getShortDescription() :? string {
        return $this->shortDescription;
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
    
    public function addProject(Project $project) : self {
        if ($this->projects === null ||$this->projects->contains($project) === false) {
            $this->projects->add($project);
        }

        return $this;
    }

    public function removeProject(Project $project) : self {
        $this->projects->removeElement($project);
    }

    public function getProjects() :? Collection {
        return $this->projects;
    }

    public function setClassIcon($classIcon) : self {
        $this->classIcon = $classIcon;
        return $this;
    }

    public function getClassIcon() :? string {
        return $this->classIcon;
    }
    
    public function setRanking($ranking) :self {
        $this->ranking = $ranking;
        return $this;
    }
    
    public function getRanking() :?int {
        return $this->ranking;
    }
    
    public function setStaffs(Collection $staffs = null) {
        $this->staffs = $staffs;
        return $this;
    }
    
    public function addStaff(Staff $staff) {
        if ($this->staffs === null || $this->staffs->contains($staff) === false) {
            $this->staffs->add($staff);
        }
        
        return $this;
    }
    
    public function removeStaff(Staff $staff) {
        $this->staffs->removeElement($staff);
        return $this;
    }
    
    public function getStaffs() {
        return $this->staffs;
    }
    
    public function setContacts (Collection $contacts) :self {
        foreach ($contacts as $contact) {
            $this->addContact($contact);
        }
        
        return $this;
    }
    
    public function addContact(Contact $contact) :self {
        if ($this->contacts->count() === 0 || $this->contacts->contains($contact) === false) {
            $this->contacts->add($contact);
        }
        
        return $this;
    }
    
    public function removeContact(Contact $contact) :self {
        if ($this->contacts->contains($contact) === true) {
            $this->contacts->removeElement($contact);
        }
        
        return $this;
    }
    
    public function getContacts() :?Collection {
        return $this->contacts;
    }
    
}
