<?php

namespace DailyBonus;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;
use pocketmine\item\Item;

class Main extends PluginBase {

    private $data;
    private $starterItems;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->data = new Config($this->getDataFolder() . "players.yml", Config::YAML, []);
        $this->starterItems = [
            Item::get(Item::OAK_WOOD, 0, 16),
            Item::get(Item::SPRUCE_WOOD, 0, 16),
            Item::get(Item::WORKBENCH, 0, 1),
            Item::get(Item::WOODEN_PICKAXE, 0, 1),
            Item::get(Item::WOODEN_AXE, 0, 1),
            Item::get(Item::WOODEN_SWORD, 0, 1),
            Item::get(Item::WOODEN_SHOVEL, 0, 1),
            Item::get(Item::TORCH, 0, 16),
            Item::get(Item::BREAD, 0, 5),
            Item::get(Item::SEEDS, 0, 10),
            Item::get(Item::WHEAT, 0, 5),
            Item::get(Item::DIRT, 0, 16),
            Item::get(Item::STONE, 0, 16),
            Item::get(Item::SAND, 0, 16),
            Item::get(Item::COBBLESTONE, 0, 16),
            Item::get(Item::APPLE, 0, 3),
            Item::get(Item::WATER_BOTTLE, 0, 1),
            Item::get(Item::STRING, 0, 5),
            Item::get(Item::CLAY, 0, 4),
            Item::get(Item::EGG, 0, 1),
        ];
        $this->getLogger()->info(TF::GREEN . "DailyBonus habilitado.");
    }

    public function onDisable() {
        $this->data->save();
        $this->getLogger()->info(TF::RED . "DailyBonus deshabilitado.");
    }

    public function onCommand(CommandSender $sender, Command $command, $label, array $args) {
        if(strtolower($command->getName()) === "daily") {
            if(!$sender instanceof Player) {
                $sender->sendMessage(TF::RED . "Este comando solo puede usarse en el juego.");
                return true;
            }

            $name = $sender->getName();
            $lastClaim = $this->data->get($name, 0);
            $now = time();
            $cooldown = 86400;

            if(($now - $lastClaim) >= $cooldown) {
                $this->darBonusDiario($sender);
                $this->data->set($name, $now);
                $this->data->save();
            } else {
                $restante = $cooldown - ($now - $lastClaim);
                $horas = floor($restante / 3600);
                $minutos = floor(($restante % 3600) / 60);
                $segundos = $restante % 60;
                $sender->sendMessage(TF::YELLOW . "Ya reclamaste tu bonus diario! Próximo reclamo en: {$horas}h {$minutos}m {$segundos}s");
            }

            return true;
        }
        return false;
    }

    private function darBonusDiario(Player $player) {
        $item = $this->starterItems[array_rand($this->starterItems)];
        $player->getInventory()->addItem($item);
        $player->sendMessage(TF::GOLD . $player->getName() . TF::RED . " ¡recibiste tu bonus diario: " . TF::GOLD . $item->getName() . TF::RED . "!");
    }
}
