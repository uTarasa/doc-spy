<?php

namespace App\Parser;

use App\Entity\Source;
use App\Reader\ReaderInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface ParserInterface
{
    public function run();

    public function setSource(Source $source): ParserInterface;

    public function getSource(): Source;

    public function setReader(ReaderInterface $reader): ParserInterface;

    public function getReader(): ?ReaderInterface;

    public function getItems(): ArrayCollection;

    public function getCount(): int;

    public function hasErrors(): bool;

    public function getErrorMessage(): string;

}