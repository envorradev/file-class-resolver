<?php

namespace Envorra\FileClassResolver\Tests;

use SplFileInfo;
use SplFileObject;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Namespace_;
use Envorra\FileClassResolver\ClassResolver;
use PHPUnit\Framework\TestCase;
use Envorra\FileClassResolver\Contracts\Resolver;
use Envorra\FileClassResolver\Tests\Environment\FolderOne\SimpleClassOne;
use Envorra\FileClassResolver\Tests\Environment\FolderTwo\SimpleClassTwo;
use Envorra\FileClassResolver\Tests\Environment\FolderTwo\ClassNeedsParam;
use Envorra\FileClassResolver\Tests\Environment\FolderOne\ClassNeedsParams;

/**
 * @coversDefaultClass \Envorra\FileClassResolver\ClassResolver
 */
class ClassResolverTest extends TestCase
{
    /**
     * @test
     * @covers ::resolve
     */
    public function it_can_resolve_class_from_string(): void
    {
        $this->assertEquals(SimpleClassOne::class, ClassResolver::resolve($this->simpleClassOne()));
    }

    /**
     * @test
     * @covers ::resolve
     */
    public function it_can_resolve_class_from_SplFileInfo(): void
    {
        $info = new SplFileInfo($this->simpleClassOne());
        $this->assertEquals(SimpleClassOne::class, ClassResolver::resolve($info));
    }

    /**
     * @test
     * @covers ::resolve
     */
    public function it_can_resolve_class_from_SplFileObject(): void
    {
        $object = new SplFileObject($this->simpleClassOne());
        $this->assertEquals(SimpleClassOne::class, ClassResolver::resolve($object));
    }

    /**
     * @test
     * @covers ::make
     */
    public function it_can_make_instance_of_class(): void
    {
        $this->assertInstanceOf(SimpleClassOne::class, ClassResolver::make($this->simpleClassOne()));
    }

    /**
     * @test
     * @covers ::resolver
     */
    public function it_can_get_resolver(): void
    {
        $this->assertInstanceOf(Resolver::class, ClassResolver::resolver($this->simpleClassOne()));
    }

    /**
     * @test
     * @covers ::getClass
     */
    public function it_can_get_class(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $this->assertEquals(SimpleClassTwo::class, $resolver->getClass());
    }

    /**
     * @test
     * @covers ::getFullyQualifiedClassName
     */
    public function it_can_get_fully_qualified_class(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $this->assertEquals(SimpleClassTwo::class, $resolver->getFullyQualifiedClassName());
    }

    /**
     * @test
     * @covers ::getClassName
     */
    public function it_can_get_class_name(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $this->assertEquals('SimpleClassTwo', $resolver->getClassName());
    }

    /**
     * @test
     * @covers ::getNamespace
     */
    public function it_can_get_namespace(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $this->assertEquals('Envorra\\FileClassResolver\\Tests\\Environment\\FolderTwo', $resolver->getNamespace());
    }

    /**
     * @test
     * @covers ::getClassNode
     */
    public function it_can_get_class_node(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $node = $resolver->getClassNode();
        $this->assertInstanceOf(Class_::class, $node);
        $this->assertEquals('SimpleClassTwo', $node->name->name);
    }

    /**
     * @test
     * @covers ::getClassInstance
     */
    public function it_can_get_class_instance_with_no_params(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $this->assertInstanceOf(SimpleClassTwo::class, $resolver->getClassInstance());
    }

    /**
     * @test
     * @covers ::getClassInstance
     */
    public function it_can_get_class_instance_with_one_param(): void
    {
        $resolver = ClassResolver::resolver(__DIR__.'/Environment/FolderTwo/ClassNeedsParam.php');
        $this->assertInstanceOf(ClassNeedsParam::class, $resolver->getClassInstance([10]));
    }

    /**
     * @test
     * @covers ::getClassInstance
     */
    public function it_can_get_class_instance_with_multiple_params(): void
    {
        $resolver = ClassResolver::resolver(__DIR__.'/Environment/FolderOne/ClassNeedsParams.php');
        $this->assertInstanceOf(ClassNeedsParams::class, $resolver->getClassInstance(['string', 6]));
    }

    /**
     * @test
     * @covers ::getClassInstance
     */
    public function it_can_get_class_instance_with_unordered_named_params(): void
    {
        $resolver = ClassResolver::resolver(__DIR__.'/Environment/FolderOne/ClassNeedsParams.php');
        $this->assertInstanceOf(ClassNeedsParams::class, $resolver->getClassInstance([
            'anArray' => [],
            'aString' => 'string',
            'anInt' => 5,
        ]));
    }

    /**
     * @test
     * @covers ::getClassInstance
     */
    public function it_returns_null_on_failure_to_get_class_instance(): void
    {
        $resolver = ClassResolver::resolver(__DIR__.'/Environment/FolderOne/ClassNeedsParams.php');
        $this->assertNull($resolver->getClassInstance());
    }

    /**
     * @test
     * @covers ::getNamespaceNode
     */
    public function it_can_get_namespace_node(): void
    {
        $resolver = ClassResolver::resolver($this->simpleClassTwo());
        $node = $resolver->getNamespaceNode();
        $this->assertInstanceOf(Namespace_::class, $node);
        $this->assertEquals([
            'Envorra',
            'FileClassResolver',
            'Tests',
            'Environment',
            'FolderTwo'
        ], $node->name->parts);
    }

    protected function simpleClassOne(): string
    {
        return __DIR__.'/Environment/FolderOne/SimpleClassOne.php';
    }

    protected function simpleClassTwo(): string
    {
        return __DIR__.'/Environment/FolderTwo/SimpleClassTwo.php';
    }
}
