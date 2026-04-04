<?php

$bootstrap = service("bootstrap");
$strings = service("strings");

$f = service("forms", array("lang" => "Development.texttophp-"));
$model = model("App\Modules\Plex\Models\Plex_Modules");
$d = array(
    "module" => $f->get_Value("module"),
);
$content = "Generando Módulo: " . $d["module"] . "\n";
//[directories]---------------------------------------------------------------------------------------------------------
$main = APPPATH . "Modules/_" . $strings->get_Ucfirst($d["module"]);
if (!is_dir($main)) {
    mkdir($main, 0777, true);
}
$directories = array(
    "config" => $main . "/" . "Config",
    "controllers" => $main . "/" . "Controllers",
    "helpers" => $main . "/" . "Helpers",
    "language" => $main . "/" . "Language",
    "language-es" => $main . "/" . "Language/es",
    "models" => $main . "/" . "Models",
    "views" => $main . "/" . "Views",
    "views-home" => $main . "/" . "Views/Home",
    "views-denied" => $main . "/" . "Views/Denied",
);
foreach ($directories as $key => $value) {
    $path = $value;
    $content .= "Creando Directorio: $path\n";
    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }
}
//[files]---------------------------------------------------------------------------------------------------------
$files = array(
    "routes" => array("file" => $directories["config"] . "/Routes.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Routes", $d)),
    "controller-module" => array("file" => $directories["controllers"] . "/" . $strings->get_Ucfirst($d["module"]) . ".php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Controller", $d)),
    "controller-router" => array("file" => $directories["controllers"] . "/Router.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Router", $d)),
    "controller-api" => array("file" => $directories["controllers"] . "/Api.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Api", $d)),
    "helper" => array("file" => $directories["helpers"] . "/" . $strings->get_Ucfirst($d["module"]) . "_helper.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Helper", $d)),
    "models-modules" => array("file" => $directories["models"] . "/" . $strings->get_Ucfirst($d["module"]) . "_Modules.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Modules", $d)),
    "models-clients-modules" => array("file" => $directories["models"] . "/" . $strings->get_Ucfirst($d["module"]) . "_Clients_Modules.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Clients_Modules", $d)),
    "views-index" => array("file" => $directories["views"] . "/index.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Index", $d)),
    "home-breadcrumb" => array("file" => $directories["views"] . "/Home/breadcrumb.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Home\breadcrumb", $d)),
    "home-deny" => array("file" => $directories["views"] . "/Home/deny.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Home\deny", $d)),
    "home-index" => array("file" => $directories["views"] . "/Home/index.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Home\index", $d)),
    "home-view" => array("file" => $directories["views"] . "/Home/view.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Home\\view", $d)),
    "denied-index" => array("file" => $directories["views"] . "/Denied/index.php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Denied\index", $d)),
    "language-es-index" => array("file" => $directories["language-es"] . "/" . $strings->get_Ucfirst($d["module"]) . ".php", "content" => view("App\Modules\Development\Views\Tools\Modules\Generator\Templates\Languages\index", $d)),
);
foreach ($files as $key => $value) {
    $path = $value["file"];
    $content .= "Creando Archivo: $path\n";
    if (!file_exists($path)) {
        file_put_contents($path, $value["content"]);
    }
}
$l["back"] = "/development/tools/home/" . lpk();
$r["code"] = $content;
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->groups["g2"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["code"])));
$card = $bootstrap->get_Card("card-view-service", array(
    "title" => "PHP",
    "header-back" => $l["back"],
    "content" => $f,
));
echo($card);
?>