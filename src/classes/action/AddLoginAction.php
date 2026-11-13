<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class AddLoginAction extends Action {

    public function execute() : string {
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
            $email_user = filter_var($_POST['email_user'], FILTER_SANITIZE_SPECIAL_CHARS);

            if ($_SESSION['statut']['loggedin'] === true) {
                return $html . "<p>/!\ Erreur : Vous êtes déjà connecté en tant que <b>{$_SESSION['statut']['email_user']}</b> /!\</p>";
            }

            // Recherche de l'utilisateur dans la base de données
            $repo = TouiteurRepository::getInstance();
            $user = $repo->getUtilisateurByEmail($email_user);

            if ($user) {
                // Connexion réussie, on stocke les infos de la base dans la session
                $_SESSION['statut']['loggedin'] = true;
                $_SESSION['statut']['id_uti'] = $user['id_uti'];
                $_SESSION['statut']['nom_user'] = $user['nom'];
                $_SESSION['statut']['prenom_user'] = $user['prenom'];
                $_SESSION['statut']['email_user'] = $user['mail'];

                $html .= "Connexion au compte de <b>{$user['prenom']} {$user['nom']}</b> effectuée avec succès";
            } else {
                $html .= "<p>/!\ Erreur : Aucun compte associé à l'email <b>$email_user</b> /!\</p>";
            }
        }
        return $html;
    }
}