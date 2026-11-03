<?php

declare(strict_types=1);

namespace iutnc\touiteur\repository;

class TouiteurRepository
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
            if (!flock($handle, LOCK_SH)) {
                throw new \RuntimeException('Impossible de verrouiller les touites.');
            }
            $content = stream_get_contents($handle);
            $touites = json_decode($content ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($touites)) {
                throw new \UnexpectedValueException('Le fichier des touites est invalide.');
            }
            return $touites;
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    public function add(string $author, string $content, ?string $image = null): void
    {
        $author = trim($author);
        $content = trim($content);
        if ($author === '' || $content === '') {
            throw new \InvalidArgumentException('Le nom d’utilisateur et le touite sont obligatoires.');
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
            if (!flock($handle, LOCK_EX)) {
                throw new \RuntimeException('Impossible de verrouiller les touites.');
            }

            rewind($handle);
            $existing = stream_get_contents($handle);
            $touites = json_decode($existing ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($touites)) {
                throw new \UnexpectedValueException('Le fichier des touites est invalide.');
            }

            $touites[] = [
                'author' => $author,
                'content' => $content,
                'image' => $image,
                'created_at' => date(DATE_ATOM),
            ];

            $json = json_encode($touites, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT);
            rewind($handle);
            if (!ftruncate($handle, 0) || fwrite($handle, $json) === false || !fflush($handle)) {
                throw new \RuntimeException('Impossible d’enregistrer le touite.');
            }
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }
}
