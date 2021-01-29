<?php
namespace Modules;

class Backup {
    public array $data;
    public function __construct(array $data = []) {
        $this->data = $data;
    }
    public function convertDataToJson() {
        $json = json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $json;
    }
    public function file() {
        $name = "backups/backup_".date("Y-m-d_h:m:s").'.json';
        $file = fopen($name, "w+");
        if (!$file) {
            throw new \Exception("Has been occurred an error while make the json file.");
        }
        return [
            "file" => $file,
            "path" => $name
        ];
    }
    public function export() {
        try {
            $file = $this->file();
            $data = $this->convertDataToJson();
            fwrite($file['file'], $data);
        } catch (\Throwable $error) {
            return false;
        }
        return [
            "path" => $file['path']
        ];
    }
}