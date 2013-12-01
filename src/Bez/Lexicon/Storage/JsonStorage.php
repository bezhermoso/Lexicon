<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon\Storage;


class JsonStorage implements StorageInterface
{

    protected $file;

    protected $initialized = false;

    protected $data;

    public function __construct($jsonFile)
    {
        $this->file = $jsonFile;
    }

    protected function initialize()
    {
        if ($this->initialized === false) {

            if (is_readable($this->file) && is_writable($this->file)) {
                $contents = file_get_contents($this->file);
                if ($contents[0] != '{' && $contents[count($contents)] != '}') {
                    throw new \RuntimeException(sprintf(
                        'File already contains data that "%s" cannot handle safely.',
                        __CLASS__
                    ));
                }
            } elseif (!file_exists($this->file)) {

                set_error_handler(array($this, 'customErrorHandler'));
                    $fh = fopen($this->file, 'w');
                    fwrite($fh, '{}');
                    fclose($fh);
                restore_error_handler();

            } else {
                throw new \RuntimeException(sprintf('"%s" must be readble and writable.', $this->file));
            }
        }
    }

    public function customErrorHandler($errstr, $errno, $severity, $errfile, $errline)
    {
        throw new \ErrorException($errstr, $errno, $severity, $errfile, $errline);
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

    protected function getData()
    {
        if ($this->data === null) {
            $this->data = json_decode(file_get_contents($this->file));
        }
        return $this->data;
    }

    public function setData($data)
    {
        $this->data = $data;
        file_put_contents($this->file, json_encode($this->data));
        return $this;
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
        $data = $this->getData();

        if (!isset($data[$field])) {
            $data[$field] = array();
        }

        if ($context === null) {
            $data[$field]['value'] = $value;
        } else {

            if (!isset($data[$field]['alt'])) {
                $data[$field]['alt'] = array();
            }
            $data[$field]['alt'][$context] = array('value' => $value);
        }

        $this->setData($data);
    }

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function retrieve($field, $context)
    {
        $data = $this->getData();

        if ($context === null && isset($data[$field]['value'])) {
            return $data[$field]['value'];
        } elseif ($context !== null && $field && isset($data[$field]['alt'][$context]['value'])) {
            return $data[$field]['alt'][$context]['value'];
        } elseif ($context !== null && $field === null) {

            $results = array();

            foreach($data as $id => $sub) {
                if (isset($sub['alt'][$context]['value'])) {
                    $results[$id] = $sub['alt'][$context]['value'];
                }
            }

            return $results;

        }
        return null;
    }

    /**
     * @param string $field
     * @param string $context
     * @return mixed
     */
    public function remove($field, $context)
    {
        $data = $this->getData();
        if ($context === null) {
            unset($data[$field]['value']);
            $this->setData($data);
        } elseif ($field !== null) {
            unset($data[$field]['alt'][$context]['value']);
            $this->setData($data);
        } else {
            foreach ($data as $id => &$sub) {
                unset($data[$id]['alt'][$context]['value']);
            }
            $this->setData($data);
        }
    }
}