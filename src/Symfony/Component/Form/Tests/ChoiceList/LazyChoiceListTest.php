<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Form\Tests\ChoiceList;

use Symfony\Component\Form\ChoiceList\ArrayChoiceList;
use Symfony\Component\Form\ChoiceList\LazyChoiceList;

/**
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class LazyChoiceListTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LazyChoiceList
     */
    private $list;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $loadedList;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    private $loader;

    private $value;

    protected function setUp()
    {
        $this->loadedList = $this->getMock('Symfony\Component\Form\ChoiceList\ChoiceListInterface');
        $this->loader = $this->getMock('Symfony\Component\Form\ChoiceList\Loader\ChoiceLoaderInterface');
        $this->value = function () {};
        $this->list = new LazyChoiceList($this->loader, $this->value);
    }

    public function testGetChoiceLoadersLoadsLoadedListOnFirstCall()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadChoiceList')
            ->with($this->value)
            ->will($this->returnValue($this->loadedList));

        // The same list is returned by the loader
        $this->loadedList->expects($this->exactly(2))
            ->method('getChoices')
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getChoices());
        $this->assertSame('RESULT', $this->list->getChoices());
    }

    /**
     * @group legacy
     */
    public function testGetChoicesUsesLoadedListWhenLoaderDoesNotCacheChoiceListOnFirstCall()
    {
        $this->loader->expects($this->at(0))
            ->method('loadChoiceList')
            ->with($this->value)
            ->willReturn($this->loadedList);

        $this->loader->expects($this->at(1))
            ->method('loadChoiceList')
            ->with($this->value)
            ->willReturn(new ArrayChoiceList(array('a', 'b')));

        // The same list is returned by the lazy choice list
        $this->loadedList->expects($this->exactly(2))
            ->method('getChoices')
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getChoices());
        $this->assertSame('RESULT', $this->list->getChoices());
    }

    public function testGetValuesLoadsLoadedListOnFirstCall()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadChoiceList')
            ->with($this->value)
            ->will($this->returnValue($this->loadedList));

        // The same list is returned by the loader
        $this->loadedList->expects($this->exactly(2))
            ->method('getValues')
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getValues());
        $this->assertSame('RESULT', $this->list->getValues());
    }

    public function testGetStructuredValuesLoadsLoadedListOnFirstCall()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadChoiceList')
            ->with($this->value)
            ->will($this->returnValue($this->loadedList));

        // The same list is returned by the loader
        $this->loadedList->expects($this->exactly(2))
            ->method('getStructuredValues')
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getStructuredValues());
        $this->assertSame('RESULT', $this->list->getStructuredValues());
    }

    public function testGetOriginalKeysLoadsLoadedListOnFirstCall()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadChoiceList')
            ->with($this->value)
            ->will($this->returnValue($this->loadedList));

        // The same list is returned by the loader
        $this->loadedList->expects($this->exactly(2))
            ->method('getOriginalKeys')
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getOriginalKeys());
        $this->assertSame('RESULT', $this->list->getOriginalKeys());
    }

    public function testGetChoicesForValuesForwardsCallIfListNotLoaded()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadChoicesForValues')
            ->with(array('a', 'b'))
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getChoicesForValues(array('a', 'b')));
        $this->assertSame('RESULT', $this->list->getChoicesForValues(array('a', 'b')));
    }

    public function testGetChoicesForValuesUsesLoadedList()
    {
        $this->loader->expects($this->exactly(3))
            ->method('loadChoiceList')
            ->with($this->value)
            // For BC, the same choice loaded list is returned 3 times
            // It should only twice in 4.0
            ->will($this->returnValue($this->loadedList));

        $this->loader->expects($this->never())
            ->method('loadChoicesForValues');

        $this->loadedList->expects($this->exactly(2))
            ->method('getChoicesForValues')
            ->with(array('a', 'b'))
            ->will($this->returnValue('RESULT'));

        // load choice list
        $this->list->getChoices();

        $this->assertSame('RESULT', $this->list->getChoicesForValues(array('a', 'b')));
        $this->assertSame('RESULT', $this->list->getChoicesForValues(array('a', 'b')));
    }

    // To be remove in 4.0
    public function testGetValuesForChoicesForwardsCallIfListNotLoaded()
    {
        $this->loader->expects($this->exactly(2))
            ->method('loadValuesForChoices')
            ->with(array('a', 'b'))
            ->will($this->returnValue('RESULT'));

        $this->assertSame('RESULT', $this->list->getValuesForChoices(array('a', 'b')));
        $this->assertSame('RESULT', $this->list->getValuesForChoices(array('a', 'b')));
    }

    public function testGetValuesForChoicesUsesLoadedList()
    {
        $this->loader->expects($this->exactly(3))
            ->method('loadChoiceList')
            ->with($this->value)
            // For BC, the same choice loaded list is returned 3 times
            // It should only twice in 4.0
            ->will($this->returnValue($this->loadedList));

        $this->loader->expects($this->never())
            ->method('loadValuesForChoices');

        $this->loadedList->expects($this->exactly(2))
            ->method('getValuesForChoices')
            ->with(array('a', 'b'))
            ->will($this->returnValue('RESULT'));

        // load choice list
        $this->list->getChoices();

        $this->assertSame('RESULT', $this->list->getValuesForChoices(array('a', 'b')));
        $this->assertSame('RESULT', $this->list->getValuesForChoices(array('a', 'b')));
    }
}
