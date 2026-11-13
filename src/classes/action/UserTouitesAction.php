<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteMessageRender;

class UserTouitesAction extends Action {
    public function execute(): string {
        $id_uti = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id_uti) {
            return "<p>Utilisateur invalide.</p>";
        }

        $repo = TouiteurRepository::getInstance();
        $touites = $repo->getTouitesByUser($id_uti);

        $html = '<p><a href="?action=default">Retour à l’accueil</a></p>';
        $html .= "<h2>Touites de l'utilisateur</h2>";
        foreach ($touites as $t) {
            $html .= "<div style=\"border: 1px solid #ccc; margin: 10px; padding: 10px;\">";
            $html .= "<p>Par <a href=\"?action=user-touites&amp;id={$t['id_uti']}\"><b>{$t['prenom']} {$t['nom']}</b></a> le {$t['date_public']}</p>";
            $html .= '<div>' . TouiteMessageRender::render(
                $t['texte'],
                true,
                $t['image_path'] ?? null
            ) . '</div>';
            $html .= "</div>";
        }
        return $html;
    }
}