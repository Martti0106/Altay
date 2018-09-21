<?php

/*
 *               _ _
 *         /\   | | |
 *        /  \  | | |_ __ _ _   _
 *       / /\ \ | | __/ _` | | | |
 *      / ____ \| | || (_| | |_| |
 *     /_/    \_|_|\__\__,_|\__, |
 *                           __/ |
 *                          |___/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author TuranicTeam
 * @link https://github.com/TuranicTeam/Altay
 *
 */

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\event\block\BlockGrowEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\math\Facing;
use pocketmine\Server;

class PumpkinStem extends Crops{

	protected $id = self::PUMPKIN_STEM;

	public function getName() : string{
		return "Pumpkin Stem";
	}

	public function onRandomTick() : void{
		if(mt_rand(0, 2) === 1){
			if($this->age < 7){
				$block = clone $this;
				++$block->age;
				Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($this, $block));
				if(!$ev->isCancelled()){
					$this->getLevel()->setBlock($this, $ev->getNewState(), true);
				}
			}else{
				foreach(Facing::HORIZONTAL as $side){
					$b = $this->getSide($side);
					if($b->getId() === self::PUMPKIN){
						return;
					}
				}
				$side = $this->getSide(Facing::HORIZONTAL[array_rand(Facing::HORIZONTAL)]);
				$d = $side->getSide(Facing::DOWN);
				if($side->getId() === self::AIR and ($d->getId() === self::FARMLAND or $d->getId() === self::GRASS or $d->getId() === self::DIRT)){
					Server::getInstance()->getPluginManager()->callEvent($ev = new BlockGrowEvent($side, BlockFactory::get(Block::PUMPKIN)));
					if(!$ev->isCancelled()){
						$this->getLevel()->setBlock($side, $ev->getNewState(), true);
					}
				}
			}
		}
	}

	public function getDropsForCompatibleTool(Item $item) : array{
		return [
			ItemFactory::get(Item::PUMPKIN_SEEDS, 0, mt_rand(0, 2))
		];
	}

	public function getPickedItem() : Item{
		return ItemFactory::get(Item::PUMPKIN_SEEDS);
	}
}
