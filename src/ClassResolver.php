<?php

namespace Envorra\FileClassResolver;

use SplFileInfo;
use SplFileObject;
use PhpParser\Node;
use PhpParser\Parser;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use Envorra\FileClassResolver\Contracts\Resolver;
use Envorra\FileClassResolver\NodeVisitors\Qualifier;

/**
 * ClassResolver
 *
 * @package Envorra\ClassResolver
 */
class ClassResolver implements Resolver
{
    protected NodeTraverser $nodeTraverser;
    /**
     * @var Node[]
     */
    protected array $nodes;
    protected Parser $parser;
    protected Qualifier $qualifier;

    /**
     * @param  SplFileObject  $file
     */
    protected function __construct(protected SplFileObject $file)
    {
        $this->parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        $this->qualifier = new Qualifier();
        $this->nodeTraverser = new NodeTraverser();
        $this->nodeTraverser->addVisitor($this->qualifier);
        $this->nodes = $this->nodeTraverser->traverse($this->getAbstractSyntaxTree());
    }

    /**
     * @inheritDoc
     */
    public static function make(SplFileObject|SplFileInfo|string $file, array $parameters = []): ?object
    {
        return ClassResolver::resolver($file)->getClassInstance($parameters);
    }

    /**
     * @inheritDoc
     */
    public static function resolve(SplFileObject|SplFileInfo|string $file): ?string
    {
        return ClassResolver::resolver($file)->getClass();
    }

    /**
     * @inheritDoc
     */
    public static function resolver(SplFileObject|SplFileInfo|string $file): ClassResolver
    {
        return new ClassResolver(ClassResolver::toFileObject($file));
    }

    /**
     * @param  SplFileObject|SplFileInfo|string  $file
     * @return SplFileObject
     */
    protected static function toFileObject(SplFileObject|SplFileInfo|string $file): SplFileObject
    {
        if ($file instanceof SplFileObject) {
            return $file;
        }

        if ($file instanceof SplFileInfo) {
            return $file->openFile();
        }

        return new SplFileObject($file);
    }

    /**
     * @inheritDoc
     */
    public function getClass(): ?string
    {
        return $this->getFullyQualifiedClassName();
    }

    /**
     * @inheritDoc
     */
    public function getClassInstance(array $parameters = []): ?object
    {
        return Instance::make($this->getClass(), $parameters);
    }

    /**
     * @inheritDoc
     */
    public function getClassName(): ?string
    {
        return $this->getClassNode()->name?->name;
    }

    /**
     * @return Class_
     */
    public function getClassNode(): Class_
    {
        return $this->qualifier->getClass();
    }

    /**
     * @inheritDoc
     */
    public function getFullyQualifiedClassName(): ?string
    {
        return $this->getNamespace().'\\'.$this->getClassName();
    }

    /**
     * @inheritDoc
     */
    public function getNamespace(): ?string
    {
        return implode('\\', $this->getNamespaceNode()->name?->parts ?? []);
    }

    /**
     * @return Namespace_
     */
    public function getNamespaceNode(): Namespace_
    {
        return $this->qualifier->getNamespace();
    }

    /**
     * @return array
     */
    protected function getAbstractSyntaxTree(): array
    {
        return $this->parser->parse($this->getFileContents());
    }

    /**
     * @return string|null
     */
    protected function getFileContents(): ?string
    {
        return $this->file->fread($this->file->getSize());
    }
}
