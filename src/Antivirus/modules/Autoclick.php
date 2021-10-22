<?php
namespace Antivirus\modules;

use Antivirus\Main;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\InventoryTransactionPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;

class Autoclick implements Listener{

    
/*
    public function onDataPacketReceive(DataPacketReceiveEvent $event){
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if ($packet instanceof InventoryTransactionPacket) {
            $transactionType = $packet->transactionType;
            if ($transactionType === InventoryTransactionPacket::TYPE_USE_ITEM || $transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY) {
                Main::getInstance()->getSession($player->getName())->addClick();
            }
        }
    }*/
    public function onDataPacketReceive(DataPacketReceiveEvent $event){
        $player = $event->getPlayer();
        $packet = $event->getPacket();
        if($packet instanceof LevelSoundEventPacket) {
            if($packet->sound == LevelSoundEventPacket::SOUND_ATTACK_NODAMAGE) {
                Main::getInstance()->getSession($player->getName())->addClick();
            }
        }
        if($packet instanceof InventoryTransactionPacket){
            $transactionType = $packet->trData;
            if($transactionType === InventoryTransactionPacket::TYPE_USE_ITEM_ON_ENTITY){
                Main::getInstance()->getSession($player->getName())->addClick();
            }
        }
    }

}