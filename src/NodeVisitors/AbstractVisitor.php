<?php

namespace Envorra\FileClassResolver\NodeVisitors;

use PhpParser\NodeVisitorAbstract;
use Envorra\FileClassResolver\Contracts\Visitor;

/**
 * AbstractVisitor
 *
 * @package Envorra\ClassResolver\NodeVisitors
 */
abstract class AbstractVisitor extends NodeVisitorAbstract implements Visitor
{

}
