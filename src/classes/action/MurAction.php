<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteMessageRender;

class MurAction extends Action {
    public function execute() : string {
        if (!isset($_SESSION['statut']['loggedin']) || $_SESSION['statut']['loggedin'] === false) {
            return "<p>Vous devez être connecté pour voir votre mur.</p>";
        }

        $idUti = $_SESSION['statut']['id_uti'];
        $repo = TouiteurRepository::getInstance();
        $touites = $repo->getMurUtilisateur($idUti);

        $html = "<h2>Mon Mur d'actualité</h2>";

        if (empty($touites)) {
            $html .= "<p>Votre mur est vide. Abonnez-vous à des utilisateurs ou des tags pour voir du contenu !</p>";
            return $html;
        }

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