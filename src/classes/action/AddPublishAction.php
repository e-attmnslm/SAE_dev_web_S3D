<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddPublishAction extends Action {

    // CREER 1 USER

    public function execute() : string {
        $html = '<b>PUBLIER UN TOUITE</b> <br>';
        if ($this->http_method === 'GET') {

            // VERIFICATION (on regarde si la liste des touites est déjà créée)
            if (isset($_SESSION['touites'])) {
                $html .= $_SESSION['touites'];
                unset($_SESSION['touites']);
            }

            // DEBUG
            var_dump($_SESSION);


            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=publish-touite" enctype="multipart/form-data">
                    <p>Publication en tant que <b>{$_SESSION['statut']['nom_user']} {$_SESSION['statut']['prenom_user']}</b>
                    <br><input type="text" autofocus name="publication" required placeholder="Texte à publier" minlength="10" maxlength="235">
                    <br><input type="file" name="inputfile" accept="image/png, image/jpeg">(lier une image au Touite png/jpg, facultatif)
                    <br><button type="submit">Publier</button>
                </form>
            FIN;
        } else {
            // POST : traiter le formulaire
            //$nom_user = filter_var($_POST['nom_user'], FILTER_SANITIZE_SPECIAL_CHARS);
            
            $publication = filter_var($_POST['publication'], FILTER_SANITIZE_SPECIAL_CHARS);

            if (isset($_FILES['inputfile']) && $_FILES['inputfile']['error'] !== UPLOAD_ERR_NO_FILE) {

                if ($_FILES['inputfile']['error'] === UPLOAD_ERR_OK) {

                $tmp = $_FILES['inputfile']['tmp_name'];
                $type = $_FILES['inputfile']['type'];

                if ($type === 'image/png' || $type === 'image/jpeg') {

                    $upload_dir = __DIR__ . '/image/';
                    $filename = uniqid();

                    if ($type === 'image/png') {
                        $extension = '.png';
                    } else {
                        $extension = '.jpg';
                    }

                    $dest = $upload_dir.$filename.$extension;

                    if (move_uploaded_file($tmp, $dest)) {
                        $html .= "Image enregistrée avec succès<br>";
                    } else {
                        $html .= "Impossible d'enregistrer l'image<br>";
                    }

                    } else {
                        $html .= "Echec du téléchargement ou type non autorisé<br>";
                    }
                }

                $_SESSION['liste'][] = [$_SESSION['statut']['email_user'], $publication, $filename];
                $date = date('Y-m-d H:i:s');
                $msg = "<div style=\"border: solid;display: inline-block;padding: 5px;\">Publication postée avec succès avec nom d'utilisateur : <b>{$_SESSION['statut'][1]}</b><br>Publication : <b>$publication</b><br>Image : <br><img src=\"src/classes/action/image/$filename$extension\" style=\"max-height: 200px;max-width: 200px;\"><br>Date à laquelle vous avez posté : <b>$date</b></div>";

            } else {
                
                $_SESSION['liste'][] = [$_SESSION['statut']['email_user'], $publication];
                $date = date('Y-m-d H:i:s');
                $msg = "<div style=\"border: solid;display: inline-block;padding: 5px;\">Publication postée avec succès avec nom d'utilisateur : <b>{$_SESSION['statut'][1]}</b><br>Publication : <b>$publication</b><br>Date à laquelle vous avez posté : <b>$date</b></div>";

            }

            $_SESSION['touites'] = $msg;

            // GESTION DES TAGS
            if (!isset($_SESSION['tags'])) {
                $_SESSION['tags'] = [];
            }

            $z = 0;
            while ($z < count($_SESSION['liste'])) {
                if (str_contains($_SESSION['liste'][$z][1], '#')) {
                    $tab = mb_str_split($_SESSION['liste'][$z][1]);
                    $tab[] = ' ';
                    $i = 0;
                    while ($i < count($tab)-1) {
                        if ($tab[$i] === '#') {
                            $str = '';
                            while (($tab[$i] != ' ') && ($i < count($tab))) {
                                $str .= $tab[$i];
                                $i++;
                            }
                            $j = 0;
                            $verif = false;
                            while ($j < count($_SESSION['tags'])) {
                                if ($_SESSION['tags'][$j] === $str) {
                                    $verif = true;
                                }
                                $j++;
                            }
                            if ($verif === false) {
                                $_SESSION['tags'][] = $str;
                            }
                        }
                        $i++;
                    }
                }
                $z++;
            }
            header("Location: ?action=publish-touite");
            exit();
        }
        return $html;
    }
}
?>