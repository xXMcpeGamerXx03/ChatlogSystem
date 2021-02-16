<?php

namespace ChatlogSystem;

use ChatlogSystem\chatlog\ChatlogManager;
use ChatlogSystem\commands\ChatlogCommand;
use ChatlogSystem\commands\testcommand;
use ChatlogSystem\listener\PlayerChatListener;
use CortexPE\Commando\exception\HookAlreadyRegistered;
use CortexPE\Commando\PacketHooker;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use CortexPE\DiscordWebhookAPI\Webhook;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\server\NetworkInterfaceCrashEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class ChatlogSystem extends PluginBase implements Listener {

    private static $instance;
    private $chatLogManager;
    private $webhook;

    public static function getPrefix(): string {
        return "§c§lChatlog§4System §r§8» §r§7";
    }

    public function getWebhook(): ?Webhook {
        if ($this->getConfig()->get("Discord-Notify") == true) {
            return $this->webhook;
        }
        return null;
    }

    public function onEnable() {
        self::$instance = $this;
        $this->saveResource("config.yml");
        if (!PacketHooker::isRegistered()) {
            try {
                PacketHooker::register($this);
            } catch (HookAlreadyRegistered $e) {
            }
        }

        $this->getServer()->getPluginManager()->registerEvents(new PlayerChatListener(), $this);
        $this->getServer()->getCommandMap()->register("chatlog", new ChatlogCommand($this, "chatlog", "Chatlog Command"));
        $this->chatLogManager = new ChatlogManager();
        $this->getChatLogManager()->loadLogs();
        if ($this->getConfig()->get("Discord-Notify") == true) {
            $this->webhook = new Webhook($this->getConfig()->get("Discord-Webhook-Url"));
            if (!$this->webhook->isValid()) {
                $this->getServer()->getLogger()->error(self::getPrefix() . "§cBitte gebe in der §econfig.yml §ceine gültige Webhook-Url an!");
                $this->setEnabled(false);
            } else {
                $this->getServer()->getLogger()->info(self::getPrefix() . "§aDie angegebene URL des Webhooks existiert!");
            }
        }

        if (($webhook = ChatlogSystem::getInstance()->getWebhook()) != null) {
            $message = new Message();
            $embed = new Embed();

            if ($this->getConfig()->get("Discord-ServerImage-Url") !== "/") {
                $embed->setThumbnail($this->getConfig()->get("Discord-ServerImage-Url"));
            }

            $embed->setTitle("Server");
            $embed->setColor(0x03fc07);
            $embed->setDescription("Der Server ist nun Online!");
            $message->addEmbed($embed);
            $webhook->send($message);
        }

    }

    public function getConfig(): Config {
        return new Config($this->getDataFolder() . "config.yml", 2);
    }

    public static function getInstance(): self {
        return self::$instance;
    }

    public function getChatLogManager(): ChatlogManager
    {
        return $this->chatLogManager;
    }

    public function onDisable() {
        if (($webhook = ChatlogSystem::getInstance()->getWebhook()) != null) {
            $message = new Message();
            $embed = new Embed();

            if ($this->getConfig()->get("Discord-ServerImage-Url") !== "/") {
                $embed->setThumbnail($this->getConfig()->get("Discord-ServerImage-Url"));
            }

            $embed->setTitle("Server");
            $embed->setColor(0xfc0303);
            $embed->setDescription("Der Server ist nun Offline!");
            $message->addEmbed($embed);
            $webhook->send($message);
        }
    }
}