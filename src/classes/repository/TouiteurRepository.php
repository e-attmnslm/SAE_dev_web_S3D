<?php
declare(strict_types=1);
namespace iutnc\touiteur\repository;

use \iutnc\touiteur\Touite\Touite;
class TouiteurRepository
{
    private \PDO $pdo;
    private static ?TouiteurRepository $instance = null;
    private static array $config = [];

private function __construct(array $conf) {
        $dsn = "{$conf['driver']}:host={$conf['host']};dbname={$conf['database']};charset=utf8mb4";
        try {
            $this->pdo = new \PDO($dsn, $conf['user'], $conf['pass'], [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (\PDOException $e) {
            // On stoppe tout en lançant une exception si la connexion échoue
            throw new \Exception("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    public static function getInstance(): TouiteurRepository {
        if (is_null(self::$instance)) {
            self::$instance = new TouiteurRepository(self::$config);
        }
        return self::$instance;
    }

    public static function setConfig(string $file): void {
        $conf = parse_ini_file($file);
        if ($conf === false) {
            throw new \Exception("Error reading configuration file");
        }
        self::$config = [
            'driver'   => $conf['driver'],
            'user'     => $conf['username'],
            'pass'     => $conf['password'],
            'host'     => $conf['host'],
            'database' => $conf['database']
        ];
    }


    /**
     * Enregistre un nouvel utilisateur dans la base de données
     */
    public function saveUtilisateur(string $nom, string $prenom, string $mail): int {
        $query = "INSERT INTO utilisateur (nom, prenom, mail, nbtouite) 
                  VALUES (:nom, :prenom, :mail, 0)";
        
        $stmt = $this->pdo->prepare($query);
        
        $stmt->execute([
            'nom'    => $nom,
            'prenom' => $prenom,
            'mail'   => $mail
        ]);

        // Retourne l'ID généré automatiquement par la base de données
        return (int) $this->pdo->lastInsertId();
    }
}