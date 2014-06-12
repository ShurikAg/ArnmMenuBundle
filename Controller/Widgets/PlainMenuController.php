<?php
namespace Arnm\MenuBundle\Controller\Widgets;

use Symfony\Component\Form\FormError;

use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Arnm\MenuBundle\Entity\Menu;
use Arnm\MenuBundle\Model\MenuWidget;
use Arnm\WidgetBundle\Controllers\ArnmWidgetController;
use Arnm\MenuBundle\Form\MenuWidgetType;
use Arnm\WidgetBundle\Entity\Param;
use Arnm\WidgetBundle\Entity\Widget;
/**
 * Plain menu widget with only simple div wrapping
 *
 * @author Alex Agulyansky <alex@iibspro.com>
 */
class PlainMenuController extends ArnmWidgetController
{
    /**
     * {@inheritdoc}
     * @see Arnm\WidgetBundle\Controllers.ArnmWidgetController::editAction()
     */
    public function editAction()
    {
        //create the form
        $menuWidgetObj = new MenuWidget();
        $form = $this->createForm(new MenuWidgetType(), $menuWidgetObj);

        return $this->render('ArnmMenuBundle:Widgets\Plain:edit.html.twig', array(
            'edit_form' => $form->createView()
        ));
    }

    /**
     * {@inheritdoc}
     * @see Arnm\WidgetBundle\Controllers.ArnmWidgetController::updateAction()
     */
    public function updateAction($id, Request $request)
    {
        $widget = $this->getWidgetManager()->findWidgetById($id);
        if (!($widget instanceof Widget)) {
            throw $this->createNotFoundException("Widget with id: '" . $id . "' not found!");
        }

        $menuWidgetObj = new MenuWidget();
        $form = $this->createForm(new MenuWidgetType(), $menuWidgetObj);

        $data = $this->extractArrayFromRequest($request);

        $form->bind($data);
        if (!$form->isValid()) {
            $response = array('error' => 'validation', 'errors' => array());
            $errors = $form->getErrors();
            foreach ($errors as $key => $error) {
                if ($error instanceof FormError) {
                    $response['errors'][$key] = $error->getMessage();
                }
            }

            return $this->createResponse($response);
        }

        $this->processSaveParams($widget, $menuWidgetObj);

        return $this->createResponse(array('OK'));
    }

    /**
     * {@inheritdoc}
     * @see Arnm\WidgetBundle\Controllers.ArnmWidgetController::getConfigFields()
     */
    public function getConfigFields()
    {
        return array(
            'title',
            'menu'
        );
    }

    /**
     * Creates new of updates existing param of the widget
     *
     * @param Widget     $widget        Widget object itself
     * @param MenuWidget $menuWidgetObj Object that is used as a data object for widget management
     */
    protected function processSaveParams(Widget $widget, MenuWidget $menuWidgetObj)
    {
        //find the widget
        $paramId = $widget->getParamByName('menu');
        $eMgr = $this->getEntityManager();
        if ($paramId instanceof Param) {
            //update existing
            $paramId->setValue((string) $menuWidgetObj->getMenu()->getId());
        } else {
            //create new
            $paramId = new Param();
            $paramId->setName('menu');
            $paramId->setValue((string) $menuWidgetObj->getMenu()->getId());
            $paramId->setWidget($widget);
        }

        $eMgr->persist($paramId);

        $paramTitle = $widget->getParamByName('title');
        if ($paramTitle instanceof Param) {
            //update existing
            $paramTitle->setValue((string) $menuWidgetObj->getTitle());
        } else {
            //create new
            $paramTitle = new Param();
            $paramTitle->setName('title');
            $paramTitle->setValue((string) $menuWidgetObj->getTitle());
            $paramTitle->setWidget($widget);
        }

        $eMgr->persist($paramTitle);

        $eMgr->flush();
    }

    /**
     * {@inheritdoc}
     * @see Arnm\WidgetBundle\Controllers.ArnmWidgetController::renderAction()
     */
    public function renderAction(Widget $widget)
    {
        $codeParam = $widget->getParamByName('menu');
        $titleParam = $widget->getParamByName('title');

        $title = null;
        if ($titleParam instanceof Param) {
            $title = $titleParam->getValue();
        }

        return $this->renderMenuAction($codeParam->getValue(), $title);
    }

    /**
     * Renders the menu itself
     *
     * @param string $menuCode Menu unique code
     * @param string $title    Optional - title to be used as a title for a widget
     *
     * @return Response
     */
    public function renderMenuAction($menuCode, $title = null)
    {
        $menuMgr = $this->getMenuManager();

        $menu = $menuMgr->getMenuRepository()->findOneByCode($menuCode);

        if (!($menu instanceof Menu)) {
            return new Response("");
        }

        $items = $menuMgr->fetchItemsTreeForMenu($menu, false);
        $items = $menuMgr->markActive($items, $this->getRequest()->getPathInfo());

        if (empty($items)) {
            return new Response("");
        }

        $params = array(
            'menu' => $menu,
            'items' => $items
        );

        if (!empty($title)) {
            $params['title'] = $title;
        }

        return $this->render("ArnmMenuBundle:Widgets\\Plain:renderMenu.html.twig", $params);
    }

    /**
     * Gets menu manager object
     *
     * @return MenuManager
     */
    protected function getMenuManager()
    {
        return $this->get('arnm_menu.manager');
    }
}
