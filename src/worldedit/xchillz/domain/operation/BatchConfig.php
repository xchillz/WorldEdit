<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\operation;

final class BatchConfig
{
    const DEFAULT_SIZE = 50;
    const MIN_BATCH_SIZE = 25;
    const MAX_BATCH_SIZE = 200;

    /** @var int */
    private $size;

    public function __construct(int $size)
    {
        if ($size < self::MIN_BATCH_SIZE) {
            throw new \InvalidArgumentException("Batch size too low, minimum is: " . self::MIN_BATCH_SIZE);
        }

        if ($size > self::MAX_BATCH_SIZE) {
            throw new \InvalidArgumentException("Batch size too high, maximum is: " . self::MAX_BATCH_SIZE);
        }

        $this->size = $size;
    }

    public static function default(): self
    {
        return new self(self::DEFAULT_SIZE);
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
