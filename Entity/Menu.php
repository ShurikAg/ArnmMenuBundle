<?php
namespace Arnm\MenuBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Arnm\MenuBundle\Entity\Item;
/**
 * Menu entity class
 *
 * @ORM\Table(name="menu")
 * @ORM\Entity(repositoryClass="Arnm\MenuBundle\Entity\MenuRepository")
 *
 * @UniqueEntity("code")
 */
class Menu
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $code
     *
     * @ORM\Column(name="code", type="string", length=50)
     *
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $code;

    /**
     * @var string $cssClass
     *
     * @ORM\Column(name="class", type="string", length=50)
     *
     * @Assert\NotNull()
     * @Assert\NotBlank()
     */
    private $class;

    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="menu")
     */
    private $items;

    /**
     * Default constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the ID
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int) $id;
    }

    /**
     * Set code
     *
     * @param string $code
     *
     * @return Area
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Gets css class of the menu
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * Sets css class for the menu
     *
     * @param string $class
     */
    public function setClass($class)
    {
        $this->class = $class;
    }

    /**
     * Add item
     *
     * @param Item $item
     *
     * @return Menu
     */
    public function addItem(Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Get items
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }
}