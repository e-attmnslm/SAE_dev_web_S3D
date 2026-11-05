<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class DefaultAction extends Action {

    public function execute() : string {
        $html = '';
        $html .= var_dump($_SESSION);
        
        // CREATIONN TABLEAU STATUT (pour savoir si l'utilisateur est connecté)
        if (!isset($_SESSION['statut'])) {
            $_SESSION['statut']['loggedin'] = false;
        }

        // CREATION TABLEAU COMPTE (pour lister l'ensemble des compte existants)
        if (!isset($_SESSION['compte'])) {
                $_SESSION['compte'] = [];
        }

        return $html;
    }
    
}

?>