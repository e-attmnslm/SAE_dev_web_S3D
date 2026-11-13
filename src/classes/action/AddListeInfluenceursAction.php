<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class AddListeInfluenceursAction extends Action
{
    public function execute(): string
    {
        $html = "<h2>Liste des influenceurs</h2>";
        $html .= "<p>Utilisateurs classés par ordre croissant du nombre d'abonnés :</p>";

        $influenceurs = TouiteurRepository::getInstance()->getListeInfluenceurs();

        if (empty($influenceurs)) {
            $html .= "<p>Aucun utilisateur trouvé.</p>";
            return $html;
        }

        $html .= "<ul>";

        foreach ($influenceurs as $influenceur) {
            $nom = htmlspecialchars($influenceur['nom']);
            $prenom = htmlspecialchars($influenceur['prenom']);
            $nbSuiveurs = $influenceur['nb_suiveurs'];

            $html .= "<li>$prenom $nom : $nbSuiveurs abonné(s)</li>";
        }

        $html .= "</ul>";

        return $html;
    }
}