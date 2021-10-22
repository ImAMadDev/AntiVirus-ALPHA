<?php

namespace Antivirus\commands;

use Antivirus\Main;
use Antivirus\Session;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use pocketmine\utils\TextFormat;

class AntivirusCommand extends Command
{

    public function __construct()
    {
        parent::__construct("antivirus", "Antivirus command for staffs", "/av help", ["av"]);
    }

    /**
     * @inheritDoc
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if(!$sender->hasPermission("antivirus.admin")){
            $sender->sendMessage(TextFormat::colorize("&cERROR > &7This command is only for staff members"));
            return;
        }
        switch ($args[0]){
            case "see":
                if(empty($args[1])){
                    $sender->sendMessage(TextFormat::colorize("&cMissing args, please type player name."));
                    return;
                }
                if(($session = Main::getInstance()->getSession($args[1])) instanceof Session){
                    $session->sendLogs($sender);
                    $sender->sendMessage(TextFormat::colorize("&aOpening player logs"));
                } else {
                    $sender->sendMessage(TextFormat::colorize("&cThat player dont have any session opened"));
                }
                break;
            case "alerts":
                if (empty($args[1])){
                    $sender->sendMessage(TextFormat::colorize("&cMissing args, please type on or off"));
                    return;
                }
                if ($args[1] == "on"){
                    $sender->sendMessage(TextFormat::colorize("&aYou have enabled antivirus alerts."));
                    if(!in_array($sender->getName(), Main::$alerts)) {
                        Main::$alerts[$sender->getName()] = true;
                    }
                } elseif($args[1] == "off"){
                    $sender->sendMessage(TextFormat::colorize("&cYou have disabled antivirus alerts."));
                    if(in_array($sender->getName(), Main::$alerts)) {
                        unset(Main::$alerts[$sender->getName()]);
                    } else {
                        $sender->sendMessage(TextFormat::colorize("&cMissing args, please type on or off"));
                        return;
                    }
                }
                break;
            case "info":
                $sender->sendMessage(TextFormat::colorize("&Information > &b") . PHP_EOL .
                    "Plugin version: " . Server::getInstance()->getPluginManager()->getPlugin("AntiVirusMad")->getDescription()->getVersion() . PHP_EOL .
                    "Author: ImAMadDev" . PHP_EOL .
                    "Features: AutoClick, Reach"
                );
                break;
            case "help":
                $sender->sendMessage(TextFormat::colorize("&6COMMANDS > &7") . PHP_EOL .
                    "- see (player name) [Used to see the player logs]" . PHP_EOL .
                    "- info [see the plugin information]" . PHP_EOL .
                    "- alerts (on|off) [Enable or disable antivirus alerts]"
                );
                break;
        }
    }
}