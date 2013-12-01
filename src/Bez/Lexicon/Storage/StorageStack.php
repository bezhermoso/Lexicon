<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;


class StorageStack implements StorageInterface
{
    /**
     * @var array|StorageInterface[]
     */
    protected $storages;

    public function __construct()
    {
        $this->storages = array();
    }

    /**
     * @param string $field
     * @param string $context
     * @param null $value
     * @return bool
     */
    public function supports($field, $context, $value = null)
    {
        if (count($this->storages) > 0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Perform the key-value pair persistence.
     *
     * @param string $field
     * @param string $context
     * @param mixed $value
     * @throws \RuntimeException
     * @return boolean
     */
    public function store($field, $context, $value)
    {
        foreach ($this->storages as $candidate) {
            if ($candidate->supports($field, $context, $value)) {
                $candidate->store($field, $context, $value);
                return;
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Cannot find a suitable storage for storing field "%", context "%s" and value "%s"',
                $field,
                $context, is_scalar($value) ? $value : get_class($value)
            ));
    }

    /**
     * @param StorageInterface $storage
     * @return $this
     */
    public function addStorage(StorageInterface $storage)
    {
        $i = array_search($storage, $this->storages);

        if ($i === false) {
            $this->storages[] = $storage;
        }

        return $this;
    }

    /**
     * @param string $field
     * @param string $context
     * @throws \RuntimeException
     * @return mixed
     */
    public function retrieve($field, $context)
    {
        foreach ($this->storages as $storage) {
            if ($storage->supports($field, $context)) {
                return $storage->retrieve($field, $context);
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Cannot find a suitable storage for storing field "%" and context "%s"',
                $field,
                $context
            ));
    }

    /**
     * @param string $field
     * @param string $context
     * @throws \RuntimeException
     * @return mixed
     */
    public function remove($field, $context)
    {
        foreach ($this->storages as $storage) {
            if ($storage->supports($field, $context)) {
                return $storage->remove($field, $context);
            }
        }

        throw new \RuntimeException(
            sprintf(
                'Cannot find a suitable storage for storing field "%" and context "%s"',
                $field,
                $context
            ));
    }
}