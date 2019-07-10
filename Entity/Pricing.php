<?php

namespace Puzzle\ExpertiseBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Knp\DoctrineBehaviors\Model\Blameable\Blameable;

/**
 * Service
 *
 * @ORM\Table(name="puzzle_expertise_pricing")
 * @ORM\Entity(repositoryClass="Puzzle\ExpertiseBundle\Repository\PricingRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Pricing
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
     * @ORM\Column(name="amount", type="integer", nullable=true)
     * @var string
     */
    private $amount;
    
    /**
     * @ORM\Column(name="currency", type="string", length=255, nullable=true)
     * @var string
     */
    private $currency;
    
    /**
     * @ORM\Column(name="period", type="string", length=255, nullable=true)
     * @var string
     */
    private $period;
    
    public function getId() :?int {
        return $this->id;
    }
    
    public function setName(string $name) :self {
        $this->name = $name;
        return $this;
    }
    
    public function getName() :? string {
        return $this->name;
    }
    
    public function setDescription(string $description) :self {
        $this->description = $description;
        return $this;
    }
    
    public function getDescription() :?string {
        return $this->description;
    }
    
    public function setAmount(int $amount) :self {
        $this->amount = $amount;
        return $this;
    }

    public function getAmount() :?int {
        return $this->amount;
    }
    
    public function setCurrency(string $currency) :self {
        $this->currency = $currency;
        return $this;
    }
    
    public function getCurrency() :?string {
        return $this->currency;
    }
    
    public function setPeriod(string $period) :self {
        $this->period = $period;
        return $this;
    }
    
    public function getPeriod() :?string {
        return $this->period;
    }
}
