<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class DefaultAction extends Action {

    public function execute() : string {
        $touites = (new TouiteurRepository())->all();
        $html = '<h2>Les derniers touites</h2>';
        $selectedId = null;

        if ($touites === []) {
            return $html . '<p>Aucun touite pour le moment.</p>';
        }

        if (array_key_exists('id', $_GET)) {
            $selectedId = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            if ($selectedId === false || $selectedId < 0 || !isset($touites[$selectedId])) {
                $html .= '<p>Ce touite est introuvable.</p>';
                $selectedId = null;
            }
        }

        foreach (array_reverse($touites, true) as $id => $touite) {
            if (!is_array($touite) || !isset($touite['content'])) {
                continue;
            }

            $author = htmlspecialchars((string) ($touite['author'] ?? 'Utilisateur'), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $fullContent = (string) $touite['content'];
            $isExpanded = $selectedId === $id;
            $previewLength = (int) ceil(mb_strlen($fullContent) / 3);
            $displayContent = !$isExpanded && mb_strlen($fullContent) > $previewLength
                ? mb_substr($fullContent, 0, $previewLength) . '…'
                : $fullContent;
            $content = nl2br(htmlspecialchars($displayContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

            if ($isExpanded) {
                $html .= '<article style="width:100%;max-width:300px;box-sizing:border-box;"><h3>' . $author . '</h3><p>' . $content . '</p>';
                if (!empty($touite['image'])) {
                    $image = htmlspecialchars((string) $touite['image'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                    $html .= '<img src="' . $image . '" alt="Image du touite" style="display:block;width:100%;max-height:400px;height:auto;object-fit:contain;">';
                }
            } else {
                $html .= '<a href="?action=default&amp;id=' . (int) $id . '" style="display:block;color:inherit;text-decoration:none;">'
                    . '<article style="width:100%;max-width:500px;box-sizing:border-box;"><h3>' . $author . '</h3><p>' . $content . '</p>';
            }

            if (!empty($touite['created_at'])) {
                $createdAt = (string) $touite['created_at'];
                $date = new \DateTimeImmutable($createdAt);
                $abbreviatedMonths = [
                    1 => 'janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin',
                    'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.',
                ];
                $displayDate = $date->format('j') . ' '
                    . $abbreviatedMonths[(int) $date->format('n')] . ' '
                    . $date->format('Y H:i');
                $html .= '<p><time datetime="' . htmlspecialchars($createdAt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">'
                    . htmlspecialchars($displayDate, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</time></p>';
            }

            $html .= '</article>';
            $html .= $isExpanded
                ? '<p><a href="?action=default">Réduire</a></p>'
                : '</a>';
        }

        return $html;
    }
    
}

?>