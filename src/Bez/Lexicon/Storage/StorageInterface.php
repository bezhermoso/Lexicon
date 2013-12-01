<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;


interface StorageInterface
{
    /**
     * Evaluates if the storage supports a particular field, namespace, and value.
     *
     * The "store" method is called if this methods returns TRUE. Not if otherwise.
     *
     * @param string $field
     * @param string $context
     * @param mixed $value
     * @return boolean
     */
    public function supports($field, $context, $value = null);

    /**
     * Perform the key-value pair persistence.
     *
     * @param string $field
     * @param string $context
     * @param mixed $value
     * @return boolean
     */
    public function store($field, $context, $value);

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function retrieve($field, $context);

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function remove($field, $context);
}