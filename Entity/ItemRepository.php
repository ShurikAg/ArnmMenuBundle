<?php
namespace Arnm\MenuBundle\Entity;

use Gedmo\Tree\Entity\Repository\NestedTreeRepository;
/**
 * Item entity repository class
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class ItemRepository extends NestedTreeRepository
{
    /**
     * Finds a root node for a menu ID
     *
     * @param int $id
     *
     * @return Item
     */
    public function findRootForMenuId($id)
    {
        $qb = $this->getRootNodesQueryBuilder()
            ->andWhere('node.menuId = :menuId')
            ->setParameter(':menuId', $id);

        return $qb->getQuery()->getSingleResult();
    }
}