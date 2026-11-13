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
        
        // (Optionnel) Récupérer le nom du tag pour l'affichage
        // $nomTag = $repo->getNomTag((int)$idTag);

        $html = "<h2>Touites pour le tag #{$idTag}</h2>";

        // Bouton pour s'abonner au tag si l'utilisateur est connecté
        if (isset($_SESSION['statut']['loggedin']) && $_SESSION['statut']['loggedin'] === true) {
            $html .= "<div style=\"margin-bottom: 20px;\">";
            $html .= "<a href=\"?action=subscribe&id_tag={$idTag}\" style=\"background: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px;\">";
            $html .= "S'abonner à ce tag";
            $html .= "</a>";
            $html .= "</div>";
        }

        // Récupération et affichage des touites du tag...
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