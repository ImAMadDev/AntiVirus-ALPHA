<?php

namespace Antivirus;

use formapi\SimpleForm;
use pocketmine\network\mcpe\protocol\ModalFormRequestPacket;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;

class Session{

    public Player $player;

    public string $name;

    public array $cps = [];

    public float $reach = 0.0;

    public function __construct(Player $player)
    {
        $this->player = $player;
        $this->name = $player->getName();
        @mkdir(Main::getInstance()->getDataFolder() . "LOGS" . DIRECTORY_SEPARATOR . $player->getLowerCaseName());
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getLogs() : Config
    {
        $date = gmdate("M_d_Y");
        return new Config(Main::getInstance()->getDataFolder() . "LOGS" . DIRECTORY_SEPARATOR . strtolower($this->getName()) . DIRECTORY_SEPARATOR . $this->getName() . "_" . $date . ".txt", Config::ENUM);
    }

    /**
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    public function saveLogs(string $data) : void
    {
        $file = $this->getLogs();
        $file->set($data);
        $file->save();
    }

    public function getClicks(): int{
        if(!isset($this->cps[0])){
            return 0;
        }
        $time = $this->cps[0];
        $clicks = $this->cps[1];
        if($time !== time()){
            unset($this->cps);
            return 0;
        }
        return $clicks;
    }

    public function addClick(): void{
        if(!isset($this->cps[0])){
            $this->cps = [time(), 0];
        }
        $time = $this->cps[0];
        $clicks = $this->cps[1];
        if($time !== time()){
            $time = time();
            $clicks = 0;
        }
        $clicks++;
        $this->cps = [$time, $clicks];
        if($this->getClicks() >= 20){
            $cps = $this->getClicks();
            $ping = $this->getPlayer()->getPing();
            $tps = Server::getInstance()->getTickUsageAverage();
            $date = gmdate("H:i:s");
            $this->saveLogs("ping: " . $ping . " current_tps: " . $tps . " time: " . $date . " CPS: " . $cps);
            Main::getInstance()->announce($this->getName() . " is probably using autoclick, current cps " . $cps);
        }
    }

    public function setReach(float $reach) : void
    {
        $this->reach = $reach;
        $ping = $this->getPlayer()->getPing();
        $tps = Server::getInstance()->getTickUsageAverage();
        $date = gmdate("H:i:s");
        $this->saveLogs("ping: " . $ping . " current_tps: " . $tps . " time: " . $date . " REACH: " . $reach);
        Main::getInstance()->announce($this->getName() . " is probably using reach: " . $reach);
    }

    public function sendLogs(Player $player) : void
    {
        $form = new SimpleForm(function (Player $player, mixed $data = null){
          if($data == null){
              return;
          }
          if ($data == "close"){
              return;
          }
          if (file_exists(Main::getInstance()->getDataFolder() . "LOGS/" . strtolower($this->getName()) . "/" . $data . ".txt")) {
              $this->sendLog($player, $data);
          }
        });
        $form->setTitle("{$this->getName()}'s logs");
        $form->addButton("Close", -1, "", "close");
        foreach (glob(Main::getInstance()->getDataFolder() . "LOGS" . DIRECTORY_SEPARATOR . strtolower($this->getName()) . DIRECTORY_SEPARATOR . "*.txt") as $log) {
            $form->addButton(basename($log, ".txt"), -1, "", basename($log, ".txt"));
            $player->sendMessage(basename($log, ".txt"));
        }
        $player->sendForm($form);
    }

    public function sendLog(Player $player, string $file) : void{
        $logs = (new Config(Main::getInstance()->getDataFolder() . "LOGS" . DIRECTORY_SEPARATOR . strtolower($this->getName()) . DIRECTORY_SEPARATOR . $file . ".txt", Config::ENUM))->getAll();
        $keys = array_keys($logs);
        $content = implode("\n", $keys);
        $form = new SimpleForm(function(Player $player, mixed $data = null) use($file,$player){
            if($data == null or $data == 1){
                return;
            }
            if($data == 0){
                if(file_exists(Main::getInstance()->getDataFolder() . "LOGS/" . strtolower($this->getName()) . "/" . $file . ".txt")) {
                    unlink(Main::getInstance()->getDataFolder() . "LOGS/" . strtolower($this->getName()) . "/" . $file . ".txt");
                    $player->sendMessage(TextFormat::colorize("&aFile deleted correctly"));
                } else {
                    $player->sendMessage(TextFormat::colorize("&cThis file is already deleted"));
                }
            }
        });
        $form->setTitle("§l§3{$file}");
        $form->setContent(TextFormat::LIGHT_PURPLE . $content);
        $form->addButton("§l§cDelete");
        $form->addButton("§l§eClose");
        $player->sendForm($form);
    }
}
