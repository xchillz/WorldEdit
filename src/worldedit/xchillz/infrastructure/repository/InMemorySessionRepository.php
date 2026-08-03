<?php

declare(strict_types=1);

namespace worldedit\xchillz\infrastructure\repository;

use worldedit\xchillz\domain\session\EditSession;
use worldedit\xchillz\domain\session\SessionRepository;
use worldedit\xchillz\domain\selection\Selection;

final class InMemorySessionRepository implements SessionRepository
{
    /** @var EditSession[] */
    private $sessions = [];

    public function get(string $playerName): EditSession
    {
        if (!isset($this->sessions[$playerName])) {
            $this->sessions[$playerName] = new EditSession($playerName, new Selection());
        }

        return $this->sessions[$playerName];
    }

    public function has(string $playerName): bool
    {
        return isset($this->sessions[$playerName]);
    }

    public function save(EditSession $session)
    {
        $this->sessions[$session->getPlayerName()] = $session;
    }

    public function remove(string $playerName)
    {
        unset($this->sessions[$playerName]);
    }
}
