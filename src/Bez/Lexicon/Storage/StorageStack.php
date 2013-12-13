<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;

use Bez\Lexicon\NodeAddress;

class StorageStack
{

    /**
     * @var StorageInterface[]
     */
    protected $storages = array();

    protected $separators = array();

    protected $fallbackStorage;

    public function addStorage($namespace, StorageInterface $storage, $separator = '.')
    {
        $this->storages[$namespace] = $storage;
        $this->separators[$namespace] = $separator;

        return $this;
    }

    public function retrieve($node)
    {
        foreach ($this->storages as $namespace => $storage) {

            $separator = $this->separators[$namespace];
            $address = new NodeAddress($node, $separator);
            if ($address->getRoot() == $namespace) {
                return $storage->retrieve($node);
            }
        }
        return null;
    }

    public function store($node, $value)
    {
        foreach ($this->storages as $namespace => $storage) {
            $separator = $this->separators[$namespace];
            $address = new NodeAddress($node, $separator);
            if ($address->getRoot() == $namespace && !$storage->isReadonly()) {
                return $storage->store($node, $value);
            }
        }
        throw new \RuntimeException(sprintf('Cannot find a suitable, writable storage for the node "%s".', $node));
    }

    public function removeStorage($namespace)
    {
        unset($this->storages[$namespace], $this->separators[$namespace]);
    }

    public function getFallbackStorage()
    {
        if (null === $this->fallbackStorage) {
            $this->fallbackStorage = new InMemoryStorage();
        }

        return $this->fallbackStorage;
    }

    public function setFallbackStorage(StorageInterface $storage)
    {
        if ($storage === $this) {
            throw new \LogicException('You cannot set a storage as its own fallback.');
        }

        $this->fallbackStorage = $storage;
    }
}