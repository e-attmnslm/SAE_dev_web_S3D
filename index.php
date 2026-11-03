<?php

declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';

use \iutnc\touiteur\dispatch\Dispatcher;
use \iutnc\touiteur\action as act;

$app = (new Dispatcher())
    ->addRoute('default', act\DefaultAction::class)
    ->addRoute('publish-touite', act\AddPublishAction::class);

try {
    echo $app->run();
} catch (\Exception $e) {
    http_response_code(500);
    echo "Une erreur est survenue pendant le traitement de la demande.";
}
?>