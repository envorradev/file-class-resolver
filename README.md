# File Class Resolver

A simple tool to resolve the fully qualified class name and an instance of the class given the path to the file.

# Install

```bash
$ composer require envorradev/file-class-resolver
```

# Usage

The following examples will use the following sample class:

```php
namespace SomeNamespace\SomeFolder;

class SomeClass {
    public function __construct(
        public string $aRequiredString,
        public int $anOptionalInt = 5,
    ) {}
}
```


```php
$filename = __DIR__.'/SomeFolder/SomeClass.php';
```

They also assume you have imported the resolver class via:

```php
use Envorra\FileClassResolver\ClassResolver;
```

## Resolve a Fully Qualified Class Name

```php
ClassResolver::resolve($filename);
```

Returns

```
'SomeNamespace\SomeFolder\SomeClass'
```

## Make an Instance

### Class with No Required Parameters

```php
ClassResolver::make($someOtherClassPath);
```

### Class with Required Parameters

Pass the parameters as an array as the parameter for the `make` method:

```php
ClassResolver::make($filename, ['string value', 10]);
```

Only the required parameters need to be passed:

```php
ClassResolver::make($filename, ['string value']);
```

You can pass the parameters as named parameters:

```php
ClassResolver::make($filename, ['aRequiredString' => 'string value', 'anOptionalInt' => 7]);
```

When using named parameters, the order does not matter:

```php
ClassResolver::make($filename, ['anOptionalInt' => 7, 'aRequiredString' => 'string value']);
```

## Get the Resolver Instance

```php
ClassResolver::resolver($filename);
```

Returns a `ClassResolver` Instance.

### Available ClassResolver Methods

In the below examples:

```php
$resolver = ClassResolver::resolve($filename);
```

#### getClass(): ?string

Gets the fully qualified class name.

Same as `ClassResolver::resolve($filename)` and `$resolver->getFullyQualifiedClassName()`

```php
$resolver->getClass();
```

Returns

```php
'SomeNamespace\SomeFolder\SomeClass'
```

#### getClassInstance(array $parameters = []): ?object

Get an instance of the class.

Same as `ClassResolver::make($filename, $parameters)`

```php
$resolver->getClassInstance(['string']);
```

Returns an instance of `SomeClass`

#### getClassName(): ?string

Gets onlt the name of the class.

```php
$resolver->getClassName();
```

Returns

```php
'SomeClass'
```

#### getClassNode(): \PhpParser\Node\Stmt\Class_

Gets the `\PhpParser\Node\Stmt\Class_` node.

See: [nikic/php-parser](https://github.com/nikic/PHP-Parser) and [Class_](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/Node/Stmt/Class_.php)

```php
$resolver->getNamespaceNode();
```

Returns something like:

```php
PhpParser\Node\Stmt\Class_ {#8563
    +name: PhpParser\Node\Identifier {#8550
        +name: "SomeClass",
    },
    +stmts: [
        PhpParser\Node\Stmt\ClassMethod {#8562
            +flags: 1,
            +byRef: false,
            +name: PhpParser\Node\Identifier {#8551
                +name: "__construct",
            },
            +params: [
                PhpParser\Node\Param {#8554
                    +type: PhpParser\Node\Identifier {#8553
                        +name: "string",
                    },
                    +byRef: false,
                    +variadic: false,
                    +var: PhpParser\Node\Expr\Variable {#8552
                        +name: "aRequiredString",
                    },
                    +default: null,
                    +flags: 1,
                    +attrGroups: [],
                },
                PhpParser\Node\Param {#8557
                    +type: PhpParser\Node\Identifier {#8556
                        +name: "int",
                    },
                    +byRef: false,
                    +variadic: false,
                    +var: PhpParser\Node\Expr\Variable {#8555
                        +name: "anOptionalInt",
                    },
                    +default: null,
                    +flags: 1,
                    +attrGroups: [],
                },
            ],
            +returnType: null,
            +stmts: [],
            +attrGroups: [],
        },
    ],
    +attrGroups: [],
    +namespacedName: null,
    +flags: 0,
    +extends: null,
    +implements: [],
}
```

#### getFullyQualifiedClassName(): ?string

Gets the fully qualified class name.

Same as `ClassResolver::resolve($filename)` and `$resolver->getClass()`

```php
$resolver->getFullyQualifiedClassName();
```

Returns

```php
'SomeNamespace\SomeFolder\SomeClass'
```

#### getNamespace(): ?string

Gets only the namespace of the class.

```php
$resolver->getNamespace();
```

Returns

```php
'SomeNamespace\SomeFolder'
```

#### getNamespaceNode(): \PhpParser\Node\Stmt\Namespace_

Gets the `\PhpParser\Node\Stmt\Namespace_` node.

See: [nikic/php-parser](https://github.com/nikic/PHP-Parser) and [Namespace_](https://github.com/nikic/PHP-Parser/blob/master/lib/PhpParser/Node/Stmt/Namespace_.php)

```php
$resolver->getNamespaceNode();
```

Returns something like:

```php
PhpParser\Node\Stmt\Namespace_ {#8549
    +name: PhpParser\Node\Name {#8548
        +parts: [
            "SomeNamespace",
            "SomeFolder",
        ],
    },
    +stmts: [
        PhpParser\Node\Stmt\Class_ {#8563
            +name: PhpParser\Node\Identifier {#8550
                +name: "SomeClass",
            },
            +stmts: [
                PhpParser\Node\Stmt\ClassMethod {#8562
                    +flags: 1,
                    +byRef: false,
                    +name: PhpParser\Node\Identifier {#8551
                        +name: "__construct",
                    },
                    +params: [
                        PhpParser\Node\Param {#8554
                            +type: PhpParser\Node\Identifier {#8553
                                +name: "string",
                            },
                            +byRef: false,
                            +variadic: false,
                            +var: PhpParser\Node\Expr\Variable {#8552
                                +name: "aRequiredString",
                            },
                            +default: null,
                            +flags: 1,
                            +attrGroups: [],
                        },
                        PhpParser\Node\Param {#8557
                            +type: PhpParser\Node\Identifier {#8556
                                +name: "int",
                            },
                            +byRef: false,
                            +variadic: false,
                            +var: PhpParser\Node\Expr\Variable {#8555
                                +name: "anOptionalInt",
                            },
                            +default: null,
                            +flags: 1,
                            +attrGroups: [],
                        },
                    ],
                    +returnType: null,
                    +stmts: [],
                    +attrGroups: [],
                },
            ],
            +attrGroups: [],
            +namespacedName: null,
            +flags: 0,
            +extends: null,
            +implements: [],
        },
    ],
}
```
