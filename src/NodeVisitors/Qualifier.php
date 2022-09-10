<?php

namespace Envorra\FileClassResolver\NodeVisitors;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;

/**
 * Qualifier
 *
 * @package Envorra\FileClassResolver\NodeVisitors
 */
class Qualifier extends AbstractVisitor
{
    protected Namespace_ $namespace;

    protected Class_ $class;

    /**
     * @return Class_
     */
    public function getClass(): Class_
    {
        return $this->class;
    }

    /**
     * @return Namespace_
     */
    public function getNamespace(): Namespace_
    {
        return $this->namespace;
    }

    /**
     * @inheritDoc
     */
    public function enterNode(Node $node): int|Node|null
    {
        if($node instanceof Namespace_) {
            $this->namespace = $node;
        }

        if($node instanceof Class_) {
            $this->class = $node;
        }

        return null;
    }


}
