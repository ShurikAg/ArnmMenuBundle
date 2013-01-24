<?php
namespace Arnm\MenuBundle\Tests\Manager;

use Arnm\MenuBundle\Manager\MenuManager;
/**
 * MenuManager test case.
 */
class MenuManagerTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Tests markActive method
     */
    public function testMarkActive()
    {
        $items = array(
            array(
                'id' => 1,
                'url' => null,
                'text' => 'item1',
                '__children' => array(
                    array(
                        'id' => 4,
                        'url' => '/boutique.html',
                        'text' => 'item4'
                    ),
                    array(
                        'id' => 5,
                        'url' => '/service.html',
                        'text' => 'item5'
                    )
                )
            ),
            array(
                'id' => 2,
                'url' => '/',
                'text' => 'item2'
            ),
            array(
                'id' => 3,
                'url' => '/custom-collection.html',
                'text' => 'item3',
                '__children' => array(
                    array(
                        'id' => 6,
                        'url' => '/service/url2.html',
                        'text' => 'item6'
                    )
                )
            )
        );

        $doctrine = $this->getMock('Doctrine\Bundle\DoctrineBundle\Registry');
        $mgr = new MenuManager($doctrine);

        $marked = $mgr->markActive($items, '');
        $this->assertEquals($items, $marked);

        $marked = $mgr->markActive($items, '/');
        $this->assertFalse(isset($marked[0]['current']));
        $this->assertTrue(isset($marked[1]['current']));
        $this->assertTrue($marked[1]['current']);
        $this->assertFalse(isset($marked[2]['current']));
        $this->assertFalse(isset($marked[3]['current']));

        $marked = $mgr->markActive($items, '/service.html');
        $this->assertTrue(isset($marked[0]['current_parent']));
        $this->assertTrue($marked[0]['current_parent']);
        $this->assertFalse(isset($marked[0]['__children'][0]['current']));
        $this->assertTrue(isset($marked[0]['__children'][1]['current']));
        $this->assertTrue($marked[0]['__children'][1]['current']);
        $this->assertFalse(isset($marked[1]['current']));
        $this->assertFalse(isset($marked[1]['current_parent']));
        $this->assertFalse(isset($marked[2]['current']));
        $this->assertFalse(isset($marked[2]['current_parent']));
        $this->assertFalse(isset($marked[3]['current']));
        $this->assertFalse(isset($marked[3]['current_parent']));
    }

}

