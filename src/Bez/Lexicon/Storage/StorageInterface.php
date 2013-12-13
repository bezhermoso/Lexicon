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
     * Perform the key-value pair persistence.
     *
     * @param string $field
     * @param mixed $value
     * @internal param string $context
     * @return boolean
     */
    public function store($field, $value);

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function retrieve($field);

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function remove($field, $context);
}