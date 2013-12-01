<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;


abstract class AbstractFileStorage implements StorageInterface
{
    function __destruct()
    {
        try {
            $this->close();
        } catch (\Exception $e) {

        }
    }

    /**
     * Called upon exit of program.
     *
     * @return mixed
     */
    abstract function close();

}