<?php

/*
 *
 *  ____            _        _   __  __ _                  __  __ ____
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) |
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine Team
 * @link http://www.pocketmine.net/
 *
 *
*/

declare(strict_types=1);

namespace pocketmine\block;

use pocketmine\item\Tool;
use pocketmine\math\AxisAlignedBB;
use pocketmine\math\Vector3;

class CobblestoneWall extends Transparent{
	const NONE_MOSSY_WALL = 0;
	const MOSSY_WALL = 1;
	const GRANITE_WALL = 2;
	const DIORITE_WALL = 3;
	const ANDESITE_WALL = 4;
	const SANDSTONE_WALL = 5;
	const BRICK_WALL = 6;
	const STONE_BRICK_WALL = 7;
	const MOSSY_STONE_BRICK_WALL = 8;
	const NETHER_BRICK_WALL = 9;
	const END_STONE_BRICK_WALL = 10;
	const PRISMARINE_WALL = 11;
	const RED_SANDSTONE_WALL = 12;
	const RED_NETHER_BRICK_WALL = 13;
	
	protected $id = self::COBBLESTONE_WALL;

	public function __construct(int $meta = 0){
		$this->meta = $meta;
	}

	public function isSolid() : bool{
		return false;
	}

	public function getToolType() : int{
		return Tool::TYPE_PICKAXE;
	}

	public function getHardness() : float{
		return 2;
	}

	public function getName() : string{
		static $names = [
			self::NONE_MOSSY_WALL => "Cobblestone",
			self::MOSSY_WALL => "Mossy Cobblestone",
			self::GRANITE_WALL => "Granite",
			self::DIORITE_WALL => "Diorite",
			self::ANDESITE_WALL => "Andesite",
			self::SANDSTONE_WALL => "Sandstone",
			self::BRICK_WALL => "Brick",
			self::STONE_BRICK_WALL => "Stone Brick",
			self::MOSSY_STONE_BRICK_WALL => "Mossy Stone Brick",
			self::NETHER_BRICK_WALL => "Nether Brick",
			self::END_STONE_BRICK_WALL => "End Stone Brick",
			self::PRISMARINE_WALL => "Prismarine",
			self::RED_SANDSTONE_WALL => "Red Sandstone",
			self::RED_NETHER_BRICK_WALL => "Red Nether Brick"
		];
		return ($names[$this->meta & 0x07] ?? "Unknown") . " Wall";
	}

	protected function recalculateBoundingBox(){

		$north = $this->canConnect($this->getSide(Vector3::SIDE_NORTH));
		$south = $this->canConnect($this->getSide(Vector3::SIDE_SOUTH));
		$west = $this->canConnect($this->getSide(Vector3::SIDE_WEST));
		$east = $this->canConnect($this->getSide(Vector3::SIDE_EAST));

		$n = $north ? 0 : 0.25;
		$s = $south ? 1 : 0.75;
		$w = $west ? 0 : 0.25;
		$e = $east ? 1 : 0.75;

		if($north and $south and !$west and !$east){
			$w = 0.3125;
			$e = 0.6875;
		}elseif(!$north and !$south and $west and $east){
			$n = 0.3125;
			$s = 0.6875;
		}

		return new AxisAlignedBB(
			$this->x + $w,
			$this->y,
			$this->z + $n,
			$this->x + $e,
			$this->y + 1.5,
			$this->z + $s
		);
	}

	public function canConnect(Block $block){
		return ($block->getId() !== self::COBBLESTONE_WALL and $block->getId() !== self::FENCE_GATE) ? $block->isSolid() and !$block->isTransparent() : true;
	}

}
