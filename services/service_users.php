<?php
header('Content-Type: application/json');
echo json_encode([
    ['id' => 1, 'name' => 'Alice'],
    ['id' => 2, 'name' => 'Bob']
]);
