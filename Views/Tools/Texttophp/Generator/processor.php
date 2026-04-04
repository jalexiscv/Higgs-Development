<?php

$b = service("bootstrap");


$f = service("forms", array("lang" => "Development.texttophp-"));


$model = model("App\Modules\Plex\Models\Plex_Modules");
$d = array(
    "text" => $f->get_Value("text"),
);

$lineas = explode("\n", $d["text"]);


$content = "";
$content .= "<?php\n";
foreach ($lineas as $linea) {
    $linea = str_replace("  ", '\t', $linea);
    $linea = trim($linea);
    $linea = str_replace('\'', '\\\'', $linea);
    $linea = str_replace('$', '\$', $linea);
    $linea = str_replace('"', '\"', $linea);
    $linea = str_replace("\n", '', $linea);
    $linea = str_replace("\\'", "'", $linea);
    $content .= "\$code.=\"{$linea}\\n\";\n";
}
$content .= "?>";


$l["back"] = "/development/tools/home/" . lpk();
$r["code"] = $content;
$f->fields["code"] = $f->get_FieldCode("code", array("value" => $r["code"], "mode" => "php"));
$f->groups["g2"] = $f->get_Group(array("legend" => "", "fields" => ($f->fields["code"])));

$card = $b->get_Card("card-view-service", array(
    "title" => "PHP",
    "header-back" => $l["back"],
    "content" => $f,
));
echo($card);
?>