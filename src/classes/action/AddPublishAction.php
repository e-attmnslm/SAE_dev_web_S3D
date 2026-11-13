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
            $publication = filter_var($_POST['publication'], FILTER_SANITIZE_SPECIAL_CHARS);
            $filename = null;
            $extension = null;
            $cheminImage = null;

            $idImg = null;
            $repo = \iutnc\touiteur\repository\TouiteurRepository::getInstance();
            if (isset($_FILES['inputfile']) && $_FILES['inputfile']['error'] === UPLOAD_ERR_OK) {
                $tmp = $_FILES['inputfile']['tmp_name'];
                $type = $_FILES['inputfile']['type'];

                if ($type === 'image/png' || $type === 'image/jpeg') {
                    $upload_dir = __DIR__ . '/image/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    $filename = uniqid();
                    $extension = ($type === 'image/png') ? '.png' : '.jpg';
                    $dest = $upload_dir . $filename . $extension;

                    if (move_uploaded_file($tmp, $dest)) {
                        $cheminImage = 'src/classes/action/image/' . $filename . $extension;
                        
                        // Enregistrement de l'image dans sa table et récupération de son ID
                        $idImg = $repo->saveImage($cheminImage);
                    }
                }
            }

            
            // 1. Enregistrement en base de données via le Repository
            
            $idUti = $_SESSION['statut']['id_uti'];
            $idTouite = $repo->saveTouite($publication, $idUti, $idImg);

            // 2. Conserve ton système de tags initial mais en l'adaptant pour insérer en base
            if (str_contains($publication, '#')) {
                $tab = mb_str_split($publication);
                $tab[] = ' ';
                $i = 0;
                while ($i < count($tab) - 1) {
                    if ($tab[$i] === '#') {
                        $str = '';
                        while (($tab[$i] != ' ') && ($i < count($tab))) {
                            $str .= $tab[$i];
                            $i++;
                        }
                        // On enlève le '#' pour garder juste le libellé du tag
                        $libelleTag = ltrim($str, '#');
                        if (!empty($libelleTag)) {
                            $idTag = $repo->getOrCreateTag($libelleTag);
                            $repo->linkTouiteTag($idTouite, $idTag);
                        }
                    }
                    $i++;
                }
            }

            header("Location: ?action=default");
            exit();
        }
        return $html;
    }
}
?>