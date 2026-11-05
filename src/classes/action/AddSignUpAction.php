<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddSignUpAction extends Action {

    public function execute() : string {
        $html = '<b>CREER UN COMPTE</b> <br>';
        if ($this->http_method === 'GET') {

            // DEBUG
            var_dump($_SESSION);


            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=signup" enctype="multipart/form-data">
                    <input type="text" name="nom_user" required placeholder="Nom d'utilisateur">
                    <br><button type="submit">Créer un compte</button>
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
                            $html .= "<p>/!\ Erreur : Compte déjà existant /!\</p>";
                        }
                    }
                }
            }

            if (($verif === false) && ($_SESSION['statut'][0] === false)) {
                $_SESSION['compte'][] = $nom_user;
                $html .= "<p>Compte <b>$nom_user</b> créé avec succès</p>
                          <p>Vous avez été automatiquement connecté à votre compte</p>";
                $_SESSION['statut'][0] = true;
                $_SESSION['statut'][1] = $nom_user;
            } else if ($_SESSION['statut'][0] === true) {
                $html .= "<p>/!\ Erreur : Vous ne pouvez pas créer un nouveau compte car vous êtes déjà connecté /!\</p>";
            }
        }
        return $html;
    }
    
}

?>