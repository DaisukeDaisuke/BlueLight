<?php

/*
 *
 *  _____   _____   __   _   _   _____  __    __  _____
 * /  ___| | ____| |  \ | | | | /  ___/ \ \  / / /  ___/
 * | |     | |__   |   \| | | | | |___   \ \/ /  | |___
 * | |  _  |  __|  | |\   | | | \___  \   \  /   \___  \
 * | |_| | | |___  | | \  | | |  ___| |   / /     ___| |
 * \_____/ |_____| |_|  \_| |_| /_____/  /_/     /_____/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author iTX Technologies
 * @link https://itxtech.org
 *
 */

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\Player;

class RedSandstoneSlab extends StoneSlab{
	const TYPE_RED_SANDSTONE = 0;
	const TYPE_PURPUR = 1;
	const TYPE_PRISMARINE = 2;
	const TYPE_DARK_PRISMARINE = 3;
	const TYPE_PRISMARINE_BRICKS = 4;
	const TYPE_MOSSY_COBBLESTONE = 5;
	const TYPE_SMOOTH_SANDSTONE = 6;
	const TYPE_RED_NETHER_BRICK = 7;

	protected $id = self::STONE_SLAB2;
	protected $doubleId = self::DOUBLE_STONE_SLAB2;
	/**
	 * @return string
	 */
	public function getName() : string{
		static $names = [
			self::TYPE_RED_SANDSTONE => "Red Sandstone",
			self::TYPE_PURPUR => "Purpur",
			self::TYPE_PRISMARINE => "Prismarine",
			self::TYPE_DARK_PRISMARINE => "Dark Prismarine",
			self::TYPE_PRISMARINE_BRICKS => "Prismarine Bricks",
			self::TYPE_MOSSY_COBBLESTONE => "Mossy Cobblestone",
			self::TYPE_SMOOTH_SANDSTONE => "Smooth Sandstone",
			self::TYPE_RED_NETHER_BRICK => "Red Nether Brick"
		];
		return (($this->meta & 0x08) > 0 ? "Upper " : "") . ($names[$this->meta & 0x07] ?? "") . " Slab";
	}
}