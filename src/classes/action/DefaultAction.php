<?php

namespace iutnc\touiteur\action;

use iutnc\touiteur\repository\TouiteurRepository;

class DefaultAction extends Action {

    public function execute() : string {
        $touites = (new TouiteurRepository())->all();
        $selectedAuthor = isset($_GET['author']) && is_string($_GET['author'])
            ? trim($_GET['author'])
            : null;
        if ($selectedAuthor === '') {
            $selectedAuthor = null;
        }

        if ($selectedAuthor !== null) {
            $touites = array_filter($touites, static fn ($touite): bool =>
                is_array($touite)
                && (string) ($touite['author'] ?? 'Utilisateur') === $selectedAuthor
            );
            $escapedAuthor = htmlspecialchars($selectedAuthor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $html = '<h2>Les touites de ' . $escapedAuthor . '</h2>'
                . '<p><a href="?action=default">Tous les touites</a></p>';
        } else {
            $html = '<h2>Les derniers touites</h2>';
        }
        $selectedId = null;

        if ($touites === []) {
            $message = $selectedAuthor === null
                ? 'Aucun touite pour le moment.'
                : 'Cet utilisateur n’a publié aucun touite.';
            return $html . '<p>' . $message . '</p>';
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
            $authorQuery = rawurlencode((string) ($touite['author'] ?? 'Utilisateur'));
            $profileHref = '?action=default&amp;author=' . $authorQuery;
            $fullContent = (string) $touite['content'];
            $isExpanded = $selectedId === $id;
            $previewLength = (int) ceil(mb_strlen($fullContent) / 3);
            $displayContent = !$isExpanded && mb_strlen($fullContent) > $previewLength
                ? mb_substr($fullContent, 0, $previewLength) . '…'
                : $fullContent;
            $content = nl2br(htmlspecialchars($displayContent, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));

            $html .= '<article style="width:100%;max-width:' . ($isExpanded ? '300px' : '500px') . ';box-sizing:border-box;">'
                . '<h3><a href="' . $profileHref . '">' . $author . '</a></h3>';
            if (!$isExpanded) {
                $expandHref = '?action=default';
                if ($selectedAuthor !== null) {
                    $expandHref .= '&amp;author=' . rawurlencode($selectedAuthor);
                }
                $html .= '<a href="' . $expandHref . '&amp;id=' . (int) $id . '" style="display:block;color:inherit;text-decoration:none;">';
            }

            $html .= '<p>' . $content . '</p>';
            if ($isExpanded && !empty($touite['image'])) {
                $image = htmlspecialchars((string) $touite['image'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $html .= '<img src="' . $image . '" alt="Image du touite" style="display:block;width:100%;max-height:400px;height:auto;object-fit:contain;">';
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

            $html .= $isExpanded ? '</article>' : '</a></article>';
            if ($isExpanded) {
                $reduceHref = '?action=default';
                if ($selectedAuthor !== null) {
                    $reduceHref .= '&amp;author=' . rawurlencode($selectedAuthor);
                }
                $html .= '<p><a href="' . $reduceHref . '">Réduire</a></p>';
            }
        }

        return $html;
    }
    
}

?>