<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;
use iutnc\touiteur\render\TouiteMessageRender;

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
            $html .= "<p>Par <a href=\"?action=user-touites&amp;id={$t['id_uti']}\"><b>{$t['prenom']} {$t['nom']}</b></a> le {$t['date_public']}</p>";
            $html .= '<div>' . TouiteMessageRender::render($t['texte'], true) . '</div>';
            $html .= "</div>";
        }

        return $html;
    }
}