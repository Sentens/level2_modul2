<?php 
namespace app\controllers;
use League\Plates\Engine;
use app\models\comments;
use app\models\user;
use PDO;
use JasonGrimes\Paginator;

class index{
	
	private $engine;
	private $comments;
	private $pdo;
	private $user;

	function __construct(Engine $engine, comments $comments, pdo $pdo, user $user)
	{
		$this->comments = $comments;
		$this->engine = $engine;
		$this->pdo = $pdo;
		$this->user = $user;
	}

	public function home($page)
	{
		$itemsPerPage = 5;
		$currentPage = $page['page'];

		if (!$currentPage) {
			$currentPage = 1;
		}



		$urlPattern = '/page(:num)';

		// Количество выводимых комментариев
		$totalComments = count($this->comments->getTotalComments());

		$maxPages = ceil($totalComments/$itemsPerPage);
		if ($currentPage > $maxPages and isset($page['page'])) {
			header("Location: /");
		}

		// Постраничный вывод комментариев
		$comments = $this->comments->getAllComments($currentPage, $itemsPerPage);

		// Создаем пагинатор
		$paginator = new Paginator($totalComments, $itemsPerPage, $currentPage, $urlPattern);

		// Выводим наш шаблон с данными
		echo $this->engine->render('index', ['comments' => $comments, 'paginator' => $paginator]);
	}

	//Добавить комментарий
	public function addComment()
	{
		$this->comments->addComment($_POST['comment']);
		header("Location: /");
	}

}


?>