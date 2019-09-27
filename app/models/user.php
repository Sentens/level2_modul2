<?php
namespace app\models;
use Delight\Auth\Auth;
use PDO;
use SimpleMail;
use \Tamtamchik\SimpleFlash\Flash;
use Aura\SqlQuery\QueryFactory;
use app\models\queryBuilder;
use Respect\Validation\Validator as v;
use Intervention\Image\ImageManager;

use Exception;
require_once __DIR__ . '/..'.'/exceptions/MyExceptions.php';

use app\exceptions\VerySmallPassword;
use app\exceptions\VerySmallUsername;
use app\exceptions\emailValidation;
use app\exceptions\emailFindValidation;
use app\exceptions\usernameLengthValidation;
use app\exceptions\identicalNewPasswords;


class user
{

	private $pdo;
	private $Auth;
	private $SimpleMail;
	private $queryFactory;
	private $queryBuilder;
	private $manager;

	function __construct(pdo $pdo, Auth $Auth, SimpleMail $SimpleMail, QueryFactory $queryFactory, queryBuilder $queryBuilder, ImageManager $ImageManager)
	{
		$this->pdo = $pdo;
		$this->Auth = $Auth;
		$this->SimpleMail = $SimpleMail;
		$this->queryFactory = $queryFactory;
		$this->queryBuilder = $queryBuilder;
		$this->manager = $ImageManager;
	}

	//Войти
	public function logIn($email, $password)
	{

		try {
		    $this->Auth->login($email, $password);
			// Ищем текущее фото пользователя
			$id = $this->Auth->getUserId();
			$user_photo = $this->queryBuilder->selectOne('users', $id);
			$_SESSION['user_photo'] = $user_photo['user_photo'];
			header("Location: /");
		}
		catch (\Delight\Auth\InvalidEmailException $e) {
			flash()->error(['Неверный емейл или пароль!']);
			header("Location: /login");
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
			flash()->error(['Неверный емейл или пароль!']);
			header("Location: /login");
		    die();
		}
		catch (\Delight\Auth\EmailNotVerifiedException $e) {
			flash()->error(['Електронная почта не подтверждена!']);
			header("Location: /login");
		    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    flash()->error(['Слишком много запросов!']);
			header("Location: /login");
		    die();
		}


	}

	//Выйти
	public function logOut()
	{
		    $this->Auth->logOut();
		    unset($_SESSION['user_photo']);
		    header("Location: /");
	}

	//Изменить профиль пользователя
	public function editProfile($username, $email, $image)
	{
		// id пользователя
		$id = $this->Auth->getUserId();

		//Если username и email соответствуют текущему и поле файл пустое, ничего не делаем
		if ($email == $this->Auth->getEmail() and $username == $_SESSION['auth_username'] and $image['size'] == 0) {
			header("Location: /profile");
			exit;
		}

		if ($image['size'] > 0) {

				//Путь к папке с изображениями
				$path = dirname(__DIR__, 2).'\public\\img\\';

				// Ищем текущее фото пользователя
				$user_photo = $this->queryBuilder->selectOne('users', $id);
				$user_photo = $user_photo['user_photo'];

				//Если изображение аватара не стандартное, есть в директории и это файл, то удаляем его
				if ($user_photo !== 'no-user.jpg' and file_exists($path.$user_photo) and is_file($path.$user_photo)) {
					unlink($path.$user_photo);
				}

				//Сохраняем наш файл в пупку $path
				$img = $this->manager->make($image['tmp_name']);
				$photoName = md5(uniqid()).'.jpeg';
				$img->save($path.$photoName);

				//Сохраняем в сессию наше фото
				$_SESSION['user_photo'] = $photoName;

				//Обновляем поле user_photo для пользователя 
				$update = $this->queryFactory->newUpdate();
				$update
				    ->table('users')
				    ->cols(array('user_photo' => $photoName))
				    ->where('id = :id')
				    ->bindValues(array('id' => $id));
			    $sth = $this->pdo->prepare($update->getStatement());
				$sth->execute($update->getBindValues());

		}

		// Верный ли формат email
		try {
			if (!v::email()->validate($email)) {
				throw new emailValidation("Неверный формат емейла");
			}

		}
		catch (emailValidation $e) {
			flash()->error($e->getMessage());
			header("Location: /profile");
			die();
		}

		// Проверяем длину имени (минимум 3 символа)
		try {
			if (!v::stringType()->length(3, null)->validate($username)){
				throw new usernameLengthValidation("Длина имени должна быть не менее 3 символов");
			}
		}
		catch (usernameLengthValidation $e) {
			flash()->error($e->getMessage());
			header("Location: /profile");
			die();
		}
		
		// Есть ли такой email в БД
		$findEmail = $this->queryBuilder->findEmail('users', $email);
		try {
			// Если новый емейл не являеться действующим и такого в БД нет
			if ($email !== $this->Auth->getEmail() and $email == $findEmail) {
				throw new emailfindValidation("Пользователь с таким email уже существует");	
			}
		}
		catch (emailfindValidation $e) {
			flash()->error($e->getMessage());
			header("Location: /profile");
			die();
		}



		// Выполняем запрос
		$this->queryBuilder->update('users', ['email' => $email, 'username' => $username], $id);

		//Меняем в сессии имя и емейл
		$_SESSION['auth_username'] = $username;
		$_SESSION['auth_email'] = $email;
		flash()->success(['Данные обновлены!']);
		header("Location: /profile");
	}

	//Изменить пароль пользователя
	public function changePasswordProfile($password, $newPassword, $confirm_new_password)
	{

		// Совпадают ли новые пароли
		try {
			if (!v::identical($newPassword)->validate($confirm_new_password)) {
				throw new identicalNewPasswords("Новые пароли не совпадают");
			}

		}
		catch (identicalNewPasswords $e) {
			flash()->error($e->getMessage());
			header("Location: /profile");
			die();
		}

		//Проверяем длину новых паролей
		try {
			if (!v::stringType()->length(3, null)->validate($newPassword) or !v::stringType()->length(3, null)->validate($confirm_new_password)) {
						throw new VerySmallPassword("Длина паролей должна быть не менее 5 символов");
					}
			}
		catch (VerySmallPassword $e) {
		    flash()->error($e->getMessage());
			header("Location: /profile");
		    die();
		}

		try {
	    	$this->Auth->changePassword($password, $newPassword);
			flash()->success(['Пароль успешно обновлен!']);
			header("Location: /profile");
		}
		catch (\Delight\Auth\NotLoggedInException $e) {
		    flash()->error(['Выполните авторизацию']);
			header("Location: /login");
		    die();
		}
		catch (\Delight\Auth\InvalidPasswordException $e) {
		    flash()->error(['Неверный текущий пароль']);
			header("Location: /profile");
		    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
		    flash()->error(['Слишком много запросов']);
			header("Location: /profile");
		    die();
		}

	}
	//Добавить нового пользователя
	public function addUser($email, $password, $username)
	{
			try {

					if (mb_strlen($password) < 5) {
						throw new VerySmallPassword("Длина пароля должна быть не менее 5 символов");
					}

					if (mb_strlen($username) < 5) {
						throw new VerySmallUsername("Длина имени должна быть не менее 5 символов");
					}


				    $userId = $this->Auth->register($email, $password, $username, function ($selector, $token) {
				    	//Получаем токен и селектор
				        $message =  '<a href='.$_SERVER['SERVER_NAME'].'/verification/'.$selector.'/'.$token.'>Подтвердить</a>';

				        //Отправляем токен и селектор на email для подтверждения
					    $this->SimpleMail
										->setTo('sentens@ukr.net', 'Alex')
										->setFrom('test@mrln.com', 'Mrln')
										->setSubject('test')
										->setMessage($message)
										->send();
				    flash()->success(['Подтвердите вашу електронную почту!']);
		    		header("Location: /login");
				    });
			}

			catch (VerySmallPassword $e) {
			    flash()->error($e->getMessage());
				header("Location: /register");
			    die();
			}

			catch (VerySmallUsername $e) {
			    flash()->error($e->getMessage());
				header("Location: /register");
			    die();
			}

			catch (\Delight\Auth\InvalidEmailException $e) {
			    flash()->error(['Неверный емейл или пароль!']);
				header("Location: /register");
			    die();
			}


			catch (\Delight\Auth\InvalidEmailException $e) {
			    flash()->error(['Неверный емейл или пароль!']);
				header("Location: /register");
			    die();
			}
			catch (\Delight\Auth\InvalidPasswordException $e) {
			    flash()->error(['Неверный емейл или пароль!']);
				header("Location: /register");
			    die();
			}
			catch (\Delight\Auth\UserAlreadyExistsException $e) {
			    flash()->error(['Такой пользователь существует!']);
				header("Location: /register");
			    die();
			}
			catch (\Delight\Auth\TooManyRequestsException $e) {
			    flash()->error(['Слишком много запросов!']);
				header("Location: /login");
			    die();
			}
	}

	//Подтвердить електронную почту пользователя
	public function Verification($selector, $token)
	{

		try {
	    		$this->Auth->confirmEmail($selector, $token);
			    flash()->success(['Електронная почта подтверждена!']);
	    		header("Location: /login");
		}
		catch (\Delight\Auth\InvalidSelectorTokenPairException $e) {
			    flash()->error(['Неверный токен!']);
				header("Location: /login");
			    die();
		}
		catch (\Delight\Auth\TokenExpiredException $e) {
			    flash()->error(['Данный токен больше не действителен!']);
				header("Location: /login");
			    die();
		}
		catch (\Delight\Auth\UserAlreadyExistsException $e) {
			    flash()->error(['Пользователь с данным емейлом уже существует']);
				header("Location: /login");
			    die();
		}
		catch (\Delight\Auth\TooManyRequestsException $e) {
			    flash()->error(['Слишком много запросов']);
				header("Location: /login");
			    die();
		}		
	}

}


 ?>