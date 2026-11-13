<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class UserTouitesAction extends Action {
    public function execute(): string {
        $id_uti = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id_uti) {
            return "<p>Utilisateur invalide.</p>";
        }

        $repo = TouiteurRepository::getInstance();
        $touites = $repo->getTouitesByUser($id_uti);

        $html = "<h2>Touites de l'utilisateur</h2>";
        foreach ($touites as $t) {
            $html .= "<div style=\"border: 1px solid #ccc; margin: 10px; padding: 10px;\">";
            $html .= "<p>Le {$t['date_public']}</p>";
            $html .= "<p>{$t['texte']}</p>";
            $html .= "<a href=\"?action=touite-detail&id={$t['id_touite']}\">Détails</a>";
            $html .= "</div>";
        }
        return $html;
    }
}