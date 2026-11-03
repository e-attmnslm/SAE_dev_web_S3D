<?php
// filepath: /home/jonathan/webs3/code/SAE_dev_web_S3D/src/classes/repository/TouiteRepository.php

declare(strict_types=1);

namespace iutnc\touiteur\repository;

class TouiteRepository
{
    private string $file;

    public function __construct()
    {
        $this->file = dirname(__DIR__, 3) . '/data/touites.json';
    }

    public function all(): array
    {
        if (!is_file($this->file)) {
            return [];
        }

        $handle = fopen($this->file, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('Impossible de lire les touites.');
        }

        try {
            flock($handle, LOCK_SH);
            $content = stream_get_contents($handle);
            $touites = json_decode($content ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            return is_array($touites) ? $touites : [];
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function add(string $content): void
    {
        $content = trim($content);
        if ($content === '') {
            throw new \InvalidArgumentException('Le touite ne peut pas être vide.');
        }

        $directory = dirname($this->file);
        if (!is_dir($directory) && !mkdir($directory, 0775, true) && !is_dir($directory)) {
            throw new \RuntimeException('Impossible de créer le dossier de stockage.');
        }

        $handle = fopen($this->file, 'c+');
        if ($handle === false) {
            throw new \RuntimeException('Impossible d’enregistrer le touite.');
        }

        try {
            flock($handle, LOCK_EX);
            rewind($handle);
            $existing = stream_get_contents($handle);
            $touites = json_decode($existing ?: '[]', true, 512, JSON_THROW_ON_ERROR);

            $touites[] = [
                'content' => $content,
                'created_at' => date(DATE_ATOM),
            ];

            rewind($handle);
            ftruncate($handle, 0);
            fwrite($handle, json_encode($touites, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}

// ...dans execute(), après le traitement du formulaire...
$touite = trim($_POST['touite'] ?? '');
(new TouiteRepository())->add($touite);

// ...dans execute()...
$touites = (new TouiteRepository())->all();
$html = '';

foreach (array_reverse($touites) as $touite) {
    $content = htmlspecialchars($touite['content'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    $html .= "<article><p>{$content}</p></article>";
}

return $html;
