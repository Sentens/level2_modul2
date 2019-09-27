<?php 
namespace App\models;

use Aura\SqlQuery\QueryFactory;
use PDO;

class QueryBuilder
{

	private $pdo;
	private $queryFactory;

	public function __construct(pdo $pdo, QueryFactory $QueryFactory){
		$this->pdo = $pdo;
		$this->queryFactory = $QueryFactory;
	}

	public function selectAll($table)
	{
		$select = $this->queryFactory->newSelect();
		$select->cols(['*'])->from($table);
		$sth = $this->pdo->prepare($select->getStatement());
		$sth->execute($select->getBindValues());
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	public function selectOne($table, $id)
	{
		$select = $this->queryFactory->newSelect();
		$select
			->cols(['*'])
			->from($table)
			->where('id = :id')
			->bindValues(['id' => $id]);
		$sth = $this->pdo->prepare($select->getStatement());
		$sth->execute($select->getBindValues());
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result;
	}

	//Найти пользователя с таким емейлом
	public function findEmail($table, $email)
	{
		$select = $this->queryFactory->newSelect();
		$select
			->cols(['email'])
			->from($table)
			->where('email = :email')
			->bindValues(['email' => $email]);
		$sth = $this->pdo->prepare($select->getStatement());
		$sth->execute($select->getBindValues());
		$result = $sth->fetch(PDO::FETCH_ASSOC);
		return $result['email'];
	}

	public function insert($table, $data)
	{
			$insert = $this->queryFactory->newInsert();
			$insert
				->into($table)
			    ->cols($data);
			$sth = $this->pdo->prepare($insert->getStatement());
			$sth->execute($insert->getBindValues());
	}

	public function update($table, $data, $id)
	{
		$update = $this->queryFactory->newUpdate();

		$update
		    ->table($table)
		    ->cols($data)
		    ->where('id = :id')
		    ->bindValues(['id' => $id]);
			$sth = $this->pdo->prepare($update->getStatement());
			$sth->execute($update->getBindValues());
	}

	public function delete($table, $id)
	{
		$delete = $this->queryFactory->newDelete();

		$delete
		    ->from($table)
		    ->where('id = :id')
		    ->bindValue('id', $id);
			$sth = $this->pdo->prepare($delete->getStatement());
			$sth->execute($delete->getBindValues());
	}



}


 ?>