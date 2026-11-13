<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class SuivreUtiAction extends Action {
    public function execute() : string {
        if (!isset($_SESSION['statut']['loggedin']) || $_SESSION['statut']['loggedin'] === false) {
            return "<p>Vous devez être connecté pour vous abonner.</p>";
        }

        $idUti = $_SESSION['statut']['id_uti'];
        $repo = TouiteurRepository::getInstance();

        // Si on veut suivre un utilisateur
        if (isset($_GET['id_suivi'])) {
            $idSuivi = (int) $_GET['id_suivi'];
            if ($idSuivi !== $idUti) { // Empêcher de se suivre soi-même
                $repo->suivreUtilisateur($idUti, $idSuivi);
            }
        }

        // Si on veut suivre un tag
        if (isset($_GET['id_tag'])) {
            $idTag = (int) $_GET['id_tag'];
            $repo->suivreTag($idUti, $idTag);
        }
        // Si on veut suivre un tag
        if (isset($_GET['id_tag'])) {
            $idTag = (int) $_GET['id_tag'];
            $repo->suivreTag($idUti, $idTag);
        }

        // Redirection vers le mur ou la page précédente
        header('Location: ?action=mur');
        exit();
    }
}