<?php
namespace Arnm\MenuBundle\Entity;

use Arnm\MenuBundle\Entity\Menu;
use Gedmo\Tree\Node;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Arnm\CoreBundle\Entity\Entity;

/**
 * Arnm\PagesBundle\Entity\Page
 *
 * @ORM\Entity
 * @ORM\Table(name="menu_item")
 * @ORM\Entity(repositoryClass="Arnm\MenuBundle\Entity\ItemRepository")
 *
 * @Gedmo\Tree(type="nested")
 */
class Item extends Entity implements Node
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
     * @var integer $menuId
     *
     * @ORM\Column(name="menu_id", type="integer")
     */
    private $menuId;

    /**
     * @var integer $parentId
     *
     * @ORM\Column(name="parent_id", type="integer", nullable=true)
     */
    private $parentId;
    /**
     * @var string $text
     *
     * @ORM\Column(name="text", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\MinLength(
     * limit=3,
     * message="Title must be at least {{ limit }} characters."
     * )
     */
    private $text;
    /**
     * @var string $url
     *
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     *
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\MinLength(
     * limit=1,
     * message="Slug must be at least {{ limit }} characters."
     * )
     */
    private $url;

    /**
     * @var Menu
     *
     * @ORM\ManyToOne(targetEntity="Menu", inversedBy="items")
     * @ORM\JoinColumn(name="menu_id", referencedColumnName="id")
     */
    private $menu;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;
    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;
    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;
    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;
    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $parent;
    /**
     * @ORM\OneToMany(targetEntity="Item", mappedBy="parent")
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Gets the menu
     *
     * @return Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }

    /**
     * Sets menu for this item
     *
     * @param Menu $menu
     */
    public function setMenu(Menu $menu)
    {
        $this->menu = $menu;
    }

    /**
     * Set text of the menu item
     *
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }
    /**
     * Gets text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
    /**
     * Set item URL
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }
    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set lft
     *
     * @param integer $lft
     */
    public function setLft($lft)
    {
        $this->lft = $lft;
    }
    /**
     * Get lft
     *
     * @return integer
     */
    public function getLft()
    {
        return $this->lft;
    }
    /**
     * Set lvl
     *
     * @param integer $lvl
     */
    public function setLvl($lvl)
    {
        $this->lvl = $lvl;
    }
    /**
     * Get lvl
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }
    /**
     * Set rgt
     *
     * @param integer $rgt
     */
    public function setRgt($rgt)
    {
        $this->rgt = $rgt;
    }
    /**
     * Get rgt
     *
     * @return integer
     */
    public function getRgt()
    {
        return $this->rgt;
    }
    /**
     * Set root
     *
     * @param integer $root
     */
    public function setRoot($root)
    {
        $this->root = $root;
    }
    /**
     * Get root
     *
     * @return integer
     */
    public function getRoot()
    {
        return $this->root;
    }
    /**
     * Set parent
     *
     * @param Arnm\PagesBundle\Entity\Page $parent
     */
    public function setParent(Item $parent)
    {
        $this->parent = $parent;
    }
    /**
     * Get parent
     *
     * @return Arnm\PagesBundle\Entity\Page
     */
    public function getParent()
    {
        return $this->parent;
    }
    /**
     * Add children
     *
     * @param Item $child
     */
    public function addChild(Item $child)
    {
        $this->children[] = $child;
    }
    /**
     * Get children
     *
     * @return Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Gets the menu ID that this item is part of
     *
     * @return int
     */
    public function getMenuId()
    {
        return $this->menuId;
    }

    /**
     * Retuns string that represents the page.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getTitle();
    }

    /**
     * Set parentId
     *
     * @param integer $parentId
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
    }

    /**
     * Get parentId
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Determines if the node is a root
     *
     * @return boolean
     */
    public function isRoot()
    {
        return ($this->getId() == $this->getRoot() && $this->getLvl() == 0);
    }
}