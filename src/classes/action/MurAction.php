<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

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
            $html .= "<p>Par <b>{$t['prenom']} {$t['nom']}</b> le {$t['date_public']}</p>";
            $html .= "<p>{$t['texte']}</p>";
            
            if (!empty($t['image_path'])) {
                $html .= "<img src=\"{$t['image_path']}\" style=\"max-height: 200px; max-width: 200px;\"><br>";
            }
            $html .= "</div>";
        }

        return $html;
    }
}