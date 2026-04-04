<?php
/**
 * @var object $authentication Authentication service from the ModuleController.
 * @var object $bootstrap Instance of the Bootstrap class from the ModuleController.
 * @var string $component Complete URI to the requested component.
 * @var object $dates Date service from the ModuleController.
 * @var string $oid String representing the object received, usually an object/data to be viewed, transferred from the ModuleController.
 * @var object $parent Represents the ModuleController.
 * @var object $request Request service from the ModuleController.
 * @var object $strings String service from the ModuleController.
 * @var string $view String passed to the view defined in the viewer for evaluation.
 * @var string $viewer Complete URI to the view responsible for evaluating each requested view.
 * @var string $views Complete URI to the module views.
 **/
//[models]--------------------------------------------------------------------------------------------------------------
$mentities = model("\App\Models\Application_Entities", true);
//[vars]----------------------------------------------------------------------------------------------------------------
$back = "/account";
$offset = !empty($request->getVar("offset")) ? $request->getVar("offset") : 0;
$search = !empty($request->getVar("search")) ? $request->getVar("search") : "";
$field = !empty($request->getVar("field")) ? $request->getVar("field") : "";
$limit = !empty($request->getVar("limit")) ? $request->getVar("limit") : 10;
$fields = array(
    "name" => lang("App.Name"),
    //"title" => lang("App.title"),
    //"details" => lang("App.details"),
    //"created_at" => lang("App.created_at"),
    //"updated_at" => lang("App.updated_at"),
);
//[build]--------------------------------------------------------------------------------------------------------------
$conditions = array();
//$mprocesses->clear_AllCache();
$rows = $mentities->getList($search, $limit, $offset);

$total = $mentities->getTotalCount();
//echo(safe_dump($rows['sql']));
//[build]--------------------------------------------------------------------------------------------------------------
$bgrid = $bootstrap->get_Grid();
$bgrid->set_Total($total);
$bgrid->set_Limit($limit);
$bgrid->set_Offset($offset);
$bgrid->set_Class("P-0 m-0");
$bgrid->set_Limits(array(10, 20, 40, 80, 160, 320, 640, 1200, 2400));
$bgrid->set_Headers(array(
    array("content" => "#", "class" => "text-center	align-middle"),
    //array("entitie" => lang("App.Entitie"), "class" => "text-left	align-middle"),
    array("content" => lang("App.Name"), "class" => "text-left	align-middle"),
    array("content" => lang("App.Options"), "class" => "text-center	align-middle"),
));
$bgrid->set_Search(array("search" => $search, "field" => $field, "fields" => $fields,));
$component = '/account/processes';
$count = $offset;
foreach ($rows as $row) {
    if (!empty($row['TABLE_NAME'])) {
        $count++;
        //[links]-------------------------------------------------------------------------------------------------------
        $hrefModel = '/development/generators/model/' . $row["TABLE_NAME"];
        $hrefController = '/development/generators/controller/' . $row["TABLE_NAME"];
        $hrefLister = '/development/generators/lister/' . $row["TABLE_NAME"];
        $hrefCreator = '/development/generators/creator/' . $row["TABLE_NAME"];
        $hrefViewer = '/development/generators/viewer/' . $row["TABLE_NAME"];
        $hrefEditor = '/development/generators/editor/' . $row["TABLE_NAME"];
        $hrefDeleter = '/development/generators/deleter/' . $row["TABLE_NAME"];
        $hreflang = '/development/generators/lang/' . $row["TABLE_NAME"];
        $hrefMigration = '/development/generators/migration/' . $row["TABLE_NAME"];
        //[buttons]-----------------------------------------------------------------------------------------------------
        $lmodel = $bootstrap->get_Link('lmodel', array('text' => "Modelo", 'class' => 'btn-outline', 'href' => $hrefModel, 'target' => '_blank', 'icon' => ICON_MODEL));
        $lcontroller = $bootstrap->get_Link('lcontroller', array('text' => "Controlador", 'class' => 'btn-outline', 'href' => $hrefController, 'target' => '_blank', 'icon' => ICON_CONTROLLER));
        $llister = $bootstrap->get_Link('llister', array('text' => "Listado", 'class' => 'btn-outline', 'href' => $hrefLister, 'target' => '_blank', 'icon' => ICON_LIST));
        $lcreator = $bootstrap->get_Link('lcreator', array('text' => "Creador", 'class' => 'btn-outline', 'href' => $hrefCreator, 'target' => '_blank', 'icon' => ICON_CREATE));
        $lviewer = $bootstrap->get_Link('lviewer', array('text' => "Visualizador", 'class' => 'btn-outline', 'href' => $hrefViewer, 'target' => '_blank', 'icon' => ICON_VIEW));
        $leditor = $bootstrap->get_Link('leditor', array('text' => "Editor", 'class' => 'btn-outline', 'href' => $hrefEditor, 'target' => '_blank', 'icon' => ICON_EDIT));
        $ldeleter = $bootstrap->get_Link('ldeleter', array('text' => "Eliminador", 'class' => 'btn-outline', 'href' => $hrefDeleter, 'target' => '_blank', 'icon' => ICON_DELETE));
        $llangs = $bootstrap->get_Link('llangs', array('text' => "Lenguaje", 'class' => 'btn-outline', 'href' => $hreflang, 'target' => '_blank', 'icon' => ICON_LANG));
        $lmigrations = $bootstrap->get_Link('lmigrations', array('text' => "Migraciones", 'class' => 'btn-outline', 'href' => $hrefMigration, 'target' => '_blank', 'icon' => ICON_MIGRATIONS));
        $options = $bootstrap->get_BtnGroup('options', array('content' => array($lmodel, $lcontroller, $llister, $lcreator, $lviewer, $leditor, $ldeleter, $llangs, $lmigrations)));
        //[etc]---------------------------------------------------------------------------------------------------------
        $bgrid->add_Row(
            array(
                array("content" => $count, "class" => "text-center align-middle"),
                //array("content" => $row["TABLE_NAME"], "class" => "text-left align-middle"),
                array("content" => urldecode($row["TABLE_NAME"]), "class" => "text-left align-middle"),
                array("content" => $options, "class" => "text-center align-middle"),
            )
        );
    }
}
//[build]---------------------------------------------------------------------------------------------------------------
$card = $bootstrap->get_Card("card-grid", array(
    "title" => lang('Processes.list-title'),
    "header-back" => $back,
    "header-add" => "/sie/q10files/import/" . lpk(),
    //"alert" => array("icon" => ICON_INFO, "type" => "info", "title" => lang('Processes.list-title'), "message" => lang('Processes.list-description')),
    "content" => $bgrid,
));
echo($card);
?>