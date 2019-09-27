<?php 
namespace app\controllers;
use League\Plates\Engine;
use app\models\user;
use Delight\Auth\Auth;
use PDO;

class profileController{
	
	private $engine;
	private $comments;
	private $pdo;
	private $auth;
	//Данные пользователя
	private $username;
	private $email;
	private $image;

	//Пароль пользователя
	private $password;
	private $new_password;
	private $confirm_new_password;

	function __construct(Engine $engine, pdo $pdo, user $user, auth $auth)
	{
		$this->engine = $engine;
		$this->pdo = $pdo;
		$this->user = $user;
		$this->auth = $auth;
	}
	// Выводим наш профиль
	public function profile()
	{
		//Если пользователь авторизирован
		if ($this->auth->isLoggedIn()) {
			// Выводим наш профиль
			echo $this->engine->render('profile');
		}else{
			// Перенаправляем на страницу авторизации
			header("Location: /login");
		}
	}

	// Редактируем наш профиль
	public function editProfile()
	{
		//Если пользователь авторизирован
		if ($this->auth->isLoggedIn()) {

			$this->username = $_POST['username'];
			$this->email = $_POST['email'];
			$this->image = $_FILES['image'];

			$this->user->editProfile($this->username, $this->email, $this->image);

		}else{
			// Перенаправляем на страницу авторизации
			header("Location: /login");
		}
	}

	// Меняем пароль
	public function changePasswordProfile()
	{
		//Если пользователь авторизирован
		if ($this->auth->isLoggedIn()) {

			$this->password = $_POST['password'];
			$this->new_password = $_POST['new_password'];
			$this->confirm_new_password = $_POST['confirm_new_password'];

			$this->user->changePasswordProfile($this->password, $this->new_password, $this->confirm_new_password);

		}else{
			// Перенаправляем на страницу авторизации
			header("Location: /login");
		}
	}
}


?>