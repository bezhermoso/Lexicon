<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Tests;


use Bez\Lexicon\NodeAddress;

class NodeAddressTest extends \PHPUnit_Framework_TestCase
{
    public function testDeepNode()
    {
        $node = new NodeAddress('foo.bar.baz', '.');

        $this->assertEquals('foo', $node->getRoot());
        $this->assertFalse($node->isLeaf());

        $this->assertInstanceOf('Bez\\Lexicon\\NodeAddress', $node->getSubnode());
        $this->assertEquals('bar', $node->getSubnode()->getRoot());
        $this->assertFalse($node->getSubnode()->isLeaf());

        $this->assertInstanceOf('Bez\\Lexicon\\NodeAddress', $node->getSubnode()->getSubnode());
        $this->assertEquals('baz', $node->getSubnode()->getSubnode()->getRoot());
        $this->assertTrue($node->getSubnode()->getSubnode()->isLeaf());
        $this->assertNull($node->getSubnode()->getSubnode()->getSubnode());

    }

    public function testBuildingOfDeepNode()
    {
        $node = new NodeAddress('foo.bar.baz', '.');

        $data = $node->build(42);

        $this->assertTrue(isset($data['foo']['bar']['baz']));

        return $data;
    }

    /**
     * @depends testBuildingOfDeepNode
     * @param $data
     */
    public function testIfDataIsAssignedInBuiltNode($data)
    {
        $this->assertEquals(42, $data['foo']['bar']['baz']);
    }

    public function testAlternateResolutionTypes()
    {
        $node = new NodeAddress('foo.bar.baz', '.');

        $array = $node->build(null, NodeAddress::RESOLVE_AS_ARRAY);
        $object = $node->build(null, NodeAddress::RESOLVE_AS_OBJECT);
        $catalogue = $node->build(null, NodeAddress::RESOLVE_AS_CATALOG);

        $this->assertInternalType('array', $array);
        $this->assertInstanceOf('ArrayObject', $object);
        $this->assertInstanceOf('Bez\\Lexicon\\Catalog', $catalogue);

    }

    public function testDeepExtraction()
    {
        $node = new NodeAddress('foo.bar.baz', '.');
        $data = array('foo' => array('bar' => array('baz' => 42)));

        $this->assertEquals(42, $node->extract($data));

        $node = new NodeAddress('foo.bar', '.');

        $this->assertEquals(array('baz' => 42), $node->extract($data));

        $this->assertNull($node->extract(array('baz' => 42)));

    }

    public function testExistenceCheck()
    {
        $data = array('foo' => array('bar' => array('baz' => 42)));

        $node = new NodeAddress('foo.bar.baz', '.');

        $this->assertTrue($node->exists($data));

        $node = new NodeAddress('foo.bar', '.');

        $this->assertTrue($node->exists($data));
        $this->assertFalse($node->exists(array('baz' => 42)));

    }

    public function testAssignment()
    {
        $data = array('omega' => 'still here!', 'foo' => array('test' => 1));

        $node = new NodeAddress('foo.bar.baz', '.');

        $node->assign(42, $data);

        $this->assertTrue($node->exists($data));
        $this->assertEquals(42, $node->extract($data));

        $node = new NodeAddress('omega', '.');
        $this->assertEquals('still here!', $node->extract($data));

        $node = new NodeAddress('foo.test', '.');
        $this->assertEquals(1, $node->extract($data));
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAddresses1()
    {
        new NodeAddress('.test', '.');
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAddresses2()
    {
        new NodeAddress('test.', '.');
    }

    /**
     * @expectedException DomainException
     */
    public function testInvalidAddresses3()
    {
        new NodeAddress('test..fail', '.');
    }

} 