<?php

declare(strict_types = 1);

namespace LDX\iProtector;

use pocketmine\world\World;
use pocketmine\math\Vector3;

class Area{

	/** @var bool[] */
	public $flags;
	/** @var string */
	private $name;
	/** @var Vector3 */
	private $pos1;
	/** @var Vector3 */
	private $pos2;
	/** @var string */
	private $worldname;
	/** @var string[] */
	private $whitelist;
	/** @var Main */
	private $plugin;

	public function __construct(string $name, array $flags, Vector3 $pos1, Vector3 $pos2, string $worldname, array $whitelist, Main $plugin){
		$this->name = strtolower($name);
		$this->flags = $flags;
		$this->pos1 = $pos1;
		$this->pos2 = $pos2;
		$this->worldname = $worldname;
		$this->whitelist = $whitelist;
		$this->plugin = $plugin;
		$this->save();
	}

	/**
	 * @return string
	 */
	public function getName() : string {
		return $this->name;
	}

	/**
	 * @return Vector3
	 */
	public function getFirstPosition() : Vector3{
		return $this->pos1;
	}

	/**
	 * @return Vector3
	 */
	public function getSecondPosition() : Vector3{
		return $this->pos2;
	}

	/**
	 * @return bool[]
	 */
	public function getFlags() : array{
		return $this->flags;
	}

	/**
	 * @param string $flag
	 *
	 * @return bool
	 */
	public function getFlag(string $flag) : bool{
		if(isset($this->flags[$flag])){
			return $this->flags[$flag];
		}

		return false;
	}

	/**
	 * @param string $flag
	 * @param bool   $value
	 *
	 * @return bool
	 */
	public function setFlag(string $flag, bool $value) : bool{
		if(isset($this->flags[$flag])){
			$this->flags[$flag] = $value;
			$this->plugin->saveAreas();

			return true;
		}

		return false;
	}

	/**
	 * @param Vector3 $pos
	 * @param string  $worldname
	 *
	 * @return bool
	 */
	public function contains(Vector3 $pos, string $worldname) : bool{
		return ((min($this->pos1->getX(), $this->pos2->getX()) <= $pos->getX()) && (max($this->pos1->getX(), $this->pos2->getX()) >= $pos->getX()) && (min($this->pos1->getY(), $this->pos2->getY()) <= $pos->getY()) && (max($this->pos1->getY(), $this->pos2->getY()) >= $pos->getY()) && (min($this->pos1->getZ(), $this->pos2->getZ()) <= $pos->getZ()) && (max($this->pos1->getZ(), $this->pos2->getZ()) >= $pos->getZ()) && ($this->worldname === $worldname));
	}

	/**
	 * @param string $flag
	 *
	 * @return bool
	 */
	public function toggleFlag(string $flag) : bool{
		if(isset($this->flags[$flag])){
			$this->flags[$flag] = !$this->flags[$flag];
			$this->plugin->saveAreas();

			return $this->flags[$flag];
		}

		return false;
	}

	/**
	 * @return string
	 */
	public function getWorldName() : string{
		return $this->worldname;
	}

	/**
	 * @return null|World
	 */
	public function getWorld() : ?World{
		return $this->plugin->getServer()->getWorldManager()->getWorldByName($this->worldname);
	}

	/**
	 * @param string $playerName
	 *
	 * @return bool
	 */
	public function isWhitelisted(string $playerName) : bool{
		if(in_array($playerName, $this->whitelist)){
			return true;
		}

		return false;
	}

	/**
	 * @param string $name
	 * @param bool   $value
	 *
	 * @return bool
	 */
	public function setWhitelisted(string $name, bool $value = true) : bool{
		if($value){
			if(!in_array($name, $this->whitelist)){
				$this->whitelist[] = $name;
				$this->plugin->saveAreas();

				return true;
			}
		}else{
			if(in_array($name, $this->whitelist)){
				$key = array_search($name, $this->whitelist);
				array_splice($this->whitelist, $key, 1);
				$this->plugin->saveAreas();

				return true;
			}
		}

		return false;
	}

	/**
	 * @return string[]
	 */
	public function getWhitelist() : array{
		return $this->whitelist;
	}

	public function delete() : void{
		unset($this->plugin->areas[$this->getName()]);
		$this->plugin->saveAreas();
	}

	public function save() : void{
		$this->plugin->areas[$this->name] = $this;
	}
}
