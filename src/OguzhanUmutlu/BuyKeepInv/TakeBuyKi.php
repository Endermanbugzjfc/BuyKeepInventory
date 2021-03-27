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

class TakeBuyKi extends Command implements PluginIdentifiableCommand
{
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct(strtolower($plugin->messages->getNested("commandtake.name")), $plugin->messages->getNested("commandtake.description"), null, $plugin->messages->getNested("commandtake.aliases"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if ($this->plugin->messages->getNested("commandtake.permission") && !$player->hasPermission("buykeepinventory." . $this->plugin->messages->getNested("commandtake.permission"))) {
            $player->sendMessage($this->plugin->messages->getNested("commandadd.error-permission"));
            return true;
        }
        $plugin = $this->plugin;
        if(!isset($args[0])) {
            $player->sendMessage($this->plugin->messages->getNested("commandadd.error-usage"));
            return true;
        }
        if(!$plugin->getServer()->getPlayer($args[0])) {
            $player->sendMessage(str_replace("%0", $args[0], $this->plugin->messages->getNested("commandadd.error-not-online")));
            return true;
        }
        $target = $plugin->getServer()->getPlayer($args[0]);
        if(($this->plugin->data->getNested($target->getName()) ?? 0) <= 0) {
            $player->sendMessage($this->plugin->messages->getNested("commandtake.error-limit"));
            return true;
        }
        $this->plugin->data->setNested($target->getName(), ($this->plugin->data->getNested($target->getName()) ?? 0) - 1);
        $this->plugin->data->save();
        $this->plugin->data->reload();
        $player->sendMessage(str_replace("%0", $target->getName(), $this->plugin->messages->getNested("commandtake.success")));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
}