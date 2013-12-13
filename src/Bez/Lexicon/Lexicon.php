<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon;

use Bez\Lexicon\Storage\StorageInterface;
use Bez\Lexicon\Storage\StorageStack;

class Lexicon extends Catalog
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function getStorage()
    {
        if (null === $this->storage) {
            $this->storage = new StorageStack();
        }
        return $this->storage;
    }

    public function get($node)
    {
        if (!$node instanceof NodeAddress) {
            $node = $this->createNodeAddress($node);
        }
        return $this->getStorage()->retrieve($node);
    }

    public function set($node, $value)
    {
        $storage = $this->getStorage();

        if (!$storage->isReadonly()) {
            if (!$node instanceof NodeAddress) {
                $node = $this->createNodeAddress($node);
            }
            return $storage->store($node, $value);
        } else {
            throw new \RuntimeException('Storage is read-only.');
        }
    }
} 