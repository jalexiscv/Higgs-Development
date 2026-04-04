<?php
set_time_limit(300000);
$b = service("bootstrap");


$f = service("forms", array("lang" => "Development.texttophp-"));


$model = model("App\Modules\Plex\Models\Plex_Modules");
$d = array(
    "text" => $f->get_Value("text"),
);

$lineas = explode("\n", $d["text"]);


$content = "";
$string = "";
foreach ($lineas as $linea) {
    if (strpos($linea, "#") === 0) {
        $content .= $linea . "\n";
    } else {
        if (strpos($linea, "msgid") === 0) {
            preg_match('/"([^"]+)"/', $linea, $coincidencias);
            if (!empty($coincidencias)) {
                $string = $coincidencias[1];
                $content .= $linea . "\n";

                $traslated = gpt_translate($string);//gpt_translate($string);
                $content .= "msgstr \"{$traslated}\"\n\n";
            }
        }
    }
}


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