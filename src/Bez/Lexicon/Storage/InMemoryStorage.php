<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;

use Bez\Lexicon\Catalog;

class InMemoryStorage implements StorageInterface
{

    protected $catalog;

    public function __construct()
    {
        $this->catalog = new Catalog();
    }

    public function isReadonly()
    {
        return false;
    }

    public function retrieve($address)
    {
        $this->catalog->get($address);
    }

    public function store($address, $value)
    {
        $this->catalog->set($address, $value);
    }
}