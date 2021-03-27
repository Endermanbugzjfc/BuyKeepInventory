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

class BkiInfoCommand extends Command implements PluginIdentifiableCommand
{
    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct(strtolower($plugin->messages->getNested("commandinfo.name")), $plugin->messages->getNested("commandinfo.description"), null, $plugin->messages->getNested("commandinfo.aliases"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if ($this->plugin->messages->getNested("commandinfo.permission") && !$player->hasPermission("buykeepinventory." . $this->plugin->messages->getNested("commandinfo.permission"))) {
            $player->sendMessage($this->plugin->messages->getNested("commandinfo.error-permission"));
            return true;
        }
        $player->sendMessage(str_replace(["%0", "%1", "%2", "%3"], [($this->plugin->data->getNested($player->getName()) ?? 0), $this->plugin->config->getNested("limit"), $this->plugin->config->getNested("cost"), $player->getName()], $this->plugin->messages->getNested("commandinfo.success")));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
} 
