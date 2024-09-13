<?php

declare(strict_types=1);

namespace Terpz710\CustomBox\Forms;

use pocketmine\console\ConsoleCommandSender;
use pocketmine\form\Form;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;

use Terpz710\CustomBox\Utils\Utils;

class BoxForms implements Form
{
    private string $name;
    private array $labelMap = [];
    protected array $data = [];
    private ?\Closure $callable;
    private string $content;

    public function __construct(string $boxName)
    {
        $this->name = $boxName;
        $this->content = Utils::getConfigValue("boxs")[$boxName]["content"] ?? "error";
        $this->initText();
        $this->data["type"] = "form";
        $this->callable = function (Player $player, ?string $data = null) use ($boxName) {
            if (is_null($data)) return;

            $id = Utils::getConfigValue("key")[0];
            $meta = Utils::getConfigValue("key")[1] ?? 0;
            if ($player->getInventory()->contains(StringToItemParser::getInstance()->parse($id)->setLore([$boxName]))) {
                $player->getInventory()->removeItem(StringToItemParser::getInstance()->parse($id)->setLore([$boxName]));
                $chance = 100;
                $loot = [];

                for($i = 0; $i < Utils::getConfigValue("boxs")[$boxName]["air_loot"]; $i++){
                    $loot[] = "";
                }

                foreach (Utils::getConfigValue("boxs")[$boxName]["loots"] as $item) {
                    $chance += $item[1];
                    for ($i = 0; $i < $item[1]; $i++) {
                        $loot[] = $item[0];
                    }
                }

                $count = count($loot) - 1;
                $loot = $loot[mt_rand(0, $count)];
                $player->getServer()->getCommandMap()->dispatch(new ConsoleCommandSender($player->getServer(), $player->getLanguage()), str_replace("{player}", $player->getName(), $loot));
            } else $player->sendMessage(Utils::getConfigReplace("no_key"));
        };
    }

    public function processData(&$data): void
    {
        $data = $this->labelMap[$data] ?? null;
    }

    public function handleResponse(Player $player, $data): void
    {
        $this->processData($data);
        $callable = $this->callable;
        $callable($player, $data);
    }

    public function initText(): void
    {
        $this->data["title"] = Utils::getConfigReplace("title", "{name}", $this->name);
        $this->data["content"] = $this->content;
        $this->data["buttons"][] = ["text" => Utils::getConfigReplace("button")];
        $this->labelMap[] = "open";
    }

    public function jsonSerialize(): array
    {
        return $this->data;
    }
}
