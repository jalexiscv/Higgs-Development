<?php

/** @var array $params - parameters passed from the parent view */

$code = '';
$code .= "/**\n";
$code .= "* Regenera o recrea la tabla de la base de datos en caso de que esta no exista\n";
$code .= "* Ejemplo de campos\n";
$code .= "* \$fields = [\n";
$code .= "*      'id'=> ['type'=>'INT','constraint'=> 5,'unsigned'=> true,'auto_increment' => true],\n";
$code .= "*      'title'=>['type'=> 'VARCHAR','constraint'=>'100','unique'  => true,],\n";
$code .= "*      'author'=>['type'=>'VARCHAR','constraint'=> 100,'default'=> 'King of Town',],\n";
$code .= "*      'description'=>['type'=>'TEXT','null'=>true,],\n";
$code .= "*      'status'=>['type'=>'ENUM','constraint'=>['publish','pending','draft'],'default'=>'pending',],\n";
$code .= "*   ];\n";
$code .= "* Ejemplo de keys\n";
$code .= "* \$forge->addPrimaryKey('id');\n";
$code .= "* \$forge->addKey('title');\n";
$code .= "* \$forge->addUniqueKey(['product', 'discount']); \n";
$code .= "*/\n";
$code .= "private function exec_TableRegenerate()\n";
$code .= "{\n";
$code .= "if (!\$this->get_TableExist()) {\n";
$code .= "\$forge = Database::forge(\$this->DBGroup);\n";
$code .= "\$fields = [\n";
foreach ($datas as $field) {
    if (($field->name != 'author') && ($field->name != 'created_at') && ($field->name != 'updated_at') && ($field->name != 'deleted_at')) {
        if ($field->type == 'int') {
            $code .= "\t\t\t '{$field->name}' => ['type' => 'INT', 'constraint' =>10, 'null' => FALSE],\n";
        } elseif ($field->type == 'double') {
            $code .= "\t\t\t '{$field->name}' => ['type' => 'DOUBLE','constraint' =>'10,2','default' => 0.00, 'null' => FALSE],\n";
        } elseif ($field->type == 'varchar') {
            $code .= "\t\t\t '{$field->name}' => ['type' => 'VARCHAR','constraint' =>{$field->max_length}, 'null' => FALSE],\n";
        } else {
            $type = strtoupper($field->type);
            $code .= "\t\t\t '{$field->name}' => ['type' => '{$type}', 'null' => FALSE],\n";
        }
    }
}
$code .= "'author' => ['type' => 'VARCHAR', 'constraint' => 13, 'null' => FALSE],\n";
$code .= "'created_at' => ['type' => 'DATETIME', 'null' => TRUE],\n";
$code .= "'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],\n";
$code .= "'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],\n";
$code .= "];\n";
$code .= "\$forge->addField(\$fields);\n";
$code .= "\$forge->addPrimaryKey(\$this->primaryKey);\n";
$code .= "\$forge->addKey('author');\n";
$code .= "\$forge->createTable(\$this->table, TRUE);\n";
$code .= "}\n";
$code .= "}\n";
$code .= "\n";
echo($code);
