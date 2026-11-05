<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class AddPublishAction extends Action
{
    public function execute(): string
    {
        $loggedIn = ($_SESSION['statut']['loggedin'] ?? false) === true;
        if ($this->http_method === 'GET') {
            $authorField = $loggedIn
                ? '<p>Publication en tant que <b>'
                    . htmlspecialchars(
                        trim((string) ($_SESSION['statut']['nom_user'] ?? '') . ' '
                            . (string) ($_SESSION['statut']['prenom_user'] ?? '')),
                        ENT_QUOTES | ENT_SUBSTITUTE,
                        'UTF-8'
                    )
                    . '</b></p>'
                : '<input type="text" name="nom_user" required placeholder="Nom d’utilisateur">';

            return <<<HTML
                <h2>Publier un touite</h2>
                <form method="post" action="?action=publish-touite" enctype="multipart/form-data">
                    {$authorField}
                    <br><textarea class="autosize" rows="5" cols="40" name="publication" required placeholder="Texte à publier" minlength="10" maxlength="235"></textarea>
                    <style>
                        .autosize {
                            width: 15em;
                            min-height: 5em;
                            field-sizing: content;
                            overflow: hidden;
                            resize: none;
                        }
                    </style>
                    <br><input type="file" name="inputfile" accept="image/png, image/jpeg">(lier une image au Touite png/jpg, facultatif)
                    <br><button type="submit">Publier</button>
                </form>
            HTML;
        }

        $author = $loggedIn
            ? trim((string) ($_SESSION['statut']['nom_user'] ?? '') . ' '
                . (string) ($_SESSION['statut']['prenom_user'] ?? ''))
            : trim((string) ($_POST['nom_user'] ?? ''));
        $publication = trim((string) ($_POST['publication'] ?? ''));
        if ($author === '') {
            return '<p>Le nom d’utilisateur est obligatoire.</p>';
        }
        if ($publication === '') {
            return '<p>Le texte du touite est obligatoire.</p>';
        }
        if (mb_strlen($publication) < 10 || mb_strlen($publication) > 235) {
            return '<p>Le texte doit contenir entre 10 et 235 caractères.</p>';
        }

        $imagePath = null;
        $uploadedFile = $_FILES['inputfile'] ?? null;
        if (is_array($uploadedFile) && ($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE) {
            if (($uploadedFile['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                return '<p>Le téléchargement de l’image a échoué.</p>';
            }

            $temporaryPath = (string) ($uploadedFile['tmp_name'] ?? '');
            $imageInfo = $temporaryPath !== '' ? getimagesize($temporaryPath) : false;
            $mimeType = is_array($imageInfo) ? ($imageInfo['mime'] ?? '') : '';
            $extensions = ['image/png' => '.png', 'image/jpeg' => '.jpg'];
            if (!isset($extensions[$mimeType])) {
                return '<p>Seules les images PNG et JPEG sont autorisées.</p>';
            }

            $uploadDirectory = __DIR__ . '/image';
            if (!is_dir($uploadDirectory) && !mkdir($uploadDirectory, 0775, true) && !is_dir($uploadDirectory)) {
                throw new \RuntimeException('Impossible de créer le dossier des images.');
            }

            $filename = bin2hex(random_bytes(16)) . $extensions[$mimeType];
            if (!move_uploaded_file($temporaryPath, $uploadDirectory . '/' . $filename)) {
                throw new \RuntimeException('Impossible d’enregistrer l’image.');
            }
            $imagePath = 'src/classes/action/image/' . $filename;
        }

        (new TouiteurRepository())->add($author, $publication, $imagePath);
        header('Location: ?action=default');
        return '';
    }
}
