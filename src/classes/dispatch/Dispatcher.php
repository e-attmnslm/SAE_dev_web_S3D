<?php

declare(strict_types=1);

namespace iutnc\touiteur\dispatch;

use \iutnc\touiteur\action\AddSignOutAction;

class Dispatcher {
    protected ?string $action = null;
    protected $actions = [];

    public function __construct(){
        $this->action = $_GET['action'] ?? 'default';
    }

    public function addRoute(string $action, string $action_class) : self {
        $this->actions[$action] = $action_class;
        return $this;
    }

    public function run() : void {
        if (isset($this->actions[$this->action])) {
            $a = (new $this->actions[$this->action]())->execute();
            echo $this->renderPage($a);
        } else {
            throw new \Exception("Action '{$this->action}' not found");
        }
    }

    private function renderPage(string $html): void
    {
        if (isset($_SESSION['statut'])) {
            if ($_SESSION['statut'][0] === true) {
                $statut = " | <a href=\"?action=signout\">Déconnecter</a>";
                $publier = " | <a href=\"?action=publish-touite\">Publier</a>";
            } else {
                $statut = "";
                $publier = "";
            }
            if ($_SESSION['statut'][0] === false) {
                $creer = "<a href=\"?action=signup\">Créer un compte</a> | ";
                $connexion = "<a href=\"?action=login\">Connexion</a> |";
            } else {
                $creer = "";
                $connexion = "";
            }
        }
        echo <<<HTML
            <!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <title>Touiteur.app</title>
            </head>
            <body>
                <h1>Touiteur</h1>

                <nav>
                    {$creer}
                    {$connexion}
                    <a href="?action=default">Accueil</a> |
                    <a href="?action=mur">Mur</a>
                    {$publier}
                    {$statut}
                </nav>

                <main>
                    {$html}
                </main>
            </body>
            </html>
            HTML;
    }
}

?>