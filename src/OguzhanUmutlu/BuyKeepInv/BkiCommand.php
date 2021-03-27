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

class BkiCommand extends Command implements PluginIdentifiableCommand
{
    public $plugin;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        parent::__construct(strtolower($plugin->messages->getNested("command.name")), $plugin->messages->getNested("command.description"), null, $plugin->messages->getNested("command.aliases"));
    }

    public function execute(CommandSender $player, string $commandLabel, array $args)
    {
        if(!($player instanceof Player)) {
            $player->sendMessage("§c> Use this command in-game.");
            return true;
        }
        if ($this->plugin->messages->getNested("command.permission") && !$player->hasPermission("buykeepinventory." . $this->plugin->messages->getNested("command.permission"))) {
            $player->sendMessage($this->plugin->messages->getNested("command.error-permission"));
            return true;
        }
        $api = EconomyAPI::getInstance();
        $money = $api->myMoney($player);
        if ($money < $this->plugin->config->getNested("cost")) {
            $player->sendMessage($this->plugin->messages->getNested("command.error-money"));
            return true;
        }
        if ($this->plugin->data->getNested($player->getName()) && (($this->plugin->data->getNested($player->getName()) + 1) > ($this->plugin->config->getNested("limit")))) {
            $player->sendMessage($this->plugin->messages->getNested("command.error-max"));
            return true;
        }
        $api->reduceMoney($player, (int)$this->plugin->config->getNested("cost"));
        $this->plugin->data->setNested($player->getName(), ($this->plugin->data->getNested($player->getName()) ?? 0) + 1);
        $this->plugin->data->save();
        $this->plugin->data->reload();
        $player->sendMessage($this->plugin->messages->getNested("command.success"));
    }

    public function getPlugin(): Plugin
    {
        return $this->plugin;
    }
} 
