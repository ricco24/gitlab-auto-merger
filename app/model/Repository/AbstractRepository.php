<?php

namespace App\Model\Repository;

use Nette\Database\Context;
use Nette\Database\Table\ActiveRow;
use Nette\Database\Table\Selection;
use Nette\Object;

class AbstractRepository extends Object
{
	/** @var Context		Database object */
	private $database;
	
	/** @var string			Define table name */
	protected $table;

	/**
	 * @param Context $database
	 */
	public function __construct(Context $database)
	{
		$this->database = $database;
	}
	
	/**
	 * Getter for database object.
	 * @return Context
	 */
	public function getDB()
	{
		return $this->database;
	}

	/**
	 * Wrapper function for table()
	 * @return Selection
	 */
	public function findAll()
	{
		return $this->database->table($this->table);
	}

	/**
	 * Fetch record by primary
	 * @param mixed $id
	 * @return bool|mixed|\Nette\Database\Table\IRow
	 */
	public function find($id)
	{
		return $this->findAll()->wherePrimary($id)->fetch();
	}

	/**
	 * Wrapper function for get().
	 * @param int $id
	 * @return ActiveRow
	 */
	public function get($id)
	{
		return $this->database->table($this->table)->get($id);
	}

	/**
	 * Wrapper insert() function.
	 * @param string|array $data
	 * @return ActiveRow or FALSE in case of an error or number of affected rows for INSERT ... SELECT
	 */
	public function insert($data)
	{
		return $this->database->table($this->table)->insert($data);
	}

	/**
	 * Wrapper update() function.
	 * @param int $id
	 * @param array|\Traversable ($column => $value) $data
	 * @return int number of affected rows or FALSE in case of an error
	 */
	public function update($id, $data)
	{
		return $this->database->table($this->table)->get($id)->update($data);
	}

	/**
	 * Wrapper delete() function.
	 * @param array|\Traversable $where
	 * @return int number of affected rows or FALSE in case of an error
	 */
	public function delete(array $where)
	{
		return $this->database->table($this->table)->where($where)->delete();
	}
}
