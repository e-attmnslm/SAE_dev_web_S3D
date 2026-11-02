<?php

declare(strict_types=1);
session_start();

require_once 'vendor/autoload.php';

use \iutnc\touiteur\dispatch\Dispatcher;

$app = (new Dispatcher());
try {
    echo $app->run();
} catch (\Exception $e) {
    echo "Oupss, something went wrong";
}
?>