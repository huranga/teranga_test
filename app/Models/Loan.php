<?php
namespace App\Models;

use App\Database;
use PDO;

class Loan {
    public int $id;
    public int $equipment_id;
    public string $grantee;
    public string $grantee_type;
    public ?string $loaned_at;
    public ?string $return_at;
    public ?string $returned_at;
    public string $return_state;
    public string $comment;

    public static function all(): array {
        $stmt = Database::getInstance()->query("SELECT * FROM loan ORDER BY id DESC");
        return $stmt->fetchAll(PDO::FETCH_CLASS, self::class);
    }

    public static function find(int $id): ?Loan {
        $stmt = Database::getInstance()->prepare("SELECT * FROM loan WHERE id=?");
        $stmt->execute([$id]);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::class);
        return $stmt->fetch() ?: null;
    }

    public function save(): bool {
        $pdo = Database::getInstance();
        if (isset($this->id)) {
            $sql = "UPDATE loan SET equipment_id=?, grantee=?, grantee_type=?, loaned_at=?, return_at=?, returned_at=?, return_state=?, comment=? WHERE id=?";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->equipment_id, $this->grantee, $this->grantee_type, $this->loaned_at, $this->return_at, $this->returned_at, $this->return_state, $this->comment, $this->id]);
        } else {
            $sql = "INSERT INTO loan (equipment_id, grantee, grantee_type, loaned_at, return_at, returned_at, return_state, comment)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            return $stmt->execute([$this->equipment_id, $this->grantee, $this->grantee_type, $this->loaned_at, $this->return_at, $this->returned_at, $this->return_state, $this->comment]);
        }
    }

    public static function delete(int $id): bool {
        $stmt = Database::getInstance()->prepare("DELETE FROM loan WHERE id=?");
        return $stmt->execute([$id]);
    }


    public static function countActiveByEquipment(int $equipmentId): int {
        $stmt = Database::getInstance()->prepare("
            SELECT COUNT(*) FROM loan 
            WHERE equipment_id = ? AND returned_at IS NULL
        ");
        $stmt->execute([$equipmentId]);
        return (int) $stmt->fetchColumn();
    }

    public static function getActiveWithEquipmentNames(): array {
        $sql = "
            SELECT l.*, e.name AS equipment_name
            FROM loan l
            JOIN equipment e ON e.id = l.equipment_id
            WHERE l.returned_at IS NULL
            ORDER BY l.loaned_at DESC
        ";
        $stmt = Database::getInstance()->query($sql);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
