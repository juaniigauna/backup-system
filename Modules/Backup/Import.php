<?php
namespace Modules\Backup;

class Import {
    public string $filePath;
    public array $data,
    $nullArray = [
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
        $this->data = $this->readFile();
    }
    public function readFile() {
        ob_start();
        include $this->filePath;
        $file = ob_get_contents();
        ob_end_clean();
        return json_decode($file);
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

    public function getTables() {
        $tables = array();
        foreach ($this->data as $table) {
            $tableName = $table->tableName;
            $columns = $this->getColumns($table->structure);
            $tables[] = "CREATE TABLE IF NOT EXISTS ".$tableName."(".implode(', ', $columns).") ENGINE = InnoDB DEFAULT CHARSET=utf8mb4";
        }
        return $tables;
    }
    public function getRows() {
        $tables = array();
        foreach ($this->data as $table) {
            $structure = array();
            $rows = array();
            foreach ($table->structure as $column) {
                $structure[] = $column->Field;
            }
            foreach ($table->rows as $row) {
                $rowValues = array();
                foreach ($row as $value) {
                    $rowValues[] = "'$value'";
                }
                $rows[] = '('.implode(',', $rowValues).')';
            }
            if (count($rows) > 0) {
                $tables[] = 'INSERT INTO '.$table->tableName.'('.implode(',', $structure).') VALUES '.implode(',', $rows);
            }
        }
        return $tables;
    }
    public function start() {
        $tablesToCreate = $this->getTables();
        $rowsToCreate = $this->getRows();
        return [
            "tables" => $tablesToCreate,
            "rows" => $rowsToCreate
        ];
    }
}