<?php

namespace App\Controller;

use Exception;
use RegexIterator;
use FilesystemIterator;

class ScriptRemotoController extends AppController
{
    /**
     * Obtiene la ruta de la carpeta con las versiones del api de la app
     *
     * @return string
     */
    public static function getVersionesDirPath()
    {
        return ROOT . DS . ".." . DS . "entregas100" . DS;
    }

    /**
     * Lee las carpetas de las versiones de la app existentes y devuelve un json con los nombres
     *
     * @return Cake\Network\Response
     */
    public function versiones()
    {
        $path = static::getVersionesDirPath();
        $iterator = new RegexIterator(new FilesystemIterator($path), "/v\d+/");
        $versiones = [];

        foreach ($iterator as $item) {
            $versiones[] = [
                "version" => $item->getFilename()
            ];
        }

        return $this->asJson([
            "success" => true,
            "message" => "Versiones de la aplicacion",
            "records" => $versiones
        ]);
    }

    /**
     * Lee los scripts precodificados
     *
     * @return Cake\Network\Response
     */
    public function readPrecodificados()
    {
        $records = [
            [
                "id" => "En blanco",
                "content" => ""
            ]
        ];

        $path = WWW_ROOT . "js" . DS . "scripts-precodificados";
        $iterator = new RegexIterator(new FilesystemIterator($path), "/\.js$/");
        foreach ($iterator as $item) {
            $records[] = [
                "id" => $item->getFilename(),
                "content" => file_get_contents($item->getPathname())
            ];
        }

        return $this->asJson([
            "success" => true,
            "message" => "Scripts precodificados",
            "records" => $records
        ]);
    }

    /**
     * Lee los archivos que son script remotos (.js) en la carpeta dependiendo de la version del api de la app
     *
     * @return Cake\Network\Response
     */
    public function read()
    {
        $version = $this->request->query['version'];
        $path = static::getVersionesDirPath();
        $fullpath = $path . $version . DS . "webroot" . DS . "js";
        $iterator = new RegexIterator(new FilesystemIterator($fullpath), "/\.js$/");
        $files = [];

        foreach ($iterator as $item) {
            $files[] = [
                "filename" => $item->getFilename(),
                "pathname" => str_replace($path, "", $item->getPathname()),
                "last_modified" => $item->getMTime(),
                "content" => null,
                "size" => $item->getSize()
            ];
        }

        return $this->asJson([
            "success" => true,
            "message" => "Scripts",
            "records" => $files
        ]);
    }

    /**
     * Eliminar los archivos que son script remotos proporcionados
     *
     * @return 
     */
    public function delete()
    {
        $files = json_decode($this->request->data['records'], true);
        $path = $this->getVersionesDirPath();

        foreach ($files as $item) {
            unlink($path . $item['pathname']);
        }

        return $this->asJson([
            "success" => true,
            "message" => "Script eliminado"
        ]);
    }

    /**
     * Crea el archivo del script remoto proporcionado
     *
     * @return Cake\Network\Response
     */
    public function create()
    {
        $records = json_decode($this->request->data['records'], true);
        $path = static::getVersionesDirPath();

        foreach ($records as $item) {
            file_put_contents($path . $item['pathname'], $item['content']);
        }

        return $this->asJson([
            "success" => true,
            "message" => "Script agregado"
        ]);
    }
}
