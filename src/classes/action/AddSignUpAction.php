<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddSignUpAction extends Action {

    public function execute() : string {
        $html = '<b>CREER UN COMPTE</b> <br>';
        if ($this->http_method === 'GET') {
            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=signup" enctype="multipart/form-data">
                    <input type="text" name="nom_user" minlength="3" maxlength="18" required placeholder="Nom">
                    <input type="text" name="prenom_user" minlength="3" maxlength="25" required placeholder="Prénom">
                    <input type="email" name="email_user" required placeholder="Email">
                    <br><button type="submit">Créer un compte</button>
                </form>
            FIN;
        } else {
            // POST : traiter le formulaire
            $nom_user = filter_var($_POST['nom_user'], FILTER_SANITIZE_SPECIAL_CHARS);
            $prenom_user = filter_var($_POST['prenom_user'], FILTER_SANITIZE_SPECIAL_CHARS);
            $email_user = filter_var($_POST['email_user'], FILTER_SANITIZE_SPECIAL_CHARS);

            $verif = false;
            if ($_SESSION['statut']['loggedin'] === false) {
                if (isset($_SESSION['compte'])) {
                    foreach ($_SESSION['compte'] as $key => $value) {
                        if ($value['email_user'] === $email_user) {
                            $verif = true;
                            $html .= "<p>/!\ Erreur : Compte déjà existant /!\</p>";
                        }
                    }
                }
            }

            if (($verif === false) && ($_SESSION['statut']['loggedin'] === false)) {

                $nb_comptes = count($_SESSION['compte']);
                $_SESSION['compte'][$nb_comptes+1]['nom_user'] = $nom_user;
                $_SESSION['compte'][$nb_comptes+1]['prenom_user'] = $prenom_user;
                $_SESSION['compte'][$nb_comptes+1]['email_user'] = $email_user;

                $html .= "<p>Compte créé avec succès :<br>Nom : <b>$nom_user</b><br>Prénom : <b>$prenom_user</b><br>Email : <b>$email_user</b></p>
                          <p>Vous avez été automatiquement connecté à votre compte</p>";

                $_SESSION['statut']['loggedin'] = true;
                $_SESSION['statut']['nom_user'] = $nom_user;
                $_SESSION['statut']['prenom_user'] = $prenom_user;
                $_SESSION['statut']['email_user'] = $email_user;

            } else if ($_SESSION['statut']['loggedin'] === true) {

                $html .= "<p>/!\ Erreur : Vous ne pouvez pas créer un nouveau compte car vous êtes déjà connecté /!\</p>";
            
            }
        }
        return $html;
    }
    
}

?>