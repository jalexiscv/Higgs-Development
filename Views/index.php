<?php
/** @var string $parent */
/** @var string $views */
/** @var string $prefix */
/** @var object $parent */
$server = service('server');
$benchmark = service('timer');
$benchmark->start('time');
$version = round(($server->get_DirectorySize(APPPATH . 'Modules/Security') / 102400), 6);
$data = $parent->get_Array();
$rviews = array(
    "default" => "{$views}\E404\index",
    "development-denied" => "{$views}\Denied\index",
    "development-home" => "{$views}\Home\index",
    "development-tools-home" => "{$views}\Tools\Home\index",
    "development-tools-texttophp-generator" => "{$views}\Tools\Texttophp\Generator\index",
    "development-tools-modules-generator" => "{$views}\Tools\Modules\Generator\index",
    "development-tools-poeditor-generator" => "{$views}\Tools\Poeditor\Generator\index",
    //[development-generators]------------------------------------------------------------------------------------------
    "development-generators-list" => "{$views}\Generators\List\index",
    "development-generators-model" => "{$views}\Generators\Model\index",
    "development-generators-controller" => "{$views}\Generators\Controller\index",
    "development-generators-lister" => "{$views}\Generators\Lister\index",
    "development-generators-viewer" => "{$views}\Generators\Viewer\index",
    "development-generators-editor" => "{$views}\Generators\Editor\index",
    "development-generators-creator" => "{$views}\Generators\Creator\index",
    "development-generators-deleter" => "{$views}\Generators\Deleter\index",
    "development-generators-lang" => "{$views}\Generators\Lang\index",
    "development-generators-migration" => "{$views}\Generators\Migration\index",
    //[ui]--------------------------------------------------------------------------------------------------------------
    "development-ui-home" => "{$views}\Ui\Home\index",
    "development-ui-buttons" => "{$views}\Ui\Buttons\index",
    "development-ui-forms" => "{$views}\Ui\Forms\index",
    "development-ui-tables" => "{$views}\Ui\Tables\index",
    "development-ui-cards" => "{$views}\Ui\Cards\index",
    "development-ui-chatbox" => "{$views}\Ui\Chatbox\index",
    "development-ui-uploaders" => "{$views}\Ui\Uploaders\index",
    //[webpack]---------------------------------------------------------------------------------------------------------
    "development-webpack-home" => "{$views}\Webpack\Home\index",
    //[ide]-------------------------------------------------------------------------------------------------------------
    "development-ide-home" => "{$views}\Ide\Home\index",
);
$uri = !isset($rviews[$prefix]) ? $rviews["default"] : $rviews[$prefix];
$json = view($uri, $data);
$mmu = model("App\Modules\Messenger\Models\Messenger_Users");
//[build]---------------------------------------------------------------------------------------------------------------
$assign = array();
$assign['theme'] = "Higgs";
$assign['main_template'] = safe_json($json, 'main_template', "c9c3");
$assign['breadcrumb'] = safe_json($json, 'breadcrumb');
$assign['main'] = safe_json($json, 'main');
$assign['left'] = get_development_sidebar();
$assign['right'] = safe_json($json, 'right') . get_application_copyright();
$assign['logo_portrait'] = get_logo("logo_portrait");
$assign['logo_landscape'] = get_logo("logo_landscape");
$assign['logo_portrait_light'] = get_logo("logo_portrait_light");
$assign['logo_landscape_light'] = get_logo("logo_landscape_light");
$assign['type'] = safe_json($json, 'type');
$assign['canonical'] = safe_json($json, 'canonical');
$assign['title'] = safe_json($json, 'title');
$assign['description'] = safe_json($json, 'description');
$assign['categories'] = safe_json($json, 'categories');
$assign['featureds'] = safe_json($json, 'featureds');
$assign['sponsoreds'] = safe_json($json, 'sponsoreds');
$assign['articles'] = safe_json($json, 'articles');
$assign['themostseens'] = safe_json($json, 'themostseens');
$assign['article'] = safe_json($json, 'article');
$assign['next'] = safe_json($json, 'next');
$assign['previus'] = safe_json($json, 'previus');
$assign['messenger'] = true;
$assign['messenger_users'] = false;
$benchmark->stop('time');
$assign['modals'] =safe_module_modal();
$assign['benchmark'] = $benchmark->getElapsedTime('time', 4);
$assign['version'] = $version;
//[print]---------------------------------------------------------------------------------------------------------------
$template = view("App\Views\Themes\Gamma\index", $assign);
echo($template);
?>