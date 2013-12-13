<?php
/**
 * Copyright 2013 Bezalel Hermoso <bezalelhermoso@gmail.com>
 * 
 * This project is free software released under the MIT license:
 * http://www.opensource.org/licenses/mit-license.php 
 */

namespace Bez\Lexicon;


class Catalog
{
    protected $nodeSeparator;

    protected $data;

    public function __construct($data = null, $nodeSeparator = '.')
    {
        $this->setNodeSeparator($nodeSeparator);

        if (is_array($data) OR $data instanceof \ArrayAccess) {
            $this->data = $data;
        }
    }

    public function setNodeSeparator($separator)
    {
        $this->nodeSeparator = $separator;

        return $this;
    }

    /**
     * @param $node
     * @return NodeAddress
     */
    protected function createNodeAddress($node)
    {
        return new NodeAddress($node, $this->nodeSeparator);
    }

    public function get($node)
    {
        if (!$node instanceof NodeAddress) {
            $node = $this->createNodeAddress($node);
        }
        return $node->extract($this->data);
    }

    public function set($node, $value)
    {
        if (!$node instanceof NodeAddress) {
            $node = $this->createNodeAddress($node);
        }
        $node->assign($value, $this->data);
    }

    public function has($node)
    {
        if (!$node instanceof NodeAddress) {
            $node = $this->createNodeAddress($node);
        }
        return $node->exists($this->data);
    }
}