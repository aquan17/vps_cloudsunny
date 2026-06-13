<?php
$ids = [243];
$action = 'upgrade';
$extra = [
    'addon_cpu' => 1,
    'addon_ram' => 0,
    'addon_disk' => 0,
];

$payload = array_merge([
    'ids' => array_values($ids),
    'action' => $action,
], $extra);

echo json_encode($payload, JSON_PRETTY_PRINT);
