<?php

namespace Envorra\FileClassResolver\Contracts;

use SplFileInfo;
use SplFileObject;

/**
 * Resolver
 *
 * @package Envorra\ClassResolver\Contracts
 */
interface Resolver
{
    /**
     * @param  SplFileObject|SplFileInfo|string  $file
     * @param  array                             $parameters
     * @return object|null
     */
    public static function make(SplFileObject|SplFileInfo|string $file, array $parameters = []): ?object;

    /**
     * @param  SplFileObject|SplFileInfo|string  $file
     * @return string|null
     */
    public static function resolve(SplFileObject|SplFileInfo|string $file): ?string;

    /**
     * @param  SplFileObject|SplFileInfo|string  $file
     * @return Resolver
     */
    public static function resolver(SplFileObject|SplFileInfo|string $file): Resolver;

    /**
     * @return string|null
     */
    public function getClass(): ?string;

    /**
     * @param  array  $parameters
     * @return object|null
     */
    public function getClassInstance(array $parameters = []): ?object;

    /**
     * @return string|null
     */
    public function getClassName(): ?string;

    /**
     * @return string|null
     */
    public function getFullyQualifiedClassName(): ?string;

    /**
     * @return string|null
     */
    public function getNamespace(): ?string;
}
