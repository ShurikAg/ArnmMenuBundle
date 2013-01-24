<?php
namespace Arnm\MenuBundle\Manager;

use Arnm\MenuBundle\Entity\ItemRepository;
use Arnm\MenuBundle\Entity\MenuRepository;
use Arnm\MenuBundle\Entity\Menu;
use Arnm\MenuBundle\Entity\Item;
use Doctrine\Bundle\DoctrineBundle\Registry;
/**
 * Menu manager handles menu management business logic
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MenuManager
{
    /**
     * Doctrine object instance
     *
     * @var Registry
     */
    protected $doctrine;

    /**
     * MenuRepository object instance
     *
     * @var MenuRepository
     */
    protected $menuRepository;

    /**
     * ItemRepository object instance
     *
     * @var ItemRepository
     */
    protected $itemRepository;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->setDoctrine($doctrine);
    }

    /**
     * Sets doctrine object instance
     *
     * @param Registry $doctrine
     */
    public function setDoctrine(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * Gets an instance ob doctine that was initially injected
     *
     * @return Registry
     */
    public function getDoctrine()
    {
        return $this->doctrine;
    }

    /**
     * Gets entity manager
     *
     * @return Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getDoctrine()->getEntityManager();
    }

    /**
     * Gets an instance of MenuRepository
     *
     * @return Arnm\MenuBundle\Entity\MenuRepository
     */
    public function getMenuRepository()
    {
        if (is_null($this->menuRepository)) {
            $this->menuRepository = $this->getEntityManager()->getRepository('ArnmMenuBundle:Menu');
        }
        return $this->menuRepository;
    }

    //TODO: Remove the two next methods when the bud is fixed properly
    /**
     * Gets an instance of ItemRepository
     *
     * @return Arnm\MenuBundle\Entity\ItemRepository
     */
    public function getItemRepository()
    {
        if (is_null($this->itemRepository)) {
            $this->itemRepository = $this->getEntityManager()->getRepository('ArnmMenuBundle:Item');
        }
        return $this->itemRepository;
    }

    /**
     * Gets a full tree of items for given menu
     *
     * @param Menu    $menu        Menu object to lookup the elements for
     * @param boolean $includeRoot Wiether or not o include the menu root element
     *
     * @return array
     */
    public function fetchItemsTreeForMenu(Menu $menu, $includeRoot = true)
    {
        $root = $this->getItemRepository()->findRootForMenuId($menu->getId());

        return $this->getItemRepository()->childrenHierarchy($root, false, array(), $includeRoot);
    }

    /**
     * Marks item/s active according to the given URL path
     * If the given path is empty or not provided, nothing is marked
     *
     * @param array  $items   Array of menu items
     * @param string $urlPath PATH path of the url
     */
    public function markActive(array $items = array(), $urlPath = null)
    {
        if(empty($urlPath)){
            return $items;
        }

        $this->doMarkMatching($items, $urlPath);

        return $items;
    }

    /**
     * Does the actual job of marking the matching item by URL
     *
     * @param array  $items   Array of menu items
     * @param string $urlPath Path to match
     *
     * @return boolean
     */
    private function doMarkMatching(array &$items = array(), $urlPath)
    {
        foreach ($items as &$item){
            if($item['url'] === $urlPath) {
                $item['current'] = true;
                return true;
            }

            $matchFoundInChildren = false;
            if(isset($item['__children']) && is_array($item['__children'])){
                $matchFoundInChildren = $this->doMarkMatching($item['__children'], $urlPath);
            }

            if($matchFoundInChildren === true) {
                $item['current_parent'] = true;
                return true;
            }
        }
    }

    /**
     * Handles the sorting logic
     *
     * @param int $nodeId
     * @param int $parentId
     * @param int $index
     *
     * @throws \InvalidArgumentException
     *
     * @return boolean True on success, false on failure
     */
    public function sortItem($nodeId, $parentId, $index)
    {
        $repo = $this->getItemRepository();
        //first get all required nodes
        $item = $repo->findOneById($nodeId);
        $parent = $repo->findOneById($parentId);

        if (! ($item instanceof Item) || ! ($parent instanceof Item) || $index < 0) {
            throw new \InvalidArgumentException('Could not find one(or more) of required nodes!');
        }

        if ($index == 0) {
            $repo->persistAsFirstChildOf($item, $parent);
        } else {
            //get all the children for parent
            $children = $repo->children($parent, true);

            //find the sibling the will eventually become a previous sibling to the item
            if (! ($children[$index - 1] instanceof Item)) {
                throw new \InvalidArgumentException('Could not find one(or more) of required nodes!');
            }

            $sibling = $children[$index - 1];

            //put the page as a next sibling of $sibling
            $repo->persistAsNextSiblingOf($item, $sibling);
        }

        $this->getEntityManager()->flush();

        return true;
    }
}

