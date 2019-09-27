<?php

	//Главная
    $r->addRoute('GET', '/', ['app\controllers\index','home']);
    $r->addRoute('GET', '/home', ['app\controllers\index','home']);
    $r->addRoute('GET', '/page{page:\d+}', ['app\controllers\index','home']);

    //Добавить комментарий
    $r->addRoute('POST', '/index/addComment', ['app\controllers\index','addComment']);


    //Страница входа
    $r->addRoute('GET', '/login', ['app\controllers\userController','LoginForm']);

    //Страница регистрации
    $r->addRoute('GET', '/register', ['app\controllers\userController','RegisterForm']);

    //Обработчик метода войти
    $r->addRoute('POST', '/login', ['app\controllers\userController','login']);

    //Обработчик метода выйти
    $r->addRoute('GET', '/logout', ['app\controllers\userController','logout']);


    //Добавление пользователя
    $r->addRoute('POST', '/adduser', ['app\controllers\userController','adduser']);

    //Верификация email
    $r->addRoute('GET', '/verification/{selector}/{token}', ['app\controllers\userController','Verification']);

    //Профиль
    $r->addRoute('GET', '/profile', ['app\controllers\profileController','profile']);
    $r->addRoute('POST', '/profile/editProfile', ['app\controllers\profileController','editProfile']);
    $r->addRoute('POST', '/profile/changePasswords', ['app\controllers\profileController','changePasswordProfile']);

    //Админка
    $r->addRoute('GET', '/admin', ['app\controllers\adminController','admin']);
    $r->addRoute('GET', '/admin/hide_comment/{id:\d+}', ['app\controllers\adminController','hide_comment']);
    $r->addRoute('GET', '/admin/show_comment/{id:\d+}', ['app\controllers\adminController','show_comment']);
    $r->addRoute('GET', '/admin/delete_comment/{id:\d+}', ['app\controllers\adminController','delete_comment']);


 ?>