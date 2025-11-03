<?php

use App\Controllers\EquipmentController;

$controller = new EquipmentController();
$equipments = $controller->indexAction();

echo "<pre>";
print_r($equipments);
echo "</pre>";
