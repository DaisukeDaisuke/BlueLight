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

namespace pocketmine\nbt;

use pocketmine\utils\Binary;

class NetworkLittleEndianNBTStream extends LittleEndianNBTStream{

	public function getInt(bool $network = false) : int{
		return Binary::readVarInt($this->buffer, $this->offset);
	}

	public function putInt($v, bool $network = false){
		($this->buffer .= Binary::writeVarInt($v));
	}

	public function getLong(bool $network = false) : int{
		return Binary::readVarLong($this->buffer, $this->offset);
	}

	public function putLong($v, bool $network = false){
		($this->buffer .= Binary::writeVarLong($v));
	}

	public function getString(bool $network = false) : string{
		return $this->get(Binary::readUnsignedVarInt($this->buffer, $this->offset));
	}

	public function putString($v, bool $network = false){
		$len = strlen($v);
		if($len > 32767){
			throw new \InvalidArgumentException("NBT strings cannot be longer than 32767 bytes, got $len bytes");
		}
		($this->buffer .= Binary::writeUnsignedVarInt($len) . $v);
	}

	public function getIntArray() : array{
		$len = $this->getInt(); //varint
		$ret = [];
		for($i = 0; $i < $len; ++$i){
			$ret[] = $this->getInt(); //varint
		}

		return $ret;
	}

	public function putIntArray(array $array){
		$this->putInt(count($array)); //varint
		foreach($array as $v){
			$this->putInt($v); //varint
		}
	}
}
