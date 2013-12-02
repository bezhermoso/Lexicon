<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Tests\Storage;


use Bez\Lexicon\Lexicon;
use Bez\Lexicon\Storage\DirectoryStorage;

class DirectoryStorageTest extends \PHPUnit_Framework_TestCase
{
    protected $dir;

    protected $lexicon;

    public function setUp()
    {
        $this->dir = realpath(__DIR__ . '/../../../../data/configDir');

        if (!$this->dir OR ($this->dir && !is_writable($this->dir))) {
            throw new \RuntimeException("Make sure directory tests/data/configDir exists and is writable.");
        }

        $this->lexicon = new Lexicon();
        $this->lexicon->setStorage(new DirectoryStorage($this->dir));
    }

    public function testPersistence()
    {
        $this->lexicon->set('about_us', 'Hello, world!');
        $this->assertEquals('Hello, world!', $this->lexicon->get('about_us'));

        $alpha = range('A', 'Z');

        $this->lexicon->set('alphabet', $alpha);
        $this->assertEquals($alpha, $this->lexicon->get('alphabet'));
    }
} 