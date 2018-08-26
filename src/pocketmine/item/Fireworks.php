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

namespace pocketmine\item;

use pocketmine\entity\Entity;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\nbt\tag\ByteArrayTag;
use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\DoubleTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\math\Vector3;
use pocketmine\entity\FireworksRocket;
use pocketmine\Player;
use pocketmine\level\sound\GenericSound;
use pocketmine\network\mcpe\protocol\LevelEventPacket;

class Fireworks extends Item{
	const BOOST_POWER = 1.25;
	const TYPE_SMALL_SPHERE = 0;
	const TYPE_HUGE_SPHERE = 1;
	const TYPE_STAR = 2;
	const TYPE_CREEPER_HEAD = 3;
	const TYPE_BURST = 4;
	const COLOR_BLACK = "\x00";
	const COLOR_RED = "\x01";
	const COLOR_DARK_GREEN = "\x02";
	const COLOR_BROWN = "\x03";
	const COLOR_BLUE = "\x04";
	const COLOR_DARK_PURPLE = "\x05";
	const COLOR_DARK_AQUA = "\x06";
	const COLOR_GRAY = "\x07";
	const COLOR_DARK_GRAY = "\x08";
	const COLOR_PINK = "\x09";
	const COLOR_GREEN = "\x0a";
	const COLOR_YELLOW = "\x0b";
	const COLOR_LIGHT_AQUA = "\x0c";
	const COLOR_DARK_PINK = "\x0d";
	const COLOR_GOLD = "\x0e";
	const COLOR_WHITE = "\x0f";

	public function __construct(int $meta = 0){
		parent::__construct(self::FIREWORKS, $meta, "Fireworks");
	}

	public function onActivate(Level $level, Player $player, Block $block, Block $target, int $face, Vector3 $facePos) : bool{
		$nbt = $this->createBaseNBT($block->add(0.5, 0, 0.5), new Vector3(0.001, 0.05, 0.001), lcg_value() * 360, 90);
		$entity = Entity::createEntity("FireworksRocket", $player->getLevel(), $nbt, $this);
		if($entity instanceof Entity){
			--$this->count;
			$entity->spawnToAll();
			return true;
		}
		return false;
	}

	public function onClickAir(Player $player, Vector3 $directionVector) : bool{
		if($player->isGliding()){
			$motion = new Vector3(
				(-sin($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI) * self::BOOST_POWER),
				(-sin($player->pitch / 180 * M_PI) * self::BOOST_POWER),
				(cos($player->yaw / 180 * M_PI) * cos($player->pitch / 180 * M_PI) * self::BOOST_POWER)
			);
			$nbt = $this->createBaseNBT($player, $motion->subtract(0, 0.1, 0), lcg_value() * 360, 90);
			$entity = Entity::createEntity("FireworksRocket", $player->getLevel(), $nbt, $this);
			if($entity instanceof Entity){
				--$this->count;
				$entity->spawnToAll();
				$player->setMotion($motion);
				//$player->getLevel()->addSound(new GenericSound($player, LevelEventPacket::EVENT_SOUND_BLAZE_SHOOT));
				return true;
			}
			return true;
		}
		return false;
	}
	
	public function getFlightDuration(){
		return $this->getNamedTag()["Fireworks"]["Flight"] ?? 1;
	}
	
	public function createBaseNBT(Vector3 $pos, Vector3 $motion = null, float $yaw = 0.0, float $pitch = 0.0){
		return new CompoundTag("", [
			new ListTag("Pos", [
				new DoubleTag("", $pos->x),
				new DoubleTag("", $pos->y),
				new DoubleTag("", $pos->z)
			]),
			new ListTag("Motion", [
				new DoubleTag("", $motion ? $motion->x : 0.0),
				new DoubleTag("", $motion ? $motion->y : 0.0),
				new DoubleTag("", $motion ? $motion->z : 0.0)
			]),
			new ListTag("Rotation", [
				new FloatTag("", $yaw),
				new FloatTag("", $pitch)
			])
		]);
	}
}

