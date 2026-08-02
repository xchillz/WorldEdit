<?php

declare(strict_types=1);

namespace worldedit\xchillz\domain\session;

interface SessionRepository
{
    public function get(string $playerName): EditSession;

    public function has(string $playerName): bool;

    public function save(EditSession $session);

    public function remove(string $playerName);
}
