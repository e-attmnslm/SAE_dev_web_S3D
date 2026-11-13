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

    /**
     * Récupère tous les touites par ordre chronologique inverse (Fonctionnalité 1)
     */
    public function getAllTouites(): array {
    // 1. Récupérer tous les touites avec l'auteur et l'image
    $query = "SELECT t.*, u.nom, u.prenom, i.chemin as image_path 
              FROM touite t 
              JOIN utilisateur u ON t.id_uti = u.id_uti 
              LEFT JOIN image i ON t.id_img = i.id_img 
              ORDER BY t.date_public DESC";
              
    $stmt = $this->pdo->query($query);
    $touites = $stmt->fetchAll(\PDO::FETCH_ASSOC);

    // 2. Pour chaque touite, aller chercher ses tags associés
    foreach ($touites as &$t) {
        $queryTag = "SELECT tags.id_tag, tags.libelle 
                     FROM AppartientTag at 
                     JOIN tags ON at.id_tag = tags.id_tag 
                     WHERE at.id_touite = :idTouite";
                     
        $stmtTag = $this->pdo->prepare($queryTag);
        $stmtTag->execute(['idTouite' => $t['id_touite']]);
        $t['tags'] = $stmtTag->fetchAll(\PDO::FETCH_ASSOC);
    }
    unset($t); // Rompre la référence

    return $touites;
}
    
    /**
     * Récupère un touite par son ID (Fonctionnalité 2)
     */
    public function getTouiteById(int $id): ?array {
        $query = "SELECT t.*, u.nom, u.prenom, i.chemin 
                  FROM touite t 
                  JOIN utilisateur u ON t.id_uti = u.id_uti 
                  LEFT JOIN image i ON t.id_img = i.id_img 
                  WHERE t.id_touite = :id";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id' => $id]);
        $res = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $res ?: null;
    }

    /**
     * Récupère les touites d'un utilisateur spécifique (Fonctionnalité 4)
     */
    public function getTouitesByUser(int $id_uti): array {
        $query = "SELECT t.*, u.nom, u.prenom, i.chemin 
                  FROM touite t 
                  JOIN utilisateur u ON t.id_uti = u.id_uti 
                  LEFT JOIN image i ON t.id_img = i.id_img 
                  WHERE t.id_uti = :id_uti 
                  ORDER BY t.date_public DESC";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['id_uti' => $id_uti]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les touites associés à un tag (Fonctionnalité 5)
     */
    public function getTouitesByTag(int $idTag): array {
    $query = "SELECT t.*, u.nom, u.prenom, i.chemin as image_path 
              FROM touite t 
              JOIN utilisateur u ON t.id_uti = u.id_uti 
              LEFT JOIN image i ON t.id_img = i.id_img 
              JOIN AppartientTag at ON t.id_touite = at.id_touite 
              WHERE at.id_tag = :idTag 
              ORDER BY t.date_public DESC";
              
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['idTag' => $idTag]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
    /**
     * Récupère un utilisateur par son adresse mail
     */
    public function getUtilisateurByEmail(string $email): ?array {
        $query = "SELECT * FROM utilisateur WHERE mail = :mail";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['mail' => $email]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    
   public function saveTouite(string $texte, int $idUti, ?int $idImg = null): int {
    $query = "INSERT INTO touite (texte, date_public, id_uti, id_img) VALUES (:texte, NOW(), :id_uti, :id_img)";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
        'texte' => $texte,
        'id_uti' => $idUti,
        'id_img' => $idImg
    ]);
    return (int) $this->pdo->lastInsertId();
}
    public function saveImage(string $chemin): int {
    $query = "INSERT INTO image (chemin) VALUES (:chemin)"; // Adapte le nom de la colonne si c'est 'url' ou 'path'
    $stmt = $this->pdo->prepare($query);
    $stmt->execute(['chemin' => $chemin]);
    return (int) $this->pdo->lastInsertId();
}

    /**
     * Récupère ou crée un tag en base et retourne son ID
     */
    public function getOrCreateTag(string $libelle): int {
        // Vérifie si le tag existe déjà
        $query = "SELECT id_tag FROM tags WHERE libelle = :libelle";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['libelle' => $libelle]);
        $tag = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($tag) {
            return (int) $tag['id_tag'];
        }

        // Sinon, on l'insère
        $insert = "INSERT INTO tags (libelle) VALUES (:libelle)";
        $stmtInsert = $this->pdo->prepare($insert);
        $stmtInsert->execute(['libelle' => $libelle]);
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Associe un tag à un touite dans la table de liaison
     */
    public function linkTouiteTag(int $idTouite, int $idTag): void {
        $query = "INSERT INTO AppartientTag (id_touite, id_tag) VALUES (:id_touite, :id_tag)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute([
            'id_touite' => $idTouite,
            'id_tag' => $idTag
        ]);
    }

    // --- ABONNEMENTS UTILISATEURS ---
    public function suivreUtilisateur(int $idSuiveur, int $idSuivi): void {
    $query = "INSERT INTO Suivre (uti_suiveur, uti_suivi) VALUES (:suiveur, :suivi)";
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
        'suiveur' => $idSuiveur, 
        'suivi' => $idSuivi
    ]);
}

    // --- ABONNEMENTS TAGS ---
    public function suivreTag(int $idUti, int $idTag): void {
        $query = "INSERT INTO AbonneTag (id_uti, id_tag) VALUES (:uti, :tag)";
        $stmt = $this->pdo->prepare($query);
        $stmt->execute(['uti' => $idUti, 'tag' => $idTag]);
    }

    // --- LE MUR PERSONNALISÉ ---
    public function getMurUtilisateur(int $idUti): array {
    $query = "SELECT DISTINCT t.*, u.nom, u.prenom, i.chemin as image_path 
              FROM touite t 
              JOIN utilisateur u ON t.id_uti = u.id_uti 
              LEFT JOIN image i ON t.id_img = i.id_img
              LEFT JOIN AppartientTag tt ON t.id_touite = tt.id_touite
              WHERE t.id_uti IN (SELECT uti_suivi FROM Suivre WHERE uti_suiveur = :idUti1)
                 OR tt.id_tag IN (SELECT id_tag FROM AbonneTag WHERE id_uti = :idUti2)
              ORDER BY t.date_public DESC";
              
    $stmt = $this->pdo->prepare($query);
    $stmt->execute([
        'idUti1' => $idUti,
        'idUti2' => $idUti
    ]);
    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
}
   
}