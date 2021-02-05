<?php

namespace OguzhanUmutlu\BuyKeepInv;

use pocketmine\{Player, Server};
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerDeathEvent;
use pocketmine\utils\Config;
use onebone\economyapi\EconomyAPI;
class Main extends PluginBase implements Listener {
  public function onEnable() {
    $this->ecoapi = class_exists(EconomyAPI::class);
    if(!$this->ecoapi) {
      $this->getLogger()->warning("EconomyAPI is not installed so plugin cannot start.");
      $this->getServer()->getPluginManager()->disablePlugin($this);
    }
    $this->saveResource("messages.yml");
    $this->saveResource("config.yml");
    $this->config = new Config($this->getDataFolder()."config.yml");
    $this->messages = new Config($this->getDataFolder()."messages.yml");
    $this->data = new Config($this->getDataFolder()."data.yml");
    $this->getServer()->getPluginManager()->registerEvents($this, $this);
    @mkdir($this->getDataFolder());
    $this->getServer()->getCommandMap()->register($this->getName(), new BkiCommand($this));
    $this->getServer()->getCommandMap()->register($this->getName()."1", new BkiInfoCommand($this));
  }
  public function onDeath(PlayerDeathEvent $e) {
    $player = $e->getPlayer();
    if($this->data->getNested($player->getName()) && $this->data->getNested($player->getName()) > 0 && !in_array($player->getPosition()->getLevel()->getName(),$this->messages->getNested("event.disabled-worlds")) && !($this->messages->getNested("event.allowed-worlds-enabled") && in_array($player->getPosition()->getLevel()->getName(),$this->messages->getNested("event.allowed-worlds")))) {
      $e->setKeepInventory(true);
      $player->sendMessage($this->messages->getNested("event.success"));
      $this->data->setNested($player->getName(), ((int)$this->data->getNested($player->getName()))-1);
      $this->data->save();
      $this->data->reload();
    }
  }
}
