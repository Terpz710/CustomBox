<?php

declare(strict_types=1);

namespace Terpz710\CustomBox\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;

use Terpz710\CustomBox\Utils\Utils;

class KeyCommand extends Command
{
    public function __construct(string $name, string $description, array $aliases)
    {
        parent::__construct($name, $description, null, $aliases);
        $this->setPermission("box.use");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if ($sender->hasPermission("box.use")) {
            if (isset($args[0])) {
                $player = $sender->getServer()->getPlayerByPrefix($args[0]);
                if ($player instanceof Player) {
                    if (isset($args[1])) {
                        $count = $args[2] ?? 1;
                        $count = is_numeric($count) ? $count : 1;
                        $item = StringToItemParser::getInstance()->parse(Utils::getConfigValue("key"))->setCount($count)->setLore([$args[1]]);
                        if ($player->getInventory()->canAddItem($item)) {
                            $player->getInventory()->addItem($item);
                        } else $player->getWorld()->dropItem($player->getPosition(), $item);
                        $sender->sendMessage(Utils::getConfigReplace("give_key", ["{player}", "{count}"], [$player->getName(), $count]));
                    } else $sender->sendMessage(Utils::getConfigReplace("no_args_box"));
                } else $sender->sendMessage(Utils::getConfigReplace("no_args_player"));
            } else $sender->sendMessage(Utils::getConfigReplace("no_args_player"));
        } else $sender->sendMessage(Utils::getConfigReplace("no_permission"));
    }
}
