<?php

namespace iutnc\touiteur\dispatch;

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
        echo <<<HTML
            <!doctype html>
            <html lang="fr">
            <head>
                <meta charset="utf-8">
                <title>Touiteur.app</title>
            </head>
            <body>  
                <h1>Touiteur</h1>

                <main>
                    {$html}
                </main>
            </body>
            </html>
            HTML;
    }
}

?>