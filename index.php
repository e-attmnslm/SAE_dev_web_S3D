<?php

declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';
use iutnc\touiteur\repository\TouiteurRepository;
TouiteurRepository::setConfig('config.db.ini');
use \iutnc\touiteur\dispatch\Dispatcher;
use \iutnc\touiteur\action as act;

$app = (new Dispatcher())
    ->addRoute('default', act\DefaultAction::class)
    ->addRoute('publish-touite', act\AddPublishAction::class)
    ->addRoute('login', act\AddLoginAction::class)
    ->addRoute('signup', act\AddSignUpAction::class)
    ->addRoute('signout', act\AddSignOutAction::class);

try {
    echo $app->run();
} catch (\Exception $e) {
    echo "Oupss, something went wrong";
}
?>