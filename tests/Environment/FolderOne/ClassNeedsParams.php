<?php

namespace Envorra\FileClassResolver\Tests\Environment\FolderOne;


class ClassNeedsParams
{
    public function __construct(public string $aString, public int $anInt, public array $anArray = [])
    {

    }
}
