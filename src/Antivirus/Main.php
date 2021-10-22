<?php

namespace Antivirus;

use Antivirus\commands\AntivirusCommand;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\Player;
use Antivirus\modules\Autoclick;
use Antivirus\modules\ReachModule;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase implements Listener{

    public static Main $instance;

    public static array $sessions = [];

    public static array $alerts = [];

    public function onLoad(){
        self::$instance = $this;
    }

    public function onEnable() {
        @mkdir($this->getDataFolder() . "LOGS");
        $this->getServer()->getPluginManager()->registerEvents(new Autoclick(), $this);
        $this->getServer()->getPluginManager()->registerEvents(new ReachModule(), $this);
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getServer()->getCommandMap()->register("antivirus", new AntivirusCommand());
    }

    public static function getInstance() : Main
    {
        return self::$instance;
    }

    /**
     * @return array
     */
    public function getSessions(): array
    {
        return self::$sessions;
    }

    public function openSession(Player $player) : void
    {
        if ($this->getSession($player->getName()) === null) {
            self::$sessions[$player->getName()] = new Session($player);
        }
    }

    public function getSession(string $name) : ?Session
    {
        if (!isset(self::$sessions[$name])) return null;
        return self::$sessions[$name];
    }

    public function announce(string $message) : void{
        foreach($this->getServer()->getOnlinePlayers() as $player) {
            if($player->hasPermission("antivirus.announce") and in_array($player->getName(), self::$alerts)){
                $player->sendMessage(TextFormat::colorize("&l&3AntiVirus-ALPHA") . " > " . TextFormat::RESET . $message);
            }
        }
    }

    public function onJoin(PlayerJoinEvent $event) : void {
        $player = $event->getPlayer();
        $this->openSession($player);
    }
}