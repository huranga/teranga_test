<?php
namespace App\Models;

use App\Database;
use PDO;

class Equipment {
    public int $id;
    public string $name;
    public string $category;
    public string $state;
    public string $serial_number;
    public ?string $purchase_date;
    public ?string $localisation;
    public string $comments;

    public static function all(): array {
        $stmt = Database::getInstance()->query("SELECT * FROM equipment ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id): ?Equipment {
        $stmt = Database::getInstance()->prepare("SELECT * FROM equipment WHERE id = ?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public function save(): bool {
        $pdo = Database::getInstance();
        if (isset($this->id)) {
            $sql = "UPDATE equipment SET name=?, category=?, state=?, serial_number=?, purchase_date=?, localisation=?, comments=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->name, $this->category, $this->state, $this->serial_number, $this->purchase_date, $this->localisation, $this->comments, $this->id]);
        } else {
            $sql = "INSERT INTO equipment (nom, categorie, etat, numero_serie, date_achat, localisation, remarques)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->name, $this->category, $this->state, $this->serial_number, $this->purchase_date, $this->localisation, $this->comments]);
        }
    }

    public static function delete(int $id): bool {
        $stmt = Database::getInstance()->prepare("DELETE FROM equipment WHERE id=?");
        return $stmt->execute([$id]);
    }
}
