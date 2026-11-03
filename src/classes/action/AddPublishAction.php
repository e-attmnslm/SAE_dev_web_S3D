<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddPublishAction extends Action {

    // CREER 1 USER

    public function execute() : string {
        $html = '<b>PUBLIER UN TOUITE</b> <br>';
        if ($this->http_method === 'GET') {
            // afficher le formulaire
            $html .= <<<FIN
                <form method="post" action="?action=publish-touite">
                    <input type="text" name="nom_user" required placeholder="Nom d'utilisateur">
                    <br><input type="text" name="publication" required placeholder="Texte à publier" minlength="10" maxlength="235">
                    <br><input type="file" name="inputfile" accept="image/png, image/jpeg">(lier une image au Touite, facultatif)
                    <br><button type="submit">Publier</button>
                </form>
            FIN;
        } else {
            // POST : traiter le formulaire
            $nom_user = filter_var($_POST['nom_user'], FILTER_SANITIZE_SPECIAL_CHARS);
            
            $publication = filter_var($_POST['publication'], FILTER_SANITIZE_SPECIAL_CHARS);

            $_SESSION['liste'][] = [$nom_user, $publication];
            $html .= "Publication postée avec succès avec nom d'utilisateur : $nom_user<br>Publication : $publication";
        }
        return $html;
    }
}
?>