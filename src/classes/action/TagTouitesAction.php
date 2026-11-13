<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class TagTouitesAction extends Action {
    public function execute() : string {
        $idTag = $_GET['id_tag'] ?? null;
        if (!$idTag) {
            return "<p>Tag introuvable.</p>";
        }

        $repo = TouiteurRepository::getInstance();
        
        $html = "<h2>Touites pour le tag #{$idTag}</h2>";

        // Bouton dynamique S'abonner / Se désabonner au tag si l'utilisateur est connecté
        if (isset($_SESSION['statut']['id_uti'])) {
            $idUti = $_SESSION['statut']['id_uti'];
            $estAbonneTag = $repo->estAbonneTag($idUti, (int)$idTag);

            $html .= "<div style=\"margin-bottom: 20px;\">";
            if ($estAbonneTag) {
                $html .= "<a href=\"?action=subscribe&id_tag={$idTag}\" style=\"background: #dc3545; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;\">Se désabonner de ce tag</a>";
            } else {
                $html .= "<a href=\"?action=subscribe&id_tag={$idTag}\" style=\"background: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;\">S'abonner à ce tag</a>";
            }
            $html .= "</a>";
            $html .= "</div>";
        }

        // Récupération et affichage des touites du tag
        $touites = $repo->getTouitesByTag((int)$idTag);
        foreach ($touites as $t) {
            $html .= "<div style=\"border: 1px solid #ccc; margin: 10px; padding: 10px;\">";
            $html .= "<p>Par <b>{$t['prenom']} {$t['nom']}</b> le {$t['date_public']}</p>";
            $html .= "<p>{$t['texte']}</p>";
            $html .= "</div>";
        }

        return $html;
    }
}