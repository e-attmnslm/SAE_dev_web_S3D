<?php
declare(strict_types=1);
use iutnc\touiteur\action\SuivreUtiAction;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'vendor/autoload.php';

use iutnc\touiteur\repository\TouiteurRepository;
TouiteurRepository::setConfig('config.db.ini');
use iutnc\touiteur\dispatch\Dispatcher;
use iutnc\touiteur\action as act;
// Initialisation propre si la session est vide
if (!isset($_SESSION['statut'])) {
    $_SESSION['statut'] = ['loggedin' => false];
}
if (!isset($_SESSION['compte'])) {
    $_SESSION['compte'] = [];
}
 
$app = (new Dispatcher())
    ->addRoute('default', act\DefaultAction::class)
    ->addRoute('publish-touite', act\AddPublishAction::class)
    ->addRoute('login', act\AddLoginAction::class)
    ->addRoute('signup', act\AddSignUpAction::class)
    ->addRoute('signout', act\AddSignOutAction::class)
    ->addRoute('tag-touites', act\TagTouitesAction::class)
    ->addRoute('user-touites', act\UserTouitesAction::class)
    ->addRoute('mur', act\MurAction::class)          // Route pour afficher le mur personnalisé
    ->addRoute('subscribe', act\SuivreUtiAction::class); // Route pour traiter l'abonnement (utilisateurs / tags)
try {
    $app->run();
} catch (\Exception $e) {
    echo "Oupss, something went wrong : " . $e->getMessage();
}