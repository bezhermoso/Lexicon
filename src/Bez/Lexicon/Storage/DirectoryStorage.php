<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;


class DirectoryStorage implements StorageInterface
{

    protected $dir;

    protected $initialized = false;

    public function __construct($dir)
    {
        $this->dir = $dir;
    }

    public function initialize()
    {
        if ($this->initialized === false) {
            if (!is_readable($this->dir) || !is_writable($this->dir)) {
                throw new \RuntimeException(sprintf('%s must be readable and writable.', $this->dir));
            }
            $this->initialized = true;
        }
    }
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
    public function supports($field, $context, $value = NULL)
    {
        return true;
    }

    /**
     * Perform the key-value pair persistence.
     *
     * @param string $field
     * @param string $context
     * @param mixed $value
     * @return boolean
     */
    public function store($field, $context, $value)
    {

        if ($context === null) {
            $file = $this->dir . DIRECTORY_SEPARATOR . $field;
        } else {
            $file = $this->dir . DIRECTORY_SEPARATOR . $context . DIRECTORY_SEPARATOR . $field;
        }

        if (is_writable($file)) {
            file_put_contents($file, json_encode($value));
        } elseif (!file_exists($file)) {
            if($context === null) {
                $this->createFile($file);
            } else {
                $this->createDirectory(dirname($file));
            }
            $this->store($field, $context, $value);
        }
    }

    public function createFile($file)
    {
        set_error_handler(array($this, 'customErrorHandler'));
            $fh = fopen($file, 'w');
            fclose($fh);
        restore_error_handler();
    }

    public function createDirectory($dir)
    {
        set_error_handler(array($this, 'customErrorHandler'));
            mkdir($dir);
        restore_error_handler();
    }

    public function customErrorHandler($errstr, $errno, $severity, $errfile, $errline)
    {
        throw new \ErrorException($errstr, $errno, $severity, $errfile, $errline);
    }

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function retrieve($field, $context)
    {
        if ($context === null) {
            $file = $this->dir . DIRECTORY_SEPARATOR . $field;
        } else {
            $file = $this->dir . DIRECTORY_SEPARATOR . $context . DIRECTORY_SEPARATOR . $field;
        }

        if (is_readable($file)) {
            return json_decode(file_get_contents($file));
        } else {
            return null;
        }
    }

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function remove($field, $context)
    {
        if ($context === null) {
            $file = $this->dir . DIRECTORY_SEPARATOR . $field;
        } elseif($field !== null) {
            $file = $this->dir . DIRECTORY_SEPARATOR . $context . DIRECTORY_SEPARATOR . $field;
        } else {
            $file = $this->dir . DIRECTORY_SEPARATOR . $context;
        }

        if (file_exists($file)) {
            set_error_handler(array($this, 'customErrorHandler'));
                unlink($file);
            restore_error_handler();
        }
    }
}