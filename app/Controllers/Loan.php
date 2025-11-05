<?php
namespace App\Controllers;

use Exception;
use App\Controller;

use App\Models\Loan;
use App\Models\Equipment;


class LoanController extends Controller {

    public function indexAction(): array {
        return Loan::all();
    }

    public function showAction(int $id): ?Loan {
        return Loan::find($id);
    }

    public function storeAction(array $data): bool {

        $equipment = Equipment::find($data['equipment_id']);
        
        if (!$equipment || $equipment->state !== 'Disponible') {
            throw new Exception("Le matériel sélectionné n'est plus disponible pour le prêt.");
        }
        
        $loan = new Loan();

        $loan->equipment_id = $data['equipment_id'];
        $loan->grantee = $data['grantee'];
        $loan->grantee_type = $data['grantee_type'];
        $loan->loaned_at = $data['loaned_at'];
        $loan->returned_at = $data['returned_at'] ?? null;
        $loan->return_state = $data['return_state'] ?? 'Bon';
        $loan->comment = $data['comment'] ?? null;

        $success = $loan->save();

        // Mettre à jour l’état du matériel si le prêt est créé
        if ($success) {
            $equipment = Equipment::find($loan->equipment_id);
            if ($equipment) {
                $equipment->state = 'En prêt';
                $equipment->save();
            }
        }

        return $success;
    }

    public function updateAction(int $id, array $data): bool {
        $loan = Loan::find($id);
        if (!$loan) return false;

        foreach ($data as $key => $value) {
            if (property_exists($loan, $key)) {
                $loan->$key = $value;
            }
        }

        $success = $loan->save();

        // Si retour enregistré, on libère le matériel
        if ($success && !empty($loan->returned_at)) {
            $equipment = Equipment::find($loan->equipment_id);
            if ($equipment) {
                $equipment->state = 'Disponible';
                $equipment->save();
            }
        }

        return $success;
    }

    public function destroyAction(int $id): bool {
        return Loan::delete($id);
    }

    public function createAction() {
        $availableEquipments = Equipment::where('state', 'available');
        
        $this->render('loan/create', [
            'equipments' => $availableEquipments
        ]);
    }

}
