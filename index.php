<?php

declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';

use \iutnc\touiteur\dispatch\Dispatcher;
use \iutnc\touiteur\action as act;

$app = (new Dispatcher())
    ->addRoute('default', act\DefaultAction::class)
    ->addRoute('publish-touite', act\AddPublishAction::class);

    //debug temporaire
    var_dump($_FILES);
    var_dump($_SESSION);
    echo "nom d'utilisateur : " . $_SESSION['liste'][0][0] . " texte : " . $_SESSION['liste'][0][1];

try {
    echo $app->run();
} catch (\Exception $e) {
    echo "Oupss, something went wrong";
}
?>