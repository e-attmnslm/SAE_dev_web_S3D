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

        // Si on clique pour un utilisateur
        if (isset($_GET['id_suivi'])) {
            $idSuivi = (int) $_GET['id_suivi'];
            if ($idSuivi !== $idUti) {
                if ($repo->estAbonneUti($idUti, $idSuivi)) {
                    $repo->seDesabonnerUti($idUti, $idSuivi);
                } else {
                    $repo->suivreUtilisateur($idUti, $idSuivi);
                }
            }
        }

        // Si on clique pour un tag
        if (isset($_GET['id_tag'])) {
            $idTag = (int) $_GET['id_tag'];
            if ($repo->estAbonneTag($idUti, $idTag)) {
                $repo->seDesabonnerTag($idUti, $idTag);
            } else {
                $repo->suivreTag($idUti, $idTag);
            }
        }

        $redirect = $_SERVER['HTTP_REFERER'] ?? '?action=mur';
        header("Location: $redirect");
        exit();
    }
}