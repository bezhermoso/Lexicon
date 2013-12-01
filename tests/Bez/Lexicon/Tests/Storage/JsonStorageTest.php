<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Tests\Storage;

use Bez\Lexicon\Lexicon;
use Bez\Lexicon\Storage\JsonStorage;

class JsonStorageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Lexicon
     */
    protected $lexicon;

    protected $initFile;
    protected $dataDir;

    /**
     * @var JsonStorage
     */
    protected $storage;

    public function setUp()
    {
        $this->dataDir = realpath(__DIR__ . '/../../../../data');
        $this->initFile = $this->dataDir . '/init.json';

        $this->lexicon = new Lexicon();
        $this->storage = new JsonStorage($this->dataDir . '/lexicon.json');
        $this->lexicon->setStorage($this->storage);

    }

    public function testInitializationOfNonExistentFile()
    {
        $this->assertFileNotExists($this->initFile, sprintf("%s already exists. Delete it before test run.", $this->initFile));

        $jsonStorage = new JsonStorage($this->initFile);
        $jsonStorage->initialize();

        $this->assertFileExists($this->initFile, sprintf('Failed to create file %s', $this->initFile));
        $this->assertEquals('{}', file_get_contents($this->initFile), sprintf('Failed to prepare %s properly', $this->initFile));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInitializationOfIncompatibleFile()
    {
        $jsonStorage = new JsonStorage($this->dataDir . '/incompatible.json');
        $jsonStorage->initialize();
    }

    public function testInitializationOfCompatibleFile()
    {
        $jsonStorage = new JsonStorage($this->dataDir . '/compatible.json');
        $jsonStorage->initialize();
    }

    public function testRetrievalOfUnsetField()
    {
        $value = $this->lexicon->get('foo');
        $this->assertNull($value);
    }

    public function testPersistence()
    {
        $value = "The quick brown fox jumps over the lazy dog.";
        $this->lexicon->set('quick-brown-fox', $value);
        $this->assertEquals($value, $this->lexicon->get('quick-brown-fox'));

        $this->lexicon->set('answer', 42);
        $this->assertEquals(42, $this->lexicon->get('answer'));

    }

    public function tearDown()
    {
        if (file_exists($this->initFile)) {
            unlink($this->initFile);
        }
    }
} 