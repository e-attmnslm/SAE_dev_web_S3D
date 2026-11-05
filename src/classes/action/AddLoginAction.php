<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddLoginAction extends Action {

    public function execute() : string {
        if (($_SESSION['statut']['loggedin'] ?? false) === true) {
            return '<p>Vous êtes déjà connecté en tant que <b>'
                . htmlspecialchars((string) $_SESSION['statut']['email_user'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                . '</b>.</p>';
        }

        $html = '<b>SE CONNECTER</b> <br>';
        if ($this->http_method === 'GET') {
            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=login" enctype="multipart/form-data">
                    <input type="email" name="email_user" required placeholder="Email">
                    <br><button type="submit">Se connecter</button>
                </form>
            FIN;
        } else {
            // POST : traiter le formulaire
            $email_user = filter_var($_POST['email_user'] ?? '', FILTER_SANITIZE_SPECIAL_CHARS);

            $verif = false;
            if (($_SESSION['statut']['loggedin'] ?? false) === false) {
                if (isset($_SESSION['compte'])) {
                    foreach ($_SESSION['compte'] as $key => $value) {
                        if ($value['email_user'] === $email_user) {

                            $verif = true;

                            $_SESSION['statut']['loggedin'] = true;
                            $_SESSION['statut']['nom_user'] = $_SESSION['compte'][$key]['nom_user'];
                            $_SESSION['statut']['prenom_user'] = $_SESSION['compte'][$key]['prenom_user'];
                            $_SESSION['statut']['email_user'] = $email_user;

                            $html .= 'Connexion au compte ayant pour email <b>'
                                . htmlspecialchars($email_user, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')
                                . '</b> effectuée avec succès';
                        
                        }
                    }
                } else {
                    $html .= "<p>Aucun compte existant dans la session actuelle</p>";
                }
            } else {
                $verif = true;
                $html .= '<p>Erreur : vous êtes déjà connecté.</p>';
            }

            if ($verif === false) {
                $html .= '<p>Erreur : aucun utilisateur avec cette adresse email.</p>';
            }

        }
        return $html;
    }

}

?>