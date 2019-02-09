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

namespace pocketmine\network\mcpe\protocol;

#include <rules/DataPacket.h>

use pocketmine\network\mcpe\NetworkSession;

class AvailableEntityIdentifiersPacket extends DataPacket{
	 const NETWORK_ID = ProtocolInfo::AVAILABLE_ENTITY_IDENTIFIERS_PACKET;

	/**
	 * Hardcoded NBT blob extracted from MCPE vanilla server.
	 * TODO: this needs to be generated dynamically, but this is here for stable backwards compatibility, so we don't care for now.
	 */
	const HARDCODED_NBT_BLOB = "CgAJBmlkbGlzdArGAQgDYmlkCm1pbmVjcmFmdDoBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQNbWluZWNyYWZ0Om5wYwMDcmlkhgQBCnN1bW1vbmFibGUAAAgDYmlkCm1pbmVjcmFmdDoBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQQbWluZWNyYWZ0OnBsYXllcgMDcmlkhAQBCnN1bW1vbmFibGUAAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQZbWluZWNyYWZ0OndpdGhlcl9za2VsZXRvbgMDcmlkYAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA5taW5lY3JhZnQ6aHVzawMDcmlkXgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA9taW5lY3JhZnQ6c3RyYXkDA3JpZFwBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQPbWluZWNyYWZ0OndpdGNoAwNyaWRaAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkGW1pbmVjcmFmdDp6b21iaWVfdmlsbGFnZXIDA3JpZFgBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQPbWluZWNyYWZ0OmJsYXplAwNyaWRWAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFG1pbmVjcmFmdDptYWdtYV9jdWJlAwNyaWRUAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkD21pbmVjcmFmdDpnaGFzdAMDcmlkUgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBVtaW5lY3JhZnQ6Y2F2ZV9zcGlkZXIDA3JpZFABCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQUbWluZWNyYWZ0OnNpbHZlcmZpc2gDA3JpZE4BCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQSbWluZWNyYWZ0OmVuZGVybWFuAwNyaWRMAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkD21pbmVjcmFmdDpzbGltZQMDcmlkSgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBdtaW5lY3JhZnQ6em9tYmllX3BpZ21hbgMDcmlkSAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBBtaW5lY3JhZnQ6c3BpZGVyAwNyaWRGAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEm1pbmVjcmFmdDpza2VsZXRvbgMDcmlkRAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBFtaW5lY3JhZnQ6Y3JlZXBlcgMDcmlkQgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBBtaW5lY3JhZnQ6em9tYmllAwNyaWRAAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkGG1pbmVjcmFmdDpza2VsZXRvbl9ob3JzZQMDcmlkNAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA5taW5lY3JhZnQ6bXVsZQMDcmlkMgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBBtaW5lY3JhZnQ6ZG9ua2V5AwNyaWQwAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEW1pbmVjcmFmdDpkb2xwaGluAwNyaWQ+AQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFm1pbmVjcmFmdDp0cm9waWNhbGZpc2gDA3JpZN4BAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkDm1pbmVjcmFmdDp3b2xmAwNyaWQcAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkD21pbmVjcmFmdDpzcXVpZAMDcmlkIgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAQELaGFzc3Bhd25lZ2cACAJpZBJtaW5lY3JhZnQ6cGlsbGFnZXIDA3JpZOQBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEW1pbmVjcmFmdDpkcm93bmVkAwNyaWTcAQEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA9taW5lY3JhZnQ6c2hlZXADA3JpZBoBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQTbWluZWNyYWZ0Om1vb3Nocm9vbQMDcmlkIAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA9taW5lY3JhZnQ6cGFuZGEDA3JpZOIBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEG1pbmVjcmFmdDpzYWxtb24DA3JpZNoBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkDW1pbmVjcmFmdDpwaWcDA3JpZBgBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQSbWluZWNyYWZ0OnZpbGxhZ2VyAwNyaWQeAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkDW1pbmVjcmFmdDpjb2QDA3JpZOABAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFG1pbmVjcmFmdDpwdWZmZXJmaXNoAwNyaWTYAQEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA1taW5lY3JhZnQ6Y293AwNyaWQWAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEW1pbmVjcmFmdDpjaGlja2VuAwNyaWQUAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkEW1pbmVjcmFmdDpiYWxsb29uAwNyaWTWAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA9taW5lY3JhZnQ6bGxhbWEDA3JpZDoBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQUbWluZWNyYWZ0Omlyb25fZ29sZW0DA3JpZCgBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQQbWluZWNyYWZ0OnJhYmJpdAMDcmlkJAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBRtaW5lY3JhZnQ6c25vd19nb2xlbQMDcmlkKgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZA1taW5lY3JhZnQ6YmF0AwNyaWQmAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkEG1pbmVjcmFmdDpvY2Vsb3QDA3JpZCwBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQPbWluZWNyYWZ0OmhvcnNlAwNyaWQuAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkDW1pbmVjcmFmdDpjYXQDA3JpZJYBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFG1pbmVjcmFmdDpwb2xhcl9iZWFyAwNyaWQ4AQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFm1pbmVjcmFmdDp6b21iaWVfaG9yc2UDA3JpZDYBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQQbWluZWNyYWZ0OnR1cnRsZQMDcmlklAEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQQbWluZWNyYWZ0OnBhcnJvdAMDcmlkPAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBJtaW5lY3JhZnQ6Z3VhcmRpYW4DA3JpZGIBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQYbWluZWNyYWZ0OmVsZGVyX2d1YXJkaWFuAwNyaWRkAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkFG1pbmVjcmFmdDp2aW5kaWNhdG9yAwNyaWRyAQpzdW1tb25hYmxlAQAIA2JpZAptaW5lY3JhZnQ6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkF21pbmVjcmFmdDp0cmlwb2RfY2FtZXJhAwNyaWSCBAEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBFtaW5lY3JhZnQ6cGhhbnRvbQMDcmlkdAEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBBtaW5lY3JhZnQ6d2l0aGVyAwNyaWRoAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFm1pbmVjcmFmdDplbmRlcl9kcmFnb24DA3JpZGoBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQRbWluZWNyYWZ0OnNodWxrZXIDA3JpZGwBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAQgCaWQTbWluZWNyYWZ0OmVuZGVybWl0ZQMDcmlkbgEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBJtaW5lY3JhZnQ6bWluZWNhcnQDA3JpZKgBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkGW1pbmVjcmFmdDpob3BwZXJfbWluZWNhcnQDA3JpZMABAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFm1pbmVjcmFmdDp0bnRfbWluZWNhcnQDA3JpZMIBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkGG1pbmVjcmFmdDpjaGVzdF9taW5lY2FydAMDcmlkxAEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQgbWluZWNyYWZ0OmNvbW1hbmRfYmxvY2tfbWluZWNhcnQDA3JpZMgBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFW1pbmVjcmFmdDphcm1vcl9zdGFuZAMDcmlkegEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZA5taW5lY3JhZnQ6aXRlbQMDcmlkgAEBCnN1bW1vbmFibGUAAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQNbWluZWNyYWZ0OnRudAMDcmlkggEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQXbWluZWNyYWZ0OmZhbGxpbmdfYmxvY2sDA3JpZIQBAQpzdW1tb25hYmxlAAAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkE21pbmVjcmFmdDp4cF9ib3R0bGUDA3JpZIgBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkEG1pbmVjcmFmdDp4cF9vcmIDA3JpZIoBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkHW1pbmVjcmFmdDpleWVfb2ZfZW5kZXJfc2lnbmFsAwNyaWSMAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBdtaW5lY3JhZnQ6ZW5kZXJfY3J5c3RhbAMDcmlkjgEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQYbWluZWNyYWZ0OnNodWxrZXJfYnVsbGV0AwNyaWSYAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBZtaW5lY3JhZnQ6ZmlzaGluZ19ob29rAwNyaWSaAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBltaW5lY3JhZnQ6ZHJhZ29uX2ZpcmViYWxsAwNyaWSeAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZA9taW5lY3JhZnQ6YXJyb3cDA3JpZKABAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkEm1pbmVjcmFmdDpzbm93YmFsbAMDcmlkogEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQNbWluZWNyYWZ0OmVnZwMDcmlkpAEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQSbWluZWNyYWZ0OnBhaW50aW5nAwNyaWSmAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBhtaW5lY3JhZnQ6dGhyb3duX3RyaWRlbnQDA3JpZJIBAQpzdW1tb25hYmxlAAAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkEm1pbmVjcmFmdDpmaXJlYmFsbAMDcmlkqgEBCnN1bW1vbmFibGUAAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQXbWluZWNyYWZ0OnNwbGFzaF9wb3Rpb24DA3JpZKwBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFW1pbmVjcmFmdDplbmRlcl9wZWFybAMDcmlkrgEBCnN1bW1vbmFibGUAAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQUbWluZWNyYWZ0OmxlYXNoX2tub3QDA3JpZLABAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFm1pbmVjcmFmdDp3aXRoZXJfc2t1bGwDA3JpZLIBAQpzdW1tb25hYmxlAAAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkIG1pbmVjcmFmdDp3aXRoZXJfc2t1bGxfZGFuZ2Vyb3VzAwNyaWS2AQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZA5taW5lY3JhZnQ6Ym9hdAMDcmlktAEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQYbWluZWNyYWZ0OmxpZ2h0bmluZ19ib2x0AwNyaWS6AQEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBhtaW5lY3JhZnQ6c21hbGxfZmlyZWJhbGwDA3JpZLwBAQpzdW1tb25hYmxlAAAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkFG1pbmVjcmFmdDpsbGFtYV9zcGl0AwNyaWTMAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBttaW5lY3JhZnQ6YXJlYV9lZmZlY3RfY2xvdWQDA3JpZL4BAQpzdW1tb25hYmxlAAAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkGm1pbmVjcmFmdDpsaW5nZXJpbmdfcG90aW9uAwNyaWTKAQEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBptaW5lY3JhZnQ6ZmlyZXdvcmtzX3JvY2tldAMDcmlkkAEBCnN1bW1vbmFibGUBAAgDYmlkAToBDGV4cGVyaW1lbnRhbAABC2hhc3NwYXduZWdnAAgCaWQYbWluZWNyYWZ0OmV2b2NhdGlvbl9mYW5nAwNyaWTOAQEKc3VtbW9uYWJsZQEACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cBCAJpZBttaW5lY3JhZnQ6ZXZvY2F0aW9uX2lsbGFnZXIDA3JpZNABAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwEIAmlkDW1pbmVjcmFmdDp2ZXgDA3JpZNIBAQpzdW1tb25hYmxlAQAIA2JpZAE6AQxleHBlcmltZW50YWwAAQtoYXNzcGF3bmVnZwAIAmlkD21pbmVjcmFmdDphZ2VudAMDcmlkcAEKc3VtbW9uYWJsZQAACANiaWQBOgEMZXhwZXJpbWVudGFsAAELaGFzc3Bhd25lZ2cACAJpZBJtaW5lY3JhZnQ6aWNlX2JvbWIDA3JpZNQBAQpzdW1tb25hYmxlAAAA";

	/** @var string */
	public $namedtag;

	protected function decodePayload(){
		$this->namedtag = $this->getRemaining();
	}

	protected function encodePayload(){
		$this->put($this->namedtag ?? base64_decode(self::HARDCODED_NBT_BLOB));
	}

	public function handle(NetworkSession $session) : bool{
		return $session->handleAvailableEntityIdentifiers($this);
	}
}