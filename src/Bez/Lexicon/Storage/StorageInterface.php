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
    public function isReadonly();

    public function retrieve($address);

    public function store($address, $value);
}