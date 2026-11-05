<?php
declare(strict_types=1);

namespace iutnc\touiteur\action;

class AddSignOutAction extends Action {

    public function execute() : string {
        $html = '<b>SE CONNECTER</b> <br>';
        if ($this->http_method === 'GET') {

            // DEBUG
            var_dump($_SESSION);


            // AFFICHER LE FORMULAIRE
            $html .= <<<FIN
                <form method="post" action="?action=signout" enctype="multipart/form-data">
                    <p>Etes-vous sûr de vous déconnecter ?</p>
                    <br><button type="submit">Se déconnecter</button>
                </form>
            FIN;
        } else {

            if (isset($_SESSION['statut'])) {
                if ($_SESSION['statut'][0] === true) {
                    unset($_SESSION['statut']);
                    $_SESSION['statut'][0] = false;
                    $html .= "Déconnecté avec succès";
                }
            }

        }
        return $html;
    }
    
}

?>