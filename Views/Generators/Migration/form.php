<?php

use Higgs\Frontend\Bootstrap\v5_3_3\Bootstrap as BS5;

$f = service('forms', ['lang' => 'Generators.']);

$eid = explode('_', $oid);
$ucf_module = safe_ucfirst($eid[0]);
$ucf_component = safe_ucfirst($eid[1]);

$timestamp = date('Y-m-d_His');
$path = APPPATH . "Modules/{$ucf_module}/Database/Migrations/";
$file = "{$timestamp}_{$ucf_module}_{$ucf_component}.php";
$uri = $path . $file;

$data = $parent->get_Array();
$data['path'] = $file;
$code = view($component . '\coders\migration', $data);

$r['uri'] = $f->get_Value('uri', $uri);
$r['code'] = $f->get_Value('code', $code);

$f->add_HiddenField('file', $file);
$f->add_HiddenField('path', $path);
$f->fields['uri'] = $f->get_FieldText('uri', ['value' => $r['uri'], 'readonly' => true]);
$f->fields['code'] = $f->get_FieldCode('code', ['value' => $r['code'], 'mode' => 'php']);
$f->fields['cancel'] = $f->get_Cancel('cancel', ['href' => '/nexus/generators/', 'text' => lang('App.Cancel'), 'type' => 'secondary', 'proportion' => 'col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right']);
$f->fields['submit'] = $f->get_Submit('submit', ['value' => 'Guardar', 'proportion' => 'col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left']);

$f->groups['g1'] = $f->get_Group(['legend' => '', 'fields' => ($f->fields['uri'])]);
$f->groups['g2'] = $f->get_Group(['legend' => '', 'fields' => ($f->fields['code'])]);

$f->groups['gy'] = $f->get_GroupSeparator();
$f->groups['gz'] = $f->get_Buttons(['fields' => $f->fields['submit'] . $f->fields['cancel']]);

$card = BS5::card([
    'headerTitle' => lang('Generators.migrations-generator-title'),
    'content' => ['htmlContent' => $f, ],
]);

echo($card);
