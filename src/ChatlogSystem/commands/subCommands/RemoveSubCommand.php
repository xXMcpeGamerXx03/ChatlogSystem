<?php


namespace ChatlogSystem\commands\subCommands;


use ChatlogSystem\ChatlogSystem;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class RemoveSubCommand extends BaseSubCommand {

    protected function prepare(): void{
        $this->registerArgument(0, new RawStringArgument("name", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args["name"])) {
            $chatlog = ChatlogSystem::getInstance()->getChatLogManager()->getChatlogByName($args["name"]);
            if ($chatlog != null) {
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§aDu hast den Chatlog §e" . $chatlog->getName() . " §agelöscht!");
                ChatlogSystem::getInstance()->getChatLogManager()->removeChatlog($chatlog, $sender->getName());
            } else {
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§cDer Chatlog §e" . $args["name"] . " §cexistiert nicht oder ist nicht geladen!");
            }
        } else {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog remove <name>");
        }
    }
}