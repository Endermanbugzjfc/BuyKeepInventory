<?php

namespace OguzhanUmutlu\BuyKeepInv;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\math\Vector3;
use onebone\economyapi\EconomyAPI;

class GiveBuyKi extends Command implements PluginIdentifiableCommand
{
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct(strtolower($plugin->messages->getNested("commandgive.name")), $plugin->messages->getNested("commandgive.description"), null, $plugin->messages->getNested("commandgive.aliases"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if ($this->plugin->messages->getNested("commandgive.permission") && !$player->hasPermission("buykeepinventory." . $this->plugin->messages->getNested("commandgive.permission"))) {
            $player->sendMessage($this->plugin->messages->getNested("commandgive.error-permission"));
            return true;
        }
        $plugin = $this->plugin;
        if(!isset($args[0])) {
            $player->sendMessage($this->plugin->messages->getNested("commandgive.error-usage"));
            return true;
        }
        if(!$plugin->getServer()->getPlayer($args[0])) {
            $player->sendMessage(str_replace("%0", $args[0], $this->plugin->messages->getNested("commandgive.error-not-online")));
            return true;
        }
        if(($this->plugin->data->getNested($player->getName()) ?? 0) <= 0) {
            $player->sendMessage($this->plugin->messages->getNested("commandgive.error-enough-buyki"));
            return true;
        }
        $target = $plugin->getServer()->getPlayer($args[0]);
        if($target->getName() == $player->getName()) {
            $player->sendMessage($this->plugin->messages->getNested("commandgive.error-you"));
            return true;
        }
        if(($this->plugin->data->getNested($target->getName()) ?? 0) >= $plugin->config->getNested("limit")) {
            $player->sendMessage($this->plugin->messages->getNested("commandgive.error-limit"));
            return true;
        }
        $this->plugin->data->setNested($player->getName(), ($this->plugin->data->getNested($player->getName()) ?? 0) - 1);
        $this->plugin->data->setNested($target->getName(), ($this->plugin->data->getNested($target->getName()) ?? 0) + 1);
        $this->plugin->data->save();
        $this->plugin->data->reload();
        $player->sendMessage(str_replace("%0", $target->getName(), $this->plugin->messages->getNested("commandgive.success")));
        $target->sendMessage(str_replace("%0", $player->getName(), $this->plugin->messages->getNested("commandgive.success1")));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}