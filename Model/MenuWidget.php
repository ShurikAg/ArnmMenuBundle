<?php
namespace Arnm\MenuBundle\Model;

use Arnm\MenuBundle\Entity\Menu;

use Symfony\Component\Validator\Constraints as Assert;
/**
 * Model class for menu widget
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MenuWidget
{
    /**
     * Title value
     * 
     * @var string
     * 
     * @Assert\Type(type="string", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\MinLength(
     * limit=1,
     * message="Title must be at least {{ limit }} characters or empty."
     * )
     */
    private $title;
    
    /**
     * Menu code to be used in this widget.
     * 
     * @var int
     * 
     * @Assert\Type(type="object", message="The value {{ value }} is not a valid {{ type }}.")
     * @Assert\NotBlank()
     */
    private $menu;
    
    /**
     * Sets the value of title
     * 
     * @param string $html
     */
    public function setTitle($title)
    {
        $this->title = (string) $title;
    }
    
    /**
     * Gets the value of title
     * 
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    
    /**
     * Sets menu
     * 
     * @param Menu $menu
     */
    public function setMenu(Menu $menu = null)
    {
        $this->menu = $menu;
    }
    
    /**
     * Gets menu
     * 
     *  @return Arnm\MenuBundle\Entity\Menu
     */
    public function getMenu()
    {
        return $this->menu;
    }
}
