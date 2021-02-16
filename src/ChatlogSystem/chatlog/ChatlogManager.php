<?php


namespace ChatlogSystem\chatlog;


use ChatlogSystem\ChatlogSystem;
use CortexPE\DiscordWebhookAPI\Embed;
use CortexPE\DiscordWebhookAPI\Message;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\MainLogger;

class ChatlogManager {

    /** @var ChatLog[] $chatLogs */
    private $chatLogs = [];
    /** @var array[] $messages */
    private $messages = [];

    public function getFreeChatLogId(string $player): int {
        $id = 1;
        if ($this->getPlayerConfig()->exists($player)) {
            $id = $id + $this->getPlayerConfig()->get($player);
        }
        return $id;
    }

    public function createChatlog(ChatLog $chatLog, string $by) {
        $this->chatLogs[$chatLog->getName()] = $chatLog;
        $cfg = $this->getPlayerConfig();
        $cfg->set($chatLog->getPlayerName(), $cfg->get($chatLog->getPlayerName()) + 1);
        $cfg->save();
        $cfg2 = $this->getConfig();
        $cfg2->setNested($chatLog->getName() . ".enabled", true);
        $cfg2->setNested($chatLog->getName() . ".name", $chatLog->getName());
        $cfg2->setNested($chatLog->getName() . ".playerName", $chatLog->getPlayerName());
        $cfg2->save();

        if (ChatlogSystem::getInstance()->getConfig()->get("Discord-Notify") == true) {
            if (($webhook = ChatlogSystem::getInstance()->getWebhook()) != null) {
                $message = new Message();
                $embed = new Embed();

                if ($this->getConfig()->get("Discord-ServerImage-Url") !== "/") {
                    $embed->setThumbnail($this->getConfig()->get("Discord-ServerImage-Url"));
                }

                $embed->setTitle("Chatlog");
                $embed->setColor(0x03fc07);
                $embed->setDescription("Ein neuer Chatlog wurde erstellt!");
                $embed->addField("**__Player__**", $chatLog->getPlayerName(), false);
                $embed->addField("**__By__**", $by, false);
                $embed->addField("**__Name__**", $chatLog->getName(), false);
                $message->addEmbed($embed);
                $webhook->send($message);
            }
        }
    }

    public function removeChatlog(ChatLog $chatLog, string $by) {
        unset($this->chatLogs[array_search($chatLog, $this->chatLogs)]);
        $cfg = $this->getPlayerConfig();
        $cfg->set($chatLog->getPlayerName(), $cfg->get($chatLog->getPlayerName()) - 1);
        $cfg->save();
        $cfg2 = $this->getConfig();
        $cfg2->remove($chatLog->getName());
        $cfg2->save();

        if (ChatlogSystem::getInstance()->getConfig()->get("Discord-Notify") == true) {
            if (($webhook = ChatlogSystem::getInstance()->getWebhook()) != null) {
                $message = new Message();
                $embed = new Embed();

                if ($this->getConfig()->get("Discord-ServerImage-Url") !== "/") {
                    $embed->setThumbnail($this->getConfig()->get("Discord-ServerImage-Url"));
                }

                $embed->setTitle("Chatlog");
                $embed->setDescription("Ein Chatlog wurde gelöscht!");
                $embed->setColor(0xfc0303);
                $embed->addField("**__Name__**", $chatLog->getName(), false);
                $embed->addField("**__By__**", $by, false);
                $message->addEmbed($embed);
                $webhook->send($message);
            }
        }
    }

    public function loadLogs() {
        foreach ($this->getConfig()->getAll() as $name => $data) {
            if (isset($data["enabled"]) && isset($data["name"]) && isset($data["playerName"])) {
                if ($data["enabled"] == true) {
                    $this->chatLogs[$data["name"]] = new ChatLog($data["name"], $data["playerName"], []);
                }
            } else {
                MainLogger::getLogger()->error("§cDer Chatlog §e" . $name . " §ckonnte nicht geladen werden! Grund: §eUngültiges Format");
            }
        }
    }

    public function playerNameExists(string $playerName): bool {
        if (isset($this->messages[$playerName])) {
            return true;
        } else {
            return false;
        }
    }

    public function getConfig(): Config {
        return new Config(ChatlogSystem::getInstance()->getDataFolder() . "chatlogs.yml", 2);
    }

    public function getPlayerConfig(): Config {
        return new Config(ChatlogSystem::getInstance()->getDataFolder() . "players.yml", 2);
    }

    /** @return ChatLog[] */
    public function getChatLogs(): array {
        return $this->chatLogs;
    }

    public function getChatlogByName(string $name): ?ChatLog {
        foreach ($this->getChatLogs() as $chatLog) {
            if ($chatLog->getName() == $name) {
                return $chatLog;
            }
        }
        return null;
    }

    public function getMessages(string $playerName): array {
        if (isset($this->messages[$playerName])) {
            return $this->messages[$playerName];
        }
        return [];
    }

    public function addMessage(string $playerName, string $message) {
        $dateTime = new \DateTime("now");
        $dateTime->setTimezone(new \DateTimeZone("Europe/Berlin"));
        $this->messages[$playerName][] = "§8[§e" . $dateTime->format("d.m.Y H:i:s.v") . "§8] §7| §c" . $playerName . ": §r§e" . $message;
    }
}