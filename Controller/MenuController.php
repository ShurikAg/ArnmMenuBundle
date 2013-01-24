<?php
namespace Arnm\MenuBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Arnm\MenuBundle\Form\ItemType;
use Symfony\Component\HttpFoundation\Response;
use Arnm\MenuBundle\Entity\Item;
use Arnm\CoreBundle\Controllers\ArnmController;
use Arnm\MenuBundle\Form\MenuType;
use Arnm\MenuBundle\Entity\Menu;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Arnm\MenuBundle\Manager\MenuManager;
/**
 * This controller handles all the menu related management
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class MenuController extends ArnmController
{
    /**
     * Renders a list of available menus
     *
     * @return Response
     */
    public function indexAction()
    {
        $entities = $this->getMenuManager()
            ->getMenuRepository()
            ->findAll();

        return $this->render('ArnmMenuBundle:Menu:index.html.twig', array(
            'entities' => $entities
        ));
    }

    /**
     * Shows a from for menu editing
     *
     * @return Response
     */
    public function newAction()
    {
        $menu = new Menu();
        $form = $this->createForm(new MenuType(), $menu);

        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                //create the root item for this menu
                $root = new Item();
                $root->setText($menu->getCode());
                $root->setMenu($menu);
                $em->persist($menu);
                $em->persist($root);
                $em->flush();

                $this->getSession()
                    ->getFlashBag()
                    ->add('notice', $this->get('translator')
                    ->trans('menu.message.create.success', array(), 'menu'));

                return $this->redirect($this->generateUrl('arnm_menus'));
            }
        }

        return $this->render('ArnmMenuBundle:Menu:new.html.twig', array(
            'menu' => $menu,
            'form' => $form->createView()
        ));
    }

    /**
     * Shows a from for menu creation
     *
     * @param int $id Menu's ID
     *
     * @return Response
     */
    public function editAction($id)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }
        $form = $this->createForm(new MenuType(), $menu);

        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();

                //create the root item for this menu
                $em->persist($menu);
                $em->flush();

                $this->getSession()
                    ->getFlashBag()
                    ->add('notice', $this->get('translator')
                    ->trans('menu.message.update.success', array(), 'menu'));

                return $this->redirect($this->generateUrl('arnm_menu_edit', array(
                    'id' => $menu->getId()
                )));
            }
        }

        return $this->render('ArnmMenuBundle:Menu:edit.html.twig', array(
            'menu' => $menu,
            'form' => $form->createView()
        ));
    }
    /**
     * Deletes menu including it's items
     *
     * @param int $id Menu's ID
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }

        $em = $this->getEntityManager();
        $em->beginTransaction();

        try {
            foreach ($menu->getItems() as $item) {
                $em->remove($item);
            }
            $em->remove($menu);
            $em->flush();

            $em->commit();

            $this->getSession()
                ->getFlashBag()
                ->add('notice', $this->get('translator')
                ->trans('menu.message.delete.success', array(), 'menu'));
        } catch (\Exception $exc) {
            $em->rollback();
            $this->getSession()
                ->getFlashBag()
                ->add('error', $this->get('translator')
                ->trans('menu.message.delete.fail', array(), 'menu'));
            $this->getSession()
                ->getFlashBag()
                ->add('error', $exc->getMessage());
        }

        return $this->redirect($this->generateUrl('arnm_menus'));
    }

    /**
     * Shows a tree of items for a menu
     *
     * @param int $id
     *
     * @return Response
     */
    public function itemsAction($id)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }

        $itemsTree = $this->getMenuManager()->fetchItemsTreeForMenu($menu);

        return $this->render('ArnmMenuBundle:Menu:items.html.twig', array(
            'menu' => $menu,
            'tree' => $itemsTree
        ));
    }

    /**
     * Adds an item to the menu
     *
     * @param int $id       Menu's ID
     * @param int $parentId Parent item ID
     *
     * @return Response
     */
    public function addItemAction($id, $parentId)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }

        //find the item
        $parent = $this->getMenuManager()
            ->getItemRepository()
            ->findOneById($parentId);
        if (! ($parent instanceof Item)) {
            throw $this->createNotFoundException("Parent menu item was not found!");
        }

        $item = new Item();
        //create form
        $form = $this->createForm(new ItemType(), $item);
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $item->setParent($parent);
                $item->setMenu($menu);
                $em->persist($item);
                $em->flush();

                return $this->redirect($this->generateUrl('armn_menu', array(
                    'id' => $menu->getId()
                )));
            }
        }

        return $this->render('ArnmMenuBundle:Menu:newItem.html.twig',
        array(
            'menu' => $menu,
            'parent' => $parent,
            'form' => $form->createView()
        ));
    }

    /**
     * Renders and handles submittion of menu item editing form
     *
     * @param int $id     Menu's ID
     * @param int $itemId Item's ID
     *
     * @return Response
     */
    public function editItemAction($id, $itemId)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }
        //find the item
        $item = $this->getMenuManager()
            ->getItemRepository()
            ->findOneById($itemId);
        if (! ($item instanceof Item)) {
            throw $this->createNotFoundException("Menu item was not found!");
        }

        //check if the item is a part of the right menu
        if ($item->getMenu() != $menu) {
            throw new \RuntimeException("Menu item does not belong to requested menu!");
        }

        //create form
        $form = $this->createForm(new ItemType(), $item);
        if ($this->getRequest()->getMethod() === 'POST') {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($item);
                $em->flush();

                return $this->redirect($this->generateUrl('armn_menu', array(
                    'id' => $menu->getId()
                )));
            }
        }

        return $this->render('ArnmMenuBundle:Menu:editItem.html.twig',
        array(
            'menu' => $menu,
            'item' => $item,
            'form' => $form->createView()
        ));
    }

    /**
     * Deletes Menu Item
     *
     * @param int $id     Menu's ID
     * @param int $itemId Item's ID
     *
     * @return Response
     */
    public function deleteItemAction($id, $itemId)
    {
        $menu = $this->getMenuManager()
            ->getMenuRepository()
            ->findOneById($id);
        if (! ($menu instanceof Menu)) {
            throw $this->createNotFoundException("Menu was not found!");
        }
        //find the item
        $item = $this->getMenuManager()
            ->getItemRepository()
            ->findOneById($itemId);
        if (! ($item instanceof Item)) {
            throw $this->createNotFoundException("Menu item was not found!");
        }

        //check if the item is a part of the right menu
        if ($item->getMenu() != $menu) {
            throw new \RuntimeException("Menu item does not belong to requested menu!");
        }

        //remove the item from the tree and delete it
        if ($this->getMenuManager()
            ->getItemRepository()
            ->childCount($item) === 0) {
            $em = $this->getEntityManager();
            $em->remove($item);
            $em->flush();
            $em->clear();

            $this->getSession()
                ->getFlashBag()
                ->add('notice', $this->get('translator')
                ->trans('menu.item.message.delete.success', array(), 'menu'));
        } else {
            $this->getSession()
                ->getFlashBag()
                ->add('error', $this->get('translator')
                ->trans('menu.item.message.delete.cannot_delete', array(), 'menu'));
        }

        return $this->redirect($this->generateUrl('armn_menu', array(
            'id' => $menu->getId()
        )));
    }

    /**
     * Handles ajax request for sorting of a tree nodes
     *
     * @param Request $request
     *
     * @return Response
     */
    public function sortItemAction(Request $request, $id)
    {
        if (! $request->isXmlHttpRequest() || $request->getMethod() != 'POST') {
            throw $this->createNotFoundException('Not valid Request');
        }

        $nodeId = $request->request->get('node');
        $parentId = $request->request->get('parent');
        $index = $request->request->get('index');

        $reply = array();
        try {
            if ($this->getMenuManager()->sortItem($nodeId, $parentId, $index) === true) {
                $reply['status'] = 'SUCCESS';
            }

        } catch (\InvalidArgumentException $e) {
            $reply['status'] = 'FAIL';
            $reply['error'] = $e->getMessage();
        }

        $response = new Response(json_encode($reply));
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * Gets menu manager object
     *
     * @return Arnm\MenuBundle\Manager\MenuManager
     */
    protected function getMenuManager()
    {
        return $this->get('arnm_menu.manager');
    }
}
