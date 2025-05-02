<?php
header('Content-Type: application/json');
echo json_encode([
    ['sku' => 'A123', 'productName' => 'Widget'],
    ['sku' => 'B456', 'productName' => 'Gadget']
]);
