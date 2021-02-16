<?php


namespace ChatlogSystem\commands\subCommands;


use ChatlogSystem\chatlog\ChatLog;
use ChatlogSystem\ChatlogSystem;
use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use CortexPE\Commando\exception\ArgumentOrderException;
use pocketmine\command\CommandSender;
use pocketmine\Server;

class CreateSubCommand extends BaseSubCommand {

    protected function prepare(): void {
        $this->registerArgument(0, new RawStringArgument("player", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if (isset($args["player"])) {
            $player = Server::getInstance()->hasOfflinePlayerData($args["player"]);
            if ($player) {
                if (ChatlogSystem::getInstance()->getChatLogManager()->playerNameExists($args["player"])) {
                    $log = new ChatLog($args["player"] . "-" . ChatlogSystem::getInstance()->getChatLogManager()->getFreeChatLogId($args["player"]), $args["player"], ChatlogSystem::getInstance()->getChatLogManager()->getMessages($args["player"]));
                    ChatlogSystem::getInstance()->getChatLogManager()->createChatlog($log, $sender->getName());
                    $sender->sendMessage(ChatlogSystem::getPrefix() . "§aDu hast einen Chatlog über den Spieler §e" . $args["player"] . " §8(§7Chatlog-Name: §4" . $log->getName() . "§8) §aerstellt!");
                } else {
                    $sender->sendMessage(ChatlogSystem::getPrefix() . "§cDer Spieler §e" . $args["player"] . " §chat noch nichts geschrieben!");
                }
            } else {
                $sender->sendMessage(ChatlogSystem::getPrefix() . "§cDer Spieler §e" . $args["player"] . " §cwar noch nie Online!");
            }
        } else {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog create <player>");
        }
    }
}