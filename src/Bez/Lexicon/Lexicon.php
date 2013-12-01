<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon;

use Bez\Lexicon\Storage\StorageInterface;

class Lexicon
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    public function set($field, $value, $context = null)
    {
        if (!$this->storage->supports($field, $context, $value)) {
            throw new \RuntimeException(
                sprintf(
                    '"%s" does not support field "%s", context "%s", and value "%s"',
                    $field,
                    $context,
                    is_scalar($value) ? $value : get_class($value)
                ));
        }

        $this->storage->store($field, $context, $value);

        return $this;
    }

    public function get($field, $context = null)
    {
        if (!$this->storage->supports($field, $context)) {
            throw new \RuntimeException(
                sprintf(
                    '"%s" does not support field "%s" and context "%s"',
                    $field,
                    $context
                ));
        }

        return $this->storage->retrieve($field, $context);
    }

    public function delete($field, $context = null)
    {
        if (!$this->storage->supports($field, $context)) {
            throw new \RuntimeException(
                sprintf(
                    '"%s" does not support field "%s" and context "%s"',
                    $field,
                    $context
                ));
        }

        $this->storage->remove($field, $context);

        return $this;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
    }
} 