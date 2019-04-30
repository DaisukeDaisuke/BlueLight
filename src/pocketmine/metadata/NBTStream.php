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

use pocketmine\nbt\tag\ByteTag;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntArrayTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;

use pocketmine\utils\Binary;

/**
 * Base Named Binary Tag encoder/decoder
 */
abstract class NBTStream extend NBT{

	public $buffer = "";
	public $offset = 0;

	public function get($len){
		if($len < 0){
			$this->offset = strlen($this->buffer) - 1;
			return "";
		}elseif($len === true){
			return substr($this->buffer, $this->offset);
		}

		return $len === 1 ? $this->buffer{$this->offset++} : substr($this->buffer, ($this->offset += $len) - $len, $len);
	}

	public function put($v){
		$this->buffer .= $v;
	}

	public function feof() : bool{
		return !isset($this->buffer{$this->offset});
	}

	/**
	 * Decodes NBT from the given binary string and returns it.
	 *
	 * @param string $buffer
	 * @param bool   $doMultiple Whether to keep reading after the first tag if there are more bytes in the buffer
	 *
	 * @return NamedTag|NamedTag[]
	 */
	public function read($buffer, $doMultiple = false, bool $network = false){
		$this->offset = 0;
		$this->buffer = $buffer;
		$data = $this->readTag();

		if($data === null){
			throw new \InvalidArgumentException("Found TAG_End at the start of buffer");
		}

		if($doMultiple and !$this->feof()){
			$data = [$data];
			do{
				$tag = $this->readTag();
				if($tag !== null){
					$data[] = $tag;
				}
			}while(!$this->feof());
		}
		$this->buffer = "";

		return $data;
	}

	/**
	 * Decodes NBT from the given compressed binary string and returns it. Anything decodable by zlib_decode() can be
	 * processed.
	 *
	 * @param string $buffer
	 *
	 * @return NamedTag|NamedTag[]
	 */
	public function readCompressed($buffer){
		return $this->read(zlib_decode($buffer));
	}

	/**
	 * @param NamedTag|NamedTag[] $data
	 *
	 * @return bool|string
	 */
	public function write($data){
		$this->offset = 0;
		$this->buffer = "";

		if($data instanceof CompoundTag){
			$this->writeTag($data);

			return $this->buffer;
		}elseif(is_array($data)){
			foreach($data as $tag){
				$this->writeTag($tag);
			}
			return $this->buffer;
		}

		return false;
	}

	/**
	 * @param NamedTag|NamedTag[] $data
	 * @param int                 $compression
	 * @param int                 $level
	 *
	 * @return bool|string
	 */
	public function writeCompressed($data, int $compression = ZLIB_ENCODING_GZIP, int $level = 7){
		if(($write = $this->write($data)) !== false){
			return zlib_encode($write, $compression, $level);
		}

		return false;
	}

	public function readTag(){
		$tagType = (ord($this->get(1)));
		if($tagType === NBT::TAG_End){
			return null;
		}

		$tag = NBT::createTag($tagType);
		$tag->setName($this->getString());
		$tag->read($this);

		return $tag;
	}

	public function writeTag(NamedTag $tag){
		($this->buffer .= chr($tag->getType()));
		$this->putString($tag->getName());
		$tag->write($this);
	}

	public function writeEnd(){
		($this->buffer .= chr(NBT::TAG_End));
	}

	public function getByte() : int{
		return (ord($this->get(1)));
	}

	public function getSignedByte() : int{
		return (ord($this->get(1)) << 56 >> 56);
	}

	public function putByte(int $v){
		$this->buffer .= (chr($v));
	}

	abstract public function getShort() : int;

	abstract public function getSignedShort() : int;

	abstract public function putShort(int $v);


	abstract public function getInt() : int;

	abstract public function putInt(int $v);

	abstract public function getLong() : int;

	abstract public function putLong(int $v);


	abstract public function getFloat() : float;

	abstract public function putFloat(float $v);


	abstract public function getDouble() : float;

	abstract public function putDouble(float $v);

	public function getString() : string{
		return $this->get($this->getShort());
	}

	/**
	 * @param string $v
	 * @throws \InvalidArgumentException if the string is too long
	 */
	public function putString(string $v){
		$len = strlen($v);
		if($len > 32767){
			throw new \InvalidArgumentException("NBT strings cannot be longer than 32767 bytes, got $len bytes");
		}
		$this->putShort($len);
		($this->buffer .= $v);
	}

	/**
	 * @return int[]
	 */
	abstract public function getIntArray() : array;

	/**
	 * @param int[] $array
	 */
	abstract public function putIntArray(array $array);


	/**
	 * @param CompoundTag $data
	 *
	 * @return array
	 */
	public static function toArray(CompoundTag $data) : array{
		$array = [];
		self::tagToArray($array, $data);
		return $array;
	}

	private static function tagToArray(array &$data, NamedTag $tag){
		/** @var CompoundTag[]|ListTag[]|IntArrayTag[] $tag */
		foreach($tag as $key => $value){
			if($value instanceof CompoundTag or $value instanceof ListTag or $value instanceof IntArrayTag){
				$data[$key] = [];
				self::tagToArray($data[$key], $value);
			}else{
				$data[$key] = $value->getValue();
			}
		}
	}

	public static function fromArrayGuesser(string $key, $value){
		if(is_int($value)){
			return new IntTag($key, $value);
		}elseif(is_float($value)){
			return new FloatTag($key, $value);
		}elseif(is_string($value)){
			return new StringTag($key, $value);
		}elseif(is_bool($value)){
			return new ByteTag($key, $value ? 1 : 0);
		}

		return null;
	}

	private static function tagFromArray(NamedTag $tag, array $data, callable $guesser){
		foreach($data as $key => $value){
			if(is_array($value)){
				$isNumeric = true;
				$isIntArray = true;
				foreach($value as $k => $v){
					if(!is_numeric($k)){
						$isNumeric = false;
						break;
					}elseif(!is_int($v)){
						$isIntArray = false;
					}
				}
				$tag{$key} = $isNumeric ? ($isIntArray ? new IntArrayTag($key, []) : new ListTag($key, [])) : new CompoundTag($key, []);
				self::tagFromArray($tag->{$key}, $value, $guesser);
			}else{
				$v = call_user_func($guesser, $key, $value);
				if($v instanceof NamedTag){
					$tag{$key} = $v;
				}
			}
		}
	}

	/**
	 * @param array         $data
	 * @param callable|null $guesser
	 *
	 * @return CompoundTag
	 */
	public static function fromArray(array $data, callable $guesser = null) : CompoundTag{
		$tag = new CompoundTag("", []);
		self::tagFromArray($tag, $data, $guesser ?? [self::class, "fromArrayGuesser"]);
		return $tag;
	}
}