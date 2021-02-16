<?php


namespace ChatlogSystem\commands;


use ChatlogSystem\chatlog\ChatLog;
use ChatlogSystem\ChatlogSystem;
use ChatlogSystem\commands\subCommands\CreateSubCommand;
use ChatlogSystem\commands\subCommands\ListSubCommand;
use ChatlogSystem\commands\subCommands\RemoveSubCommand;
use ChatlogSystem\commands\subCommands\ViewSubCommand;
use CortexPE\Commando\args\BaseArgument;
use CortexPE\Commando\BaseCommand;
use pocketmine\command\CommandSender;

class ChatlogCommand extends BaseCommand {

    protected function prepare(): void {
        $this->setAliases(["cl"]);
        $this->setPermission("chatlog.command");
        $this->registerSubCommand(new CreateSubCommand("create", "Erstelle einen Chatlog", ["add", "c", "a"]));
        $this->registerSubCommand(new RemoveSubCommand("remove", "Lösche einen Chatlog", ["delete", "d", "r"]));
        $this->registerSubCommand(new ListSubCommand("list", "Sehe alle Chatlogs", ["all", "logs"]));
        $this->registerSubCommand(new ViewSubCommand("view", "Sehe alle Nachrichten eines Chatlogs", ["see", "v", "s"]));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        if ($sender->hasPermission($this->getPermission())) {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog create <player>");
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog remove <name>");
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog list");
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§c/chatlog view <name>");
        } else {
            $sender->sendMessage(ChatlogSystem::getPrefix() . "§cDafür hast du keine Rechte!");
        }
    }
}