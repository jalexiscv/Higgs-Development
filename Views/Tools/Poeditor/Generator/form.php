<?php

$f = service('forms', ['lang' => 'Development.texttophp-']);
/*
* -----------------------------------------------------------------------------
* [Requests]
* -----------------------------------------------------------------------------
*/
$r['module'] = $f->get_Value('module', pk());
$r['text'] = $f->get_Value('text');
$back = '/nexus/modules/list/' . lpk();/*
* -----------------------------------------------------------------------------
* [Fields]
* -----------------------------------------------------------------------------
*/
$f->fields['text'] = $f->get_FieldTextArea('text', ['value' => $r['text'], 'proportion' => 'col-12']);
$f->fields['cancel'] = $f->get_Cancel('cancel', ['href' => $back, 'text' => lang('App.Cancel'), 'type' => 'secondary', 'proportion' => 'col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-right']);
$f->fields['submit'] = $f->get_Submit('submit', ['value' => lang('App.Create'), 'proportion' => 'col-xl-6 col-lg-6 col-md-6 col-sm-12 col-12 padding-left']);
/*
* -----------------------------------------------------------------------------
* [Groups]
* -----------------------------------------------------------------------------
*/
$f->groups['g3'] = $f->get_Group(['legend' => '', 'fields' => ($f->fields['text'])]);
/*
* -----------------------------------------------------------------------------
* [Buttons]
* -----------------------------------------------------------------------------
*/
$f->groups['gy'] = $f->get_GroupSeparator();
$f->groups['gz'] = $f->get_Buttons(['fields' => $f->fields['submit'] . $f->fields['cancel']]);
//[card]---------------------------------------------------------------------------------------------------------------
$bootstrap = service('bootstrap');
$card = $bootstrap->get_Card('card-view-service', [
    'title' => lang('Modules.modules-generator-title'),
    'header-back' => '',
    'content' => $f,
]);
echo($card);
