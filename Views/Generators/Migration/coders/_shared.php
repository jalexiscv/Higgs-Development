<?php

$eid = explode('_', $oid);
if (count($eid) < 2) {
    throw new \InvalidArgumentException("OID must be module_component format: {$oid}");
}

$g = (object)[
    'ucf_module'    => safe_ucfirst($eid[0]),
    'ucf_component' => safe_ucfirst($eid[1]),
    'ucf_options'   => safe_ucfirst($eid[2] ?? ''),
    'slc_module'    => safe_strtolower($eid[0]),
    'slc_component' => safe_strtolower($eid[1]),
    'slc_options'   => safe_strtolower($eid[2] ?? ''),
    'has_options'   => count($eid) === 3,
];

$g->classname = $g->has_options
    ? "{$g->ucf_module}_{$g->ucf_component}_{$g->ucf_options}"
    : "{$g->ucf_module}_{$g->ucf_component}";
$g->timestamp = date('Y-m-d_His');
$g->namespaced = "App\\Modules\\{$g->ucf_module}\\Database\\Migrations\\{$g->timestamp}_{$g->ucf_module}_{$g->ucf_component}.php";
$g->path = APPPATH . "Modules/{$g->ucf_module}/Database/Migrations/";
$g->uri = $g->path . $g->timestamp . "_{$g->ucf_module}_{$g->ucf_component}.php";
$g->file = "{$g->timestamp}_{$g->ucf_module}_{$g->ucf_component}.php";

$db = \Config\Database::connect('default');
$g->fields = $db->getFieldNames($oid);
