<?php 
namespace app\controllers;
use League\Plates\Engine;
use app\models\user;
use Delight\Auth\Auth;
use app\models\comments;
use PDO;

	
		
class adminController{

	private $engine;
	private $comments;
	private $pdo;
	private $auth;

	function __construct(Engine $engine, pdo $pdo, user $user, auth $auth, comments $comments)
	{
		$this->engine = $engine;
		$this->pdo = $pdo;
		$this->user = $user;
		$this->auth = $auth;
		$this->comments = $comments;
	}

	//Вывести страницу /admin
	public function admin()
	{
		//Если пользователь авторизирован и роль админ (не добавлял)
		if ($this->auth->isLoggedIn()) {

			$comments = $this->comments->getAllCommentsWithoutHidden();

			// Выводим нашу админку
			echo $this->engine->render('admin', ['comments' => $comments]);
		}else{
			// Перенаправляем на страницу авторизации
			header("Location: /login");
		}
	}

	//Скрыть комментарий
	public function hide_comment($id)
	{
		$comments = $this->comments->CommentHidden($id, '1');
		header("Location: /admin");
	}

	//Показать комментарий
	public function show_comment($id)
	{
		$comments = $this->comments->CommentHidden($id, '0');
		header("Location: /admin");
	}

	//Удалить комментарий
	public function delete_comment($id)
	{
		$comments = $this->comments->CommentDelete($id);
		header("Location: /admin");
	}
}


?>