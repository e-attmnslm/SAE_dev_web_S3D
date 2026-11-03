<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class DefaultAction extends Action {

    public function execute() : string {
        $touites = (new TouiteurRepository())->all();
        $html = '<h2>Les derniers touites</h2>';

        if ($touites === []) {
            return $html . '<p>Aucun touite pour le moment.</p>';
        }

        foreach (array_reverse($touites) as $touite) {
            if (!is_array($touite) || !isset($touite['content'])) {
                continue;
            }

            $author = htmlspecialchars((string) ($touite['author'] ?? 'Utilisateur'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $content = nl2br(htmlspecialchars((string) $touite['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
            $html .= '<article><h3>' . $author . '</h3><p>' . $content . '</p>';

            if (!empty($touite['image'])) {
                $image = htmlspecialchars((string) $touite['image'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html .= '<img src="' . $image . '" alt="Image du touite" style="max-height:200px;max-width:200px;">';
            }

            if (!empty($touite['created_at'])) {
                $createdAt = htmlspecialchars((string) $touite['created_at'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html .= '<p><time datetime="' . $createdAt . '">' . $createdAt . '</time></p>';
            }

            $html .= '</article>';
        }

        return $html;
    }
    
}

?>