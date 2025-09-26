<?php

namespace DailyBonus;

use pocketmine\plugin\PluginBase;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat as TF;

class Main extends PluginBase {

    private $data;

    public function onEnable() {
        @mkdir($this->getDataFolder());
        $this->data = new Config($this->getDataFolder() . "players.yml", Config::YAML, []);
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
        $player->sendMessage(TF::AQUA . "¡Recibiste tu bonus diario!");
    }
}
