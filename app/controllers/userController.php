<?php 
namespace app\controllers;
use League\Plates\Engine;
use app\models\user;

class userController{

	private $engine;
	private $user;
	
	function __construct(Engine $engine, user $user)
	{
		$this->engine = $engine;
		$this->user = $user;
	}
	//Страница входа
	public function LoginForm()
	{
		echo $this->engine->render('login');
	}

	//Страница регистрации
	public function RegisterForm()
	{
		echo $this->engine->render('register');
	}

	//Войти
	public function logIn()
	{
		$this->user->logIn($_POST['email'], $_POST['password']);
	}

	//Выйти
	public function logOut()
	{
		$this->user->logOut();
	}

	//Зарегистрироватся
	public function adduser()
	{
		$this->user->addUser($_POST['email'], $_POST['password'], $_POST['username']);
	}

	//Подтвердить email
	public function Verification($arg)
	{
		$this->user->Verification($arg['selector'], $arg['token']);
	}

}


 ?>