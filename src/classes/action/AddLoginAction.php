<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddLoginAction extends Action {

    public function execute() : string {
        $html = '<b>SE CONNECTER</b> <br>';
        if ($this->http_method === 'GET') {

            // DEBUG
            var_dump($_SESSION);


            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=login" enctype="multipart/form-data">
                    <input type="text" name="nom_user" required placeholder="Nom d'utilisateur">
                    <br><button type="submit">Se connecter</button>
                </form>
            FIN;
        } else {
            // POST : traiter le formulaire
            $nom_user = filter_var($_POST['nom_user'], FILTER_SANITIZE_SPECIAL_CHARS);

            $verif = false;
            if ($_SESSION['statut'][0] === false) {
                if (isset($_SESSION['compte'])) {
                    foreach ($_SESSION['compte'] as $key => $value) {
                        if ($value === $nom_user) {
                            $verif = true;
                            $_SESSION['statut'][0] = true;
                            $_SESSION['statut'][1] = $nom_user;
                            $html .= "Connecté en tant que <b>$nom_user</b> avec succès";
                        }
                    }
                } else {
                    $html .= "<p>Aucun compte existant dans la session actuelle</p>";
                }
            } else {
                $verif = true;
                $html .= "<p>/!\ Erreur : Vous êtes déjà connecté en tant que <b>{$_SESSION['statut'][1]}</b> /!\</p>";
            }

            if ($verif === false) {
                $html .= "<p>/!\ Erreur : Utilisateur <b>$nom_user</b> non existant /!\</p>";
            }

        }
        return $html;
    }
    
}

?>