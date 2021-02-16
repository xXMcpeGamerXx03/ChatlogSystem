<?php


namespace ChatlogSystem\chatlog;


class ChatLog {

    private $name;
    private $playerName;
    private $messages = [];

    public function __construct(string $name, string $playerName, array $messages) {
        $this->name = $name;
        $this->messages = $messages;
        $this->playerName = $playerName;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPlayerName(): string
    {
        return $this->playerName;
    }

    public function getMessages(): array {
        return $this->messages;
    }
}