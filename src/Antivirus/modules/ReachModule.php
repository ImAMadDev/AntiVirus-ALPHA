<?php

namespace Antivirus\modules;

use Antivirus\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\item\ItemIds;
use pocketmine\Player;

class ReachModule implements Listener
{

    private array $bypass = [ItemIds::BOW,ItemIds::ENDER_PEARL, ItemIds::EGG, ItemIds::SNOWBALL];

    public function checkPossibleErrors(Player $attacker, Player $damaged) : bool{
        if($attacker->getLevel()->getFolderName() != $damaged->getLevel()->getFolderName()) return false;
        if(in_array($attacker->getInventory()->getItemInHand()->getId(), $this->bypass)) return false;
        return true;
    }

    public function onDamage(EntityDamageByEntityEvent $event) : void{
        $damaged = $event->getEntity();
        if($damaged instanceof Player){
            $attacker = $event->getDamager();
            if($attacker instanceof Player) {
                $reach = $attacker->distance($damaged->getPosition());
                if($reach > 7 and $this->checkPossibleErrors($attacker, $damaged)){
                    Main::getInstance()->getSession($attacker)->setReach(reach: $reach);
                }
            }
        }
    }
}