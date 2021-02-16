<?php


namespace ChatlogSystem\listener;


use ChatlogSystem\ChatlogSystem;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;

class PlayerChatListener implements Listener {

    public function onChat(PlayerChatEvent $event) {
        ChatlogSystem::getInstance()->getChatLogManager()->addMessage($event->getPlayer()->getName(), $event->getMessage());
    }
}