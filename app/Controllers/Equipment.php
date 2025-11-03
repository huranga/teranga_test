<?php
namespace App\Controllers;

use App\Models\Equipment;

class EquipmentController {

    public function indexAction(): array {
        return Equipment::all();
    }

    public function showAction(int $id): ?Equipment {
        return Equipment::find($id);
    }

    public function storeAction(array $data): bool {
        $equipment = new Equipment();
        
        $equipment->name = $data['name'];
        
        $equipment->category = $data['category'] ?? null;
        $equipment->state = $data['state'] ?? 'Disponible';
        
        $equipment->serial_number = $data['serial_number'] ?? null;
        $equipment->purchase_date = $data['purchase_date'] ?? null;
        
        $equipment->localisation = $data['localisation'] ?? null;
        $equipment->comments = $data['comments'] ?? null;
        
        return $equipment->save();
    }

    public function updateAction(int $id, array $data): bool {
        $equipment = Equipment::find($id);
        if (!$equipment) return false;

        foreach ($data as $key => $value) {
            if (property_exists($equipment, $key)) {
                $equipment->$key = $value;
            }
        }
        return $equipment->save();
    }

    public function destroyAction(int $id): bool {
        return Equipment::delete($id);
    }
}
