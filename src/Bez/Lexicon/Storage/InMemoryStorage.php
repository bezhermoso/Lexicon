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


    public function supports($address)
    {
        return true;
    }

    public function retrieve($address)
    {
        // TODO: Implement retrieve() method.
    }

    public function store($address)
    {
        // TODO: Implement store() method.
    }
}