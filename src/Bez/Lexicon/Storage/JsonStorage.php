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

    public  function initialize()
    {
        if ($this->initialized === false) {
            if (is_readable($this->file) && is_writable($this->file)) {
                $contents = file_get_contents($this->file);

                if (strlen($contents) == 0) {
                    set_error_handler(array($this, 'customErrorHandler'));
                        $fh = fopen($this->file, 'w');
                        fwrite($fh, '{}');
                        fclose($fh);
                    restore_error_handler();
                } elseif ($contents[0] != '{' && $contents[count($contents)] != '}') {
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
            $this->initialized = true;
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
        $this->initialize();

        if ($this->data === null) {
            $this->data = json_decode(file_get_contents($this->file));
        }
        return $this->data;
    }

    public function setData($data)
    {
        $this->initialize();

        $this->data = $data;

        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            $json = json_encode($this->data,JSON_PRETTY_PRINT | JSON_BIGINT_AS_STRING);
        } else {
            $json = json_encode($this->data);
        }
        file_put_contents($this->file, $json);
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

        if (!isset($data->$field)) {
            $data->$field = (object) array();
        }

        if ($context === null) {
            $data->$field->definition = $value;
        } else {

            if (!isset($data->$field->contexts)) {
                $data->$field->contexts = (object) array();
            }
            $data->$field->contexts->$context = (object) array('definition' => $value);
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

        if ($context === null && isset($data->$field->definition)) {
            return $data->$field->definition;
        } elseif ($context !== null && $field && isset($data->$field->contexts->$context->definition)) {
            return $data->$field->contexts->$context->definition;
        } elseif ($context !== null && $field === null) {

            $results = array();

            foreach($data as $id => $sub) {
                if (isset($sub->contexts->$context->definition)) {
                    $results[$id] = $sub->contexts->$context->definition;
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
            unset($data->$field->definition);
            $this->setData($data);
        } elseif ($field !== null) {
            unset($data->$field->contexts->$context->definition);
            $this->setData($data);
        } else {
            foreach ($data as $id => &$sub) {
                unset($data->$id->contexts->$context->definition);
            }
            $this->setData($data);
        }
    }
}