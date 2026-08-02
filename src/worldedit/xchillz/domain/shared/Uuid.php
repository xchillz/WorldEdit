<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\shared;

final class Uuid
{
    /** @var string */
    private $value;

    private function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function generate(): self
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        $hex = bin2hex($data);

        $formatted = sprintf(
            '%s-%s-%s-%s-%s',
            substr($hex, 0, 8),
            substr($hex, 8, 4),
            substr($hex, 12, 4),
            substr($hex, 16, 4),
            substr($hex, 20, 12)
        );

        return new self($formatted);
    }

    public static function fromString(string $value): self
    {
        if (!self::isValid($value)) {
            throw new \InvalidArgumentException("Invalid UUID format: {$value}");
        }

        return new self($value);
    }

    public static function isValid(string $value): bool
    {
        return (bool) preg_match(
            '/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i',
            $value
        );
    }

    public function equals(Uuid $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
