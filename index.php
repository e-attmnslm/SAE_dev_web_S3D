<?php

declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';

use \iutnc\touiteur\dispatch\Dispatcher;
use \iutnc\touiteur\action\DefaultAction;

$app = (new Dispatcher())->addRoute('default', DefaultAction::class);
try {
    echo $app->run();
} catch (\Exception $e) {
    echo "Oupss, something went wrong";
}
?>