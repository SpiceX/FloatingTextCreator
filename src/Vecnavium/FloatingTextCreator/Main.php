<?php


namespace Vecnavium\FloatingTextCreator;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\utils\TextFormat as TF;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\level\particle\FloatingTextParticle;
use pocketmine\plugin\PluginBase;
use Vecnavium\FloatingTextCreator\Tasks\ConfigVersionTask;
use Vecnavium\FloatingTextCreator\Tasks\FTCUpdateTask;
use Vecnavium\FloatingTextCreator\Commands\FTCCommand;

class Main extends PluginBase {

    public $floatingTexts = [];

    public function onLoad()
    {
        $this->configVersion = new ConfigVersionTask($this);
    }

    public function onEnable()
    {
        $this->floatingText = new Config($this->getDataFolder() . "ftc.yml", Config::YAML);
        $this->getServer()->getCommandMap()->register("FloatingTextCreator", new FTCCommand($this));
        $this->getScheduler()->scheduleRepeatingTask(new FTCUpdateTask($this), 20 * $this->getUpdateTimer());
        $this->restartFTC();
    }
    
    public function restartFTC() {
        foreach($this->getFloatingTexts()->getAll() as $id => $array) {
            $this->floatingTexts[$id] = new FloatingTextParticle(new Vector3($array["x"], $array["y"], $array["z"]), "");
        }
    }

    public function getFloatingTexts(): Config {
        return $this->floatingText;
    }

    public function getUpdateTimer(): int {
        return $this->getConfig()->get("ft-updatetime");
    }


    public function replaceProcess(Player $player, string $string): string {
        $string = str_replace("{player}", $player->getName(), $string);
        $string = str_replace("{ip}", Server::getInstance()->getIp(), $string);
        $string = str_replace("{port}", Server::getInstance()->getPort(), $string);
        $string = str_replace("{line}", TF::EOL, $string);
        $string = str_replace("\n", TF::EOL, $string);
        $string = str_replace("{world}", $player->getLevel()->getName(), $string);
        $string = str_replace("{x}", $player->getX(), $string);
        $string = str_replace("{y}", $player->getY(), $string);
        $string = str_replace("{z}", $player->getZ(), $string);
        $string = str_replace("{online}", Server::getInstance()->getQueryInformation()->getPlayerCount(), $string);
        $string = str_replace("{max_online}", Server::getInstance()->getQueryInformation()->getMaxPlayerCount(), $string);
        $string = str_replace("{ping}", $player->getPing(), $string);
        return $string;
    }

}
