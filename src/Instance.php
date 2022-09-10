<?php

namespace Envorra\FileClassResolver;

use ReflectionClass;
use ReflectionMethod;
use ReflectionException;
use ReflectionParameter;
use Envorra\FileClassResolver\Exceptions\ClassInstanceException;

/**
 * Instance
 *
 * @package Envorra\FileClassResolver
 */
class Instance
{
    protected ?ReflectionMethod $constructor;
    /**
     * @var ReflectionParameter[]
     */
    protected array $expectedParameters = [];
    protected ReflectionClass $reflection;
    protected int $requiredParameters = 0;
    protected int $totalParameters = 0;

    /**
     * @throws ReflectionException
     */
    protected function __construct(protected string $class, protected array $parameters)
    {
        $this->reflection = new ReflectionClass($this->class);
        $this->constructor = $this->reflection->getConstructor();

        if ($this->constructor) {
            $this->expectedParameters = $this->constructor->getParameters();
            $this->totalParameters = $this->constructor->getNumberOfParameters();
            $this->requiredParameters = $this->constructor->getNumberOfRequiredParameters();
        }
    }

    /**
     * @param  string  $class
     * @param  array   $parameters
     * @return object|null
     */
    public static function make(string $class, array $parameters = []): ?object
    {
        try {
            return (new self($class, $parameters))->tryInstantiate();
        } catch (ReflectionException) {
            return null;
        }
    }

    /**
     * @param  string  $class
     * @param  array   $parameters
     * @return object
     * @throws ReflectionException|ClassInstanceException
     */
    public static function makeOrFail(string $class, array $parameters = []): object
    {
        return (new self($class, $parameters))->instantiate();
    }

    /**
     * @param  string|int  $key
     * @return mixed
     */
    protected function getParameter(string|int $key): mixed
    {
        if (array_key_exists($key, $this->parameters)) {
            return $this->parameters[$key];
        }

        return null;
    }

    /**
     * Get the parameters in appropriate order.
     *
     * @return array
     * @throws ClassInstanceException
     */
    protected function getParameters(): array
    {
        if (count($this->parameters) < $this->requiredParameters) {
            throw new ClassInstanceException('Incorrect number of parameters.');
        }

        $parameters = [];
        $position = 0;

        foreach ($this->expectedParameters as $parameter) {
            $actual = $this->getParameter($parameter->getName());
            if ($actual && $this->validParameterType($parameter, $actual)) {
                $parameters[] = $actual;
                continue;
            }

            // If the array doesn't have a key with the named parameter, cycle through the parameters
            // given in the array until one with the correct type has been found.
            for ($counter = $position; $counter < count($this->parameters); $counter++) {
                $actual = $this->getParameter($counter);
                if ($actual && $this->validParameterType($parameter, $actual)) {
                    $parameters[] = $actual;
                    $position++;
                    continue 2;
                }
            }

            if (!$parameter->isOptional()) {
                throw new ClassInstanceException('Given parameters do not match with expected parameters.');
            }
        }

        return $parameters;
    }

    /**
     * @return object
     * @throws ClassInstanceException
     */
    protected function instantiate(): object
    {
        if (!$this->validClass()) {
            throw new ClassInstanceException($this->class.' is not instantiable.');
        }

        try {
            return $this->reflection->newInstanceArgs($this->getParameters());
        } catch (ReflectionException $exception) {
            throw new ClassInstanceException($exception->getMessage());
        }
    }

    /**
     * @return object|null
     */
    protected function tryInstantiate(): ?object
    {
        try {
            return $this->instantiate();
        } catch (ClassInstanceException) {
            return null;
        }
    }

    /**
     * @return bool
     */
    protected function validClass(): bool
    {
        return $this->reflection->isInstantiable();
    }

    /**
     * @param  ReflectionParameter  $reflectionParameter
     * @param  mixed                $parameter
     * @return bool
     */
    protected function validParameterType(ReflectionParameter $reflectionParameter, mixed $parameter): bool
    {
        if (is_null($parameter)) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        $expectedType = $reflectionParameter->getType()?->getName();

        /** @phpstan-ignore-next-line */
        if ($reflectionParameter->getType()?->isBuiltin()) {
            return match ($expectedType) {
                'int', 'integer' => is_integer($parameter),
                'bool', 'boolean' => is_bool($parameter),
                'string' => is_string($parameter),
                'float', 'real', 'double' => is_float($parameter),
                'array' => is_array($parameter),
                'object' => is_object($parameter),
                default => false
            };
        }

        return $parameter instanceof $expectedType;
    }
}
