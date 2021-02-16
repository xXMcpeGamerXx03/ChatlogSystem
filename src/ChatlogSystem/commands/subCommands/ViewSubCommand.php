<?php


namespace ChatlogSystem\commands\subCommands;


use ChatlogSystem\ChatlogSystem;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use pocketmine\command\CommandSender;

class ViewSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("name", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args["name"])) {
            $chatlog = ChatlogSystem::getInstance()->getChatLogManager()->getChatlogByName($args["name"]);
            if ($chatlog != null) {
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§7Name: §e" . $chatlog->getName());
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§7Player: §e" . $chatlog->getPlayerName());
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§aMessages: §8(§7Format: §etag.monat.Jahr Stunde:minute:sekunde.millisekunde§8)");
                if (empty($chatlog->getMessages())) {
                    $sender->sendMessage(ChatlogSystem::getPrefix() . "§cKeine Nachrichten vorhanden!");
                } else {
                    foreach ($chatlog->getMessages() as $i => $message) {
                        $sender->sendMessage($message);
                    }
                }
            } else {
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§cDer Chatlog §e" . $args["name"] . " §cexistiert nicht oder ist nicht geladen!");
            }
        } else {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog view <name>");
        }
    }
}