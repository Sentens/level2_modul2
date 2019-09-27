<?php 
namespace App\models;
use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use PDO;

use Exception;
require_once __DIR__ . '/..'.'/exceptions/MyExceptions.php';
use app\exceptions\CommentAdded;
use app\exceptions\SmallComment;

class comments
{

	private $pdo;
	private $queryFactory;
	private $auth;

	function __construct(pdo $pdo, QueryFactory $QueryFactory, auth $auth)
	{
		$this->pdo = $pdo;
		$this->queryFactory = $QueryFactory;
		$this->auth = $auth;
	}

	// Показать все комментарии
	public function getTotalComments()
	{	
		$totalItems = $this->queryFactory->newSelect();
		$totalItems->cols(['c.id', 'c.comment', 'c.hidden', 'c.date', 'u.user_photo', 'u.username'])
					->from('comments AS c')
					->join('LEFT', 'users AS u','c.id_user = u.id')
					->where('hidden = 0');
		$sth = $this->pdo->prepare($totalItems->getStatement());
		$sth->execute($totalItems->getBindValues());
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}

	// Показать все комментарии
	public function getAllComments($page, $itemsPerPage)
	{	
		$currentPage = $page;
		$itemsPerPage = $itemsPerPage;

		$getAllComments = $this->queryFactory->newSelect();
		$getAllComments->cols(['c.id', 'c.comment', 'c.hidden', 'c.date', 'u.user_photo', 'u.username'])
						->from('comments AS c')
						->join('LEFT', 'users AS u','c.id_user = u.id')
						->where('hidden = 0')
						->page($currentPage)
						->setPaging($itemsPerPage)
						->orderBy(array('date DESC'));
		$sth = $this->pdo->prepare($getAllComments->getStatement());
		$sth->execute($getAllComments->getBindValues());
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}



	// Показать все комментарии
	public function getAllCommentsWithoutHidden()
	{	
		$getAllComments = $this->queryFactory->newSelect();
		$getAllComments
					->cols(['c.id', 'c.comment', 'c.hidden', 'c.date', 'u.user_photo', 'u.username'])
					->from('comments AS c')
					->join('LEFT', 'users AS u','c.id_user = u.id')
					->orderBy(array('date DESC'));
					
		$sth = $this->pdo->prepare($getAllComments->getStatement());
		$sth->execute($getAllComments->getBindValues());
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		return $result;
	}


	// Скрыть комментарий
	public function CommentHidden($id, $hidden)
	{	
		$update = $this->queryFactory->newUpdate();
		$update
		    ->table('comments')                  // update this table
		    ->cols(array(                   // bind values as "SET bar = :bar"
		        'hidden' => $hidden
		    ))
		    ->where('id = :id')           // AND WHERE these conditions
		    ->bindValues(array(             // bind these values to the query
		        'id' => $id['id']
		    ));
		    $sth = $this->pdo->prepare($update->getStatement());
			$sth->execute($update->getBindValues());
	}

	// Удалить комментарий
	public function CommentDelete($id)
	{	
		
		$delete = $this->queryFactory->newDelete();
				$delete
				    ->from('comments')                   // FROM this table
				    ->where('id = :id')
				    ->bindValues(array(             // bind these values to the query
				        'id' => $id['id'],
				    ));
		    $sth = $this->pdo->prepare($delete->getStatement());
			$sth->execute($delete->getBindValues());
	}


	// Добавить комментарий
	public function addComment($comment)
	{	
			try {

				if (mb_strlen($comment) < 20) {
					throw new SmallComment("Коментарий должен быть не менее 20 символов");
				}


				$id = $this->auth->getUserId();
					$insertComment = $this->queryFactory->newInsert();
					$insertComment->into('comments')
								    ->cols([
								    	'id_user' => $id,
								        'comment' => $comment
								    ]);
					$sth = $this->pdo->prepare($insertComment->getStatement());
					$sth->execute($insertComment->getBindValues());

					throw new CommentAdded("Комментарий успешно добавлен");

			}

			catch (CommentAdded $e) {
					flash()->success($e->getMessage());
					header("Location: /");
				    die();
			}

			catch (SmallComment $e) {
					flash()->error($e->getMessage());
					header("Location: /");
				    die();
			}		
	}

}
 ?>