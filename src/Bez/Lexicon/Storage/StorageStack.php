<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;

use Bez\Lexicon\NodeAddress;

class StorageStack implements StorageInterface
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

    public function removeStorage($namespace)
    {
        unset($this->storages[$namespace], $this->separators[$namespace]);
    }

    public function isReadonly()
    {
        $result = array_filter($this->storages, function (StorageInterface $storage) {
           return $storage->isReadonly();
        });
        return count($result) > 0;
    }

    public function retrieve(NodeAddress $nodeAddress)
    {
        if (isset($this->storages[$nodeAddress->getRoot()])) {

            $storage = $this->storages[$nodeAddress->getRoot()];
            $separator = $this->separators[$nodeAddress->getRoot()];

            $subnode = $nodeAddress->getSubnode();

            if ($subnode->getSeparator() != $separator) {
                $nodeAddress = new NodeAddress($subnode->getAddress(), $separator);
            }
            return $storage->retrieve($nodeAddress);
        } else {
            $this->getFallbackStorage()->retrieve($nodeAddress);
        }
    }

    public function store(NodeAddress $nodeAddress, $value)
    {
        if (isset($this->storages[$nodeAddress->getRoot()])) {

            $storage = $this->storages[$nodeAddress->getRoot()];
            $separator = $this->separators[$nodeAddress->getRoot()];

            $subnode = $nodeAddress->getSubnode();

            if ($subnode->getSeparator() != $separator) {
                $nodeAddress = new NodeAddress($subnode->getAddress(), $separator);
            }
            return $storage->store($nodeAddress, $value);
        } else {
            $this->getFallbackStorage()->store($nodeAddress, $value);
        }
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