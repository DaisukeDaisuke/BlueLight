<?php

/*
 *   ____  _            _      _       _     _
 *  |  _ \| |          | |    (_)     | |   | |
 *  | |_) | |_   _  ___| |     _  __ _| |__ | |_
 *  |  _ <| | | | |/ _ \ |    | |/ _` | '_ \| __|
 *  | |_) | | |_| |  __/ |____| | (_| | | | | |_
 *  |____/|_|\__,_|\___|______|_|\__, |_| |_|\__|
 *                                __/ |
 *                               |___/
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author BlueLightJapan Team
 * 
*/

namespace pocketmine\entity;

use pocketmine\entity\Entity;
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\network\mcpe\protocol\EntityEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\level\particle\FlameParticle;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\item\Fireworks;

class FireworksRocket extends Entity{
	const NETWORK_ID = 72;

	public $width = 0.25;
	public $length = 0.25;
	public $height = 0.25;

	protected $gravity = 0;
	protected $drag = 0.01;

	protected $lifeTime = 0;

	public function __construct(Level $level, CompoundTag $nbt, $fireworks = null){
		parent::__construct($level, $nbt);
		if($fireworks instanceof Fireworks){
			$this->setDataProperty(Entity::DATA_DISPLAY_ITEM, Entity::DATA_TYPE_SLOT, $fireworks);
			$this->setLifeTime($this->getRandomizedFlightDuration());
			$level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LAUNCH);
		}
	}

	public function setLifeTime(int $life){
		$this->lifeTime = $life;
	}

	public function getRandomizedFlightDuration(){
		return ($this->getDataProperty(self::DATA_DISPLAY_ITEM)->getFlightDuration() + 1) * 10 + mt_rand(0, 5) + mt_rand(0, 6);
	}

	public function spawnTo(Player $player){
		$pk = new AddEntityPacket;
		$pk->entityRuntimeId = $this->getId();
		$pk->type = self::NETWORK_ID;
		$pk->position = $this->asVector3();
		$pk->motion = $this->getMotion();
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$player->dataPacket($pk);
		parent::spawnTo($player);
		
		$this->setMotion(new Vector3(0, 2.5, 0));
	}

	public function entityBaseTick(int $tickDiff = 1) : bool{
		if($this->closed) {
			return false;
		}
		$hasUpdate = parent::entityBaseTick($tickDiff);
		if($this->doLifeTimeTick()) {
			$hasUpdate = true;
			$this->kill();
		}
		return $hasUpdate;
	}

	protected function doLifeTimeTick() {
		if(--$this->lifeTime < 0) {
			
			$fireworks = $this->getDataProperty(self::DATA_DISPLAY_ITEM);
			if($fireworks === null){
				return;
			}
			$fireworks_nbt = $fireworks->getNamedTag()["Fireworks"];
			if($fireworks_nbt === null){
				return;
			}
			$explosions = $fireworks_nbt["Explosions"];
			if($explosions === null){
				return;
			}
			/** @var CompoundTag $explosion */
			foreach($explosions as $explosion){
				switch($explosion["FireworkType"]){
					case Fireworks::TYPE_SMALL_SPHERE:
						$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_BLAST);
						break;
					case Fireworks::TYPE_HUGE_SPHERE:
						$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_LARGE_BLAST);
						break;
					case Fireworks::TYPE_STAR:
					case Fireworks::TYPE_BURST:
					case Fireworks::TYPE_CREEPER_HEAD:
						$this->level->broadcastLevelSoundEvent($this, LevelSoundEventPacket::SOUND_TWINKLE);
						break;
				}
			}
			
			
			
			$pk = new EntityEventPacket();
			$pk->entityRuntimeId = $this->id;
			$pk->event = 25;
			$pk->data = 0;
			$this->server->broadcastPacket($this->getViewers(), $pk);
			return true;
		}
		return false;
	}

	public function getName() : string{
		return "Firework";
	}
}
