<?php


namespace ChatlogSystem\commands\subCommands;


use ChatlogSystem\ChatlogSystem;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class ListSubCommand extends BaseSubCommand {

    protected function prepare(): void {

    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $list = array();
        if (empty(ChatlogSystem::getInstance()->getChatLogManager()->getChatLogs())) {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "Chatlogs: §cEs existieren keine Chatlogs!");
        } else {
            foreach (ChatlogSystem::getInstance()->getChatLogManager()->getChatLogs() as $chatLog) {
                array_push($list, $chatLog->getName());
            }

            $sender->sendMessage(ChatlogSystem::getPrefix() . "Chatlogs: §e" . implode("", $list));
        }
    }
}