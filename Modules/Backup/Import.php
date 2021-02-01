<?php
namespace Modules\Backup;

class Import {
    public string $filePath;
    public array $nullArray = [
        "NO" => "NOT NULL",
        "YES" => "NULL"
    ],
    $keyArray = [
        "PRI" => "PRIMARY KEY",   
    ],
    $extraArray = [
        "auto_increment" => "AUTO_INCREMENT",
        "on update current_timestamp()" => "ON UPDATE current_timestamp()"    
    ],
    $defaultArray = [
        "current_timestamp()" => "DEFAULT current_timestamp()"
    ];
    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }
    public function readFile() {
        ob_start();
        include $this->filePath;
        $file = ob_get_contents();
        ob_end_clean();
        return $file;
    }
    public function validateColumnStructure($columnStructure) {
        $fieldList = array();
        foreach ($columnStructure as $field => $value) {
            if ($field === 'Field' && $value) {
                $fieldList[] = $value;
            } elseif ($field === 'Type' && $value) {
                $fieldList[] = strtoupper($value);
            } elseif ($field === 'Null' && $value) {
                $fieldList[] = $this->nullArray[$value];
            } elseif ($field === 'Key' && $value) {
                $fieldList[] = $this->keyArray[$value];
            } elseif ($field === 'Default' && $value) {
                $fieldList[] = array_key_exists($value, $this->defaultArray) ? $this->defaultArray[$value] : "DEFAULT '$value'";
            } elseif ($field === 'Extra' && $value) {
                $fieldList[] = $this->extraArray[$value];
            }
        }
        return $fieldList;
    }
    public function getColumns(array $structure) {
        $columns = array();
        foreach ($structure as $column) {
            $create = $this->validateColumnStructure($column);
            $columns[] = implode(' ', $create);
        }
        return $columns;
    }
    public function getTables(array $fileBackup) {
        $tables = array();
        foreach ($fileBackup as $table) {
            $tableName = $table->tableName;
            $columns = $this->getColumns($table->structure);
            $tables[] = "CREATE TABLE IF NOT EXISTS ".$tableName."(".implode(', ', $columns).") ENGINE = InnoDB DEFAULT CHARSET=utf8mb4;";
        }
        return $tables;
    }
    public function start() {
        $fileBackup = json_decode($this->readFile());
        $tablesToCreate = $this->getTables($fileBackup);
        return $tablesToCreate;
    }
}