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
        // Initialisation par défaut pour éviter les warnings
        $creer = "";
        $connexion = "";
        $publier = "";
        $statut = "";

        if (isset($_SESSION['statut'])) {
            if ($_SESSION['statut']['loggedin'] === true) {
                $statut = " | <a href=\"?action=signout\">Déconnecter</a>";
                $publier = " | <a href=\"?action=publish-touite\">Publier</a>";
                $influenceurs = " | <a href=\"?action=liste-influenceurs\">Liste des influenceurs</a>";
            if ($_SESSION['statut']['loggedin'] === false) {
                $creer = "<a href=\"?action=signup\">Créer un compte</a> | ";
                $connexion = "<a href=\"?action=login\">Connexion</a> |";
            }
        }
        
        echo <<<HTML
            <!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <title>Touiteur.app</title>
                <style>
                    .touite-message:not([open]) .touite-full,
                    .touite-message[open] .touite-short {
                        display: none;
                    }
                </style>
            </head>
            <body>
                <h1>Touiteur .main</h1>

                <nav>
                    {$creer}
                    {$connexion}
                    <a href="?action=default">Accueil</a> |
                    <a href="?action=mur">Mur</a>
                    {$publier}
                    {$influenceurs}
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