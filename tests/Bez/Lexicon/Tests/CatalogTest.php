<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Tests;


use Bez\Lexicon\Catalog;

class CatalogTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Catalog
     */
    protected $catalog;

    public function setUp()
    {
        $this->catalog = new Catalog(array('foo' => array('bar' => 42)));
    }

    public function testRetrieval()
    {
        $this->assertEquals(42, $this->catalog->get('foo.bar'));
        $this->assertEquals(array('bar' => 42), $this->catalog->get('foo'));
        $this->assertNull($this->catalog->get('foo.baz'));
    }

    public function testAssignment()
    {
        $this->catalog->set('foo.bar', 'forty two.');
        $this->assertEquals('forty two.', $this->catalog->get('foo.bar'));

        $this->catalog->set('foo.baz', 42);
        $this->assertEquals(42, $this->catalog->get('foo.baz'));
        $this->assertEquals('forty two.', $this->catalog->get('foo.bar'));
    }

    public function testTest()
    {

    }
}