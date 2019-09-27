<?php 
namespace app\exceptions;

class MyExceptions extends \Exception {}

class VerySmallPassword extends MyExceptions {}

class VerySmallUsername extends MyExceptions {}

class CommentAdded extends MyExceptions {}

class SmallComment extends MyExceptions {}

class emailValidation extends MyExceptions {}

class emailFindValidation extends MyExceptions {}

class usernameLengthValidation extends MyExceptions {}

class identicalNewPasswords extends MyExceptions {}

?>
