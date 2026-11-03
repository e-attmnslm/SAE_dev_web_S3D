<?php

declare(strict_types=1);

namespace iutnc\touiteur\repository;

use iutnc\touiteur\exception\DatabaseException;
use iutnc\touiteur\model\Touite;

class TouiteurRepository
{
    private \PDO $pdo;
    private static ?TouiteurRepository $instance = null;
    private static array $config = [];

    private function __construct(array $conf)
    {
        try {
            $this->pdo = new \PDO($conf['dsn'], $conf['user'], $conf['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            ]);
        } catch (\PDOException) {
            // on ne remonte pas le message PDO : il peut contenir le mot de passe
            throw new DatabaseException('Connexion à la base de données impossible');
        }
    }

    public static function setConfig(string $file): void
    {
        $conf = @parse_ini_file($file);
        if ($conf === false) {
            throw new DatabaseException('Fichier de configuration illisible');
        }
        self::$config = [
            'dsn'  => "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4",
            'user' => $conf['username'],
            'pass' => $conf['password'],
        ];
        self::$instance = null;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self(self::$config);
        }
        return self::$instance;
    }

    /**
     * @return Touite[] du plus récent au plus ancien
     */
    public function findAllTouites(): array
    {
        $st = $this->pdo->prepare(
            'SELECT t.id, t.texte, t.created_at, t.image, t.user_id, u.username
             FROM touite t
             JOIN user u ON u.id = t.user_id
             ORDER BY t.created_at DESC, t.id DESC'
        );
        $st->execute();

        $touites = [];
        foreach ($st->fetchAll() as $row) {
            $touites[] = new Touite(
                (int)$row['id'],
                $row['texte'],
                (int)$row['user_id'],
                $row['username'],
                new \DateTimeImmutable($row['created_at']),
                0,                      // score : calculé plus tard avec la table des notes
                $row['image']           // null si pas d'image
            );
        }
        return $touites;
    }
}