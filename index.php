<?php

declare(strict_types=1);
session_start();

if (!isset($_SESSION['statut'])) {
    $_SESSION['statut'] = ['loggedin' => false];
}
if (!isset($_SESSION['compte'])) {
    $_SESSION['compte'] = [];
}

require_once 'vendor/autoload.php';

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
    http_response_code(500);
    echo "Une erreur est survenue pendant le traitement de la demande.";
}
?>