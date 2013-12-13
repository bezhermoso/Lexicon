<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon;

class NodeAddress
{
    const RESOLVE_AS_CATALOG = 1;
    const RESOLVE_AS_ARRAY = 2;
    const RESOLVE_AS_OBJECT = 3;

    protected $node;

    protected $separator;

    protected $subAddress;

    protected $subnode;

    protected $root;

    /**
     * @param $node
     * @param string $separator
     * @throws \DomainException
     */
    public function __construct($node, $separator = '.')
    {
        $this->separator = $separator;
        if($this->isValid($node)) {
            $this->parse($node, $separator);
            $this->node = $node;
        } else {
            throw new \DomainException('Address "' . $node . '" is not formatted correctly.');
        }
    }

    /**
     * @return mixed
     */

    public function getRoot()
    {
        return $this->root;
    }

    /**
     * @return bool
     */
    public function isLeaf()
    {
        return $this->subAddress == null;
    }

    /**
     * @return $this|null
     */
    public function getSubnode()
    {

        if ($this->isLeaf()) {
            return null;
        } elseif (null === $this->subnode) {
            $this->subnode = new static($this->subAddress, $this->separator);
        }

        return $this->subnode;
    }

    /**
     * @param $node
     * @param string $separator
     */
    private function parse($node, $separator = '.')
    {
        if (false !== ($pos = strpos($node, $separator))) {
            $this->root = substr($node, 0, $pos);
            $this->subAddress = substr($node, $pos + 1, strlen($node) - $pos);
            $this->subnode = new static($this->subAddress, $separator);
        } else{
            $this->root = $node;
        }
    }

    /**
     * @param $node
     * @return int
     */
    public function isValid($node)
    {
        $pattern = '/^[a-z0-9A-Z_]+(' . preg_quote($this->separator) . '[a-z0-9A-Z_]+){0,}$/';
        return preg_match($pattern, $node);
    }

    /**
     * @param null $value
     * @param int $resolveAs
     * @return array|\ArrayObject|Catalog
     */
    public function build($value = null, $resolveAs = self::RESOLVE_AS_OBJECT)
    {

        $data = array(
                    $this->getRoot() =>
                        $this->isLeaf()  ?
                            $value :
                            $this->getSubnode()->build($value, $resolveAs));

        switch($resolveAs) {
            case self::RESOLVE_AS_OBJECT:
                $data = new \ArrayObject($data);
                break;

            case self::RESOLVE_AS_CATALOG:
                $data = new Catalog($data, $this->separator);
                break;
        }

        return $data;
    }

    /**
     * @param $data
     * @return bool
     * @throws \InvalidArgumentException
     */
    public function exists($data)
    {
        if (!is_array($data) && !$data instanceof \ArrayObject) {
            throw new \InvalidArgumentException(sprintf('Expected array or ArrayObject. "%s" given.', get_class($data)));
        }

        if (isset($data[$this->getRoot()])) {
            return $this->isLeaf() ? true : $this->getSubnode()->exists($data[$this->getRoot()]);
        } else {
            return false;
        }
    }

    /**
     * @param $data
     * @return null
     * @throws \InvalidArgumentException
     */
    public function extract($data)
    {
        if (!is_array($data) && !$data instanceof \ArrayObject) {
            throw new \InvalidArgumentException(sprintf('Expected array or ArrayObject. "%s" given.', get_class($data)));
        }

        if (isset($data[$this->getRoot()])) {
            $subData = $data[$this->getRoot()];
            return $this->isLeaf() ? $subData : $this->getSubnode()->extract($subData);
        } else {
            return null;
        }
    }

    public function assign($value, &$data)
    {
        if (!is_array($data) && !$data instanceof \ArrayObject) {
            throw new \InvalidArgumentException(sprintf('Expected array or ArrayObject. "%s" given.', get_class($data)));
        }

        $resolveAs = is_array($data) ? self::RESOLVE_AS_ARRAY : self::RESOLVE_AS_OBJECT;

        if (!isset($data[$this->getRoot()])) {
            $built = $this->build($value, $resolveAs);
            $data[$this->getRoot()] = $built[$this->getRoot()];
        }

        if ($this->isLeaf()) {
            $data[$this->getRoot()] = $value;
        } else {
            $this->getSubnode()->assign($value, $data[$this->getRoot()]);
        }
    }
}