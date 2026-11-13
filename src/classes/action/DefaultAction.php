<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteMessageRender;

class DefaultAction extends Action {

    public function execute() : string {
        // Initialisation des sessions si besoin
        if (!isset($_SESSION['statut'])) {
            $_SESSION['statut']['loggedin'] = false;
        }
        if (!isset($_SESSION['compte'])) {
            $_SESSION['compte'] = [];
        }

        // Affichage du fil d'actualité
        $repo = TouiteurRepository::getInstance();
        $touites = $repo->getAllTouites();

        $html = "<h2>Fil d'actualité</h2>";
        if (empty($touites)) {
            $html .= "<p>Aucun touite pour le moment.</p>";
            return $html;
        }

        foreach ($touites as $t) {
            $html .= "<div style=\"border: 1px solid #ccc; margin: 10px 0; padding: 15px;\">";
            
            // Auteur + Bouton pour s'abonner à cet utilisateur
            $html .= "<p>Par <a href=\"?action=user-touites&amp;id={$t['id_uti']}\"><b>{$t['prenom']} {$t['nom']}</b></a>";
            if (isset($_SESSION['statut']['id_uti']) && $_SESSION['statut']['id_uti'] != $t['id_uti']) {
                $html .= " <a href=\"?action=subscribe&id_suivi={$t['id_uti']}\" style=\"font-size: small; background: #eee; padding: 2px 6px; text-decoration: none; border-radius: 3px;\">+ S'abonner</a>";
            }
            $html .= " le {$t['date_public']}</p>";

            // Texte court du touite avec les hashtags cliquables.
            $texteFormate = TouiteMessageRender::render($t['texte'], true, $t['image_path'] ?? null);
            $html .= "<div>{$texteFormate}</div>";

            // Tags associés (si gérés via un tableau séparé)
            if (!empty($t['tags'])) {
                $html .= "<p style=\"margin-top: 5px;\">";
                foreach ($t['tags'] as $tag) {
                    $html .= "<a href=\"?action=tag-touites&id_tag={$tag['id_tag']}\" style=\"text-decoration: none; background: #eef; padding: 2px 5px; margin-right: 5px; border-radius: 3px;\">";
                    $html .= "#{$tag['libelle']}";
                    $html .= "</a>";
                }
                $html .= "</p>";
            }

            // Fermeture propre du bloc de chaque touite
            $html .= "</div>"; 
        }

        return $html;
    }
}