<?php

namespace App\Controller;

use DateTime;
use DateInterval;
use Exception;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Worksheet_PageSetup;
use PHPExcel_Style;
use PHPExcel_Style_Alignment;
use PHPExcel_Style_Border;

class ManguerasController extends AppController
{

    /**
     * Lee el catalogo de las mangueras
     * @return JsonResponse
     */
    public function read()
    {
        $oConexion = $this->getConexion('mangueras');
        $plaza_id = $_REQUEST['plaza_id'];
        $rubro_venta_id = $_REQUEST['rubro_venta_id'];

        // obtiene la informacion pedida por el usuario
        if ($rubro_venta_id == 0) {
                $sQuery = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plaza,
                rubros_ventas.nombre AS rubro_venta,
                IF(mangueras.rubro_venta_id = 1,CONCAT('B-',LPAD(mangueras.id_manguera,3,'0')),
                IF(mangueras.rubro_venta_id = 2,CONCAT('P-',LPAD(mangueras.id_manguera,3,'0')),
                IF(mangueras.rubro_venta_id = 3,CONCAT('A-',LPAD(mangueras.id_manguera,3,'0')),
                IF(mangueras.rubro_venta_id = 4,CONCAT('E-',LPAD(mangueras.id_manguera,3,'0')),
                IF(mangueras.rubro_venta_id = 5,CONCAT('R-',LPAD(mangueras.id_manguera,3,'0')),0)))))AS manguera
                FROM mangueras
                INNER JOIN plazas ON plazas.id_plaza = mangueras.plaza_id
                INNER JOIN permisos ON permisos.planta_id = mangueras.planta_id
                INNER JOIN rubros_ventas ON rubros_ventas.id_rubro_venta = mangueras.rubro_venta_id
                WHERE mangueras.estatus = 1 AND mangueras.plaza_id = ?";
                $rQuery = $oConexion->query($sQuery, [
                    $plaza_id
                ]);
        } else {
            $sQuery = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plaza,
                    rubros_ventas.nombre AS rubro_venta,
                    IF(mangueras.rubro_venta_id = 1,CONCAT('B-',LPAD(mangueras.id_manguera,3,'0')),
                    IF(mangueras.rubro_venta_id = 2,CONCAT('P-',LPAD(mangueras.id_manguera,3,'0')),
                    IF(mangueras.rubro_venta_id = 3,CONCAT('A-',LPAD(mangueras.id_manguera,3,'0')),
                    IF(mangueras.rubro_venta_id = 4,CONCAT('E-',LPAD(mangueras.id_manguera,3,'0')),
                    IF(mangueras.rubro_venta_id = 5,CONCAT('R-',LPAD(mangueras.id_manguera,3,'0')),0)))))AS manguera
                    FROM mangueras
                    INNER JOIN plazas ON plazas.id_plaza = mangueras.plaza_id
                    INNER JOIN permisos ON permisos.planta_id = mangueras.planta_id
                    INNER JOIN rubros_ventas ON rubros_ventas.id_rubro_venta = mangueras.rubro_venta_id
                    WHERE mangueras.estatus = 1 AND mangueras.plaza_id = ? AND mangueras.rubro_venta_id = ?";
            $rQuery = $oConexion->query($sQuery, [
                $plaza_id,
                $rubro_venta_id
            ]);
        }

        if (count($rQuery) > 0) {
            $records = $rQuery;
        }

            return $this->asJson(array(
                "success" => true,
                "message" => "Catalogo de mangueras",
                "records" => $records,
                "metadata" => array(
                    "total_registros" => count($records)
                )
            ));
    }

    /**
     * Lee el catalogo de manguerasReInsertar
     * @return JsonResponse
     */
    public function readManguerasReInsertar()
    {
        $oConexion = $this->getConexion('manguerasReInsertar');
        // $plaza_id = $_REQUEST['plaza_id'];

        // obtiene la informacion pedida por el usuario
        $query = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plaza,
            IF(mangueras.rubro_venta_id = 1,CONCAT('B-',LPAD(mangueras.id_manguera,3,'0')),
            IF(mangueras.rubro_venta_id = 2,CONCAT('P-',LPAD(mangueras.id_manguera,3,'0')),
            IF(mangueras.rubro_venta_id = 3,CONCAT('A-',LPAD(mangueras.id_manguera,3,'0')),
            IF(mangueras.rubro_venta_id = 4,CONCAT('E-',LPAD(mangueras.id_manguera,3,'0')),
            IF(mangueras.rubro_venta_id = 5,CONCAT('R-',LPAD(mangueras.id_manguera,3,'0')),0)))))AS manguera
            FROM mangueras
            INNER JOIN plazas ON plazas.id_plaza = mangueras.plaza_id
            INNER JOIN permisos ON permisos.planta_id = mangueras.planta_id
            INNER JOIN rubros_ventas ON rubros_ventas.id_rubro_venta = mangueras.rubro_venta_id
            WHERE mangueras.estatus = 1 ";
        $rQuery = $oConexion->query($query);

        $records = $rQuery;

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de mangueras de reinsertar",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    /**
    * Carga el combobox de plantas
    * @return JsonResponse
    */
    public function Plazas()
    {
        $oConexion = $this->getConexion('mangueras');

        // obtiene todas las plazas
        $sQuery = "SELECT * FROM plazas";
        $rQuery = $oConexion->query($sQuery);

        if (count($rQuery) > 0) {
            $records = $rQuery;
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de plazas",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    /**
    * Carga el combobox de plantas
    * @return JsonResponse
    */
    public function Plantas()
    {
        $oConexion = $this->getConexion('mangueras');
        $plaza_id = $_REQUEST['plaza_id'];

        // obtiene todas las plazas
        $sQuery = "SELECT * FROM plantas WHERE plaza_id = ?";
        $rQuery = $oConexion->query($sQuery, [
            $plaza_id
        ]);

        if (count($rQuery) > 0) {
            $records = $rQuery;
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de plantas",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    /**
    * Carga la informacion de claves
    * @return JsonResponse
    */
    public function maecias()
    {
        $oConexion = $this->getConexion('mangueras');
        $plaza_id = $_REQUEST['plaza_id'];

        // obtiene todas las plazas
        $sQuery = "SELECT * FROM maecias WHERE plaza_id = ?";
        $rQuery = $oConexion->query($sQuery, [
            $plaza_id
        ]);

        if (count($rQuery) > 0) {
            $records = $rQuery;
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de claves",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    /**
    * Carga la informacion de claves
    * @return JsonResponse
    */
    public function rubrosVentas()
    {
        $oConexion = $this->getConexion('mangueras');

        // obtiene todas los rubros de ventas
        $sQuery = "SELECT * FROM rubros_ventas";
        $rQuery = $oConexion->query($sQuery);

        // if (count($rQuery) > 0) {
            $records = $rQuery;
        // }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de rubros de ventas",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    public function ultimaManguera()
    {
        $oConexion = $this->getConexion('mangueras');
        $plaza_id = $_REQUEST['plaza_id'];
        $rubro_venta_id = $_REQUEST['rubro_venta_id'];
        $planta_id = $_REQUEST['planta_id'];

        // obtiene todas las plazas
        $sQuery = "SELECT MAX(num_manguera) AS ultimo FROM mangueras WHERE plaza_id = ? AND rubro_venta_id = ? AND planta_id = ?";
        $rQuery = $oConexion->query($sQuery, [
            $plaza_id,
            $rubro_venta_id,
            $planta_id
        ]);

        $rQuery[0]['ultimo'] = $rQuery[0]['ultimo']+1;

        if (count($rQuery) > 0) {
            $records = $rQuery;
        }

        return $this->asJson(array(
            "success" => true,
            "message" => "Catalogo de claves",
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }

    /**
     * Crea una manguera
     * @return JsonResponse
     */
    public function insert()
    {
        try {
            $records = json_decode($_REQUEST['records'], true);
            $records = $records[0];
            $success = false;

            $oConexion = $this->getConexion('mangueras');
            $manguerasReInsertar = $this->getConexion('manguerasReInsertar');
            $conexiones = array('mangueras'=> $oConexion);
            $conne = array();
            $con = "SELECT * FROM conexion WHERE plaza_id = ?";
            $aQueryParams = array($records['plaza_id']);
            $aResultado = $oConexion->query($con, $aQueryParams);

            if (count($aResultado) <= 0) {
                throw new Exception("Error al obtener los datos de conexion a la plaza");
            }

            $cont = 1;
            foreach ($aResultado as $key => $value) {
                $var = 'conexion'.$cont;
                $cone = preg_replace('/\s+/', '', strtolower($value['nom_servidor']));
                $$var = $this->getConexion(
                    $cone,
                    array(
                    "host" => $value["ip"],
                    "username" => $value["usuario"],
                    "password" => $value["password"],
                    "database" => $value["base"]
                    )
                );

                $conexiones[$value['nom_servidor']] = $$var;
                $cont++;
            }

            set_time_limit(600);

            if (array_key_exists('num_estacion', $records)) {
                $query = "SELECT * FROM mangueras WHERE num_estacion = ? AND planta_id = ? AND mangueras.estatus = 1";
                $rQuery = $oConexion->query($query, [
                $records['num_estacion'],
                $records['planta_id']
                ]);

                if (empty($rQuery)) {
                    $query = "SELECT * FROM mangueras WHERE plaza_id = ? AND planta_id = ? AND rubro_venta_id = ? 
                    AND num_estacion = ? AND num_bomba = ? AND mangueras.estatus = 1";
                    $rQuery = $oConexion->query($query, [
                        $records['plaza_id'],
                        $records['planta_id'],
                        $records['rubro_venta_id'],
                        $records['num_estacion'],
                        $records['num_bomba']
                    ]);

                    if (empty($rQuery)) {
                            $query = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, rubro_venta, num_manguera, descrip_manguera, descrip_rubro_venta, num_eco, num_bascula, num_red, 
                        num_bomba, num_estacion, sub_red, fecha_alta, fecha_baja) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                            $sQuery = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, 
                            cvecia_id, rubro_venta, num_manguera, descrip_manguera, descrip_rubro_venta, num_eco, 
                            num_bascula, num_red, num_bomba, num_estacion, sub_red, fecha_alta, fecha_baja, servidor)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                        foreach ($conexiones as $key => $value) {
                            try {
                                    $value->begin();
                                    $manguerasReInsertar->begin();
                                        $rQuery = $value->query($query, [
                                            $records['plaza_id'],
                                            $records['plaza'],//pendiente de verificar
                                            $records['cvecia'],
                                            $records['planta_id'],
                                            $records['rubro_venta_id'],
                                            $records['rubro_venta'],
                                            $records['num_manguera'],
                                            $records['descrip_manguera'],
                                            $records['descrip_rubro_venta'],
                                            (isset($records['num_bascula'])== true ? $records['num_bascula'] : 0),
                                            (isset($records['num_bomba']) == true ? $records['num_bomba'] : 0),
                                            (isset($records['num_eco']) == true ?  $records['num_eco'] : 0),
                                            (isset($records['num_estacion']) == true  ? $records['num_estacion'] : 0),
                                            (isset($records['num_red']) == true ? $records['num_red'] : 0),
                                            (isset($records['sub_red']) == true ? $records['sub_red'] : 0),
                                            date("Y-m-d"),
                                            '0000-00-00'
                                        ]);

                                    $records['clientId'] = $records['id_manguera'];
                                    $records['id_manguera'] = $oConexion->lastInsertId();
                                    $records['estatus'] = 1;
                                // $data = [$data];
                            } catch (Exception $e) {
                                $rQuery = $manguerasReInsertar->query($sQuery, [
                                    $records['plaza_id'],
                                    $records['plaza'],//pendiente de verificar
                                    $records['cvecia'],
                                    $records['planta_id'],
                                    $records['rubro_venta_id'],
                                    $records['cvecia_id'],
                                    $records['rubro_venta'],
                                    $records['num_manguera'],
                                    $records['descrip_manguera'],
                                    $records['descrip_rubro_venta'],
                                    (isset($records['num_eco']) == true ?  $records['num_eco'] : 0),
                                    (isset($records['num_bascula'])== true ? $records['num_bascula'] : 0),
                                    (isset($records['num_red']) == true ? $records['num_red'] : 0),
                                    (isset($records['num_bomba']) == true ? $records['num_bomba'] : 0),
                                    (isset($records['num_estacion']) == true  ? $records['num_estacion'] : 0),
                                    (isset($records['sub_red']) == true ? $records['sub_red'] : 0),
                                    date("Y-m-d"),
                                    '0000-00-00',
                                    $key
                                ]);

                                $records['clientId'] = $records['id_manguera'];
                                $records['id_manguera'] = $manguerasReInsertar->lastInsertId();
                                $records['estatus'] = 1;
                                // $data = [$data];
                            }
                            $value->commit();
                            $manguerasReInsertar->commit();
                        }

                            $records = [$records];

                            $msg = "<center>Se agrego correctamente!</center>";
                            $success = true;
                    } else {
                        $msg = "<center>Ya existe la bomba <b>".$records['num_bomba']." </b> en la estación <b>
                        ".$records['num_estacion']." </b>, verifique porfavor</center>";
                        $success = false;
                    }
                } else {
                    $msg = "<center>Ya existe el numero de estación <b>".$records['num_estacion']." </b> en la Planta <b>"
                    .$records['nombre_planta']."</b>, verifique porfavor</center>";
                    $success = false;
                }
            } else {
                if ($records['rubro_venta_id'] == 1) {
                    $query = "SELECT * FROM mangueras WHERE plaza_id = ? AND planta_id = ? AND rubro_venta_id = ? 
                     AND num_bascula = ? AND mangueras.estatus = 1";
                    $rQuery = $oConexion->query($query, [
                    $records['plaza_id'],
                    $records['planta_id'],
                    $records['rubro_venta_id'],
                    $records['num_bascula']
                    ]);
                }
                if ($records['rubro_venta_id'] == 3) {
                    $query = "SELECT * FROM mangueras WHERE plaza_id = ? AND planta_id = ? AND rubro_venta_id = ? 
                    AND num_eco = ? AND mangueras.estatus = 1";
                    $rQuery = $oConexion->query($query, [
                    $records['plaza_id'],
                    $records['planta_id'],
                    $records['rubro_venta_id'],
                    $records['num_eco']
                    ]);
                }
                if ($records['rubro_venta_id'] == 5) {
                    $query = "SELECT * FROM mangueras WHERE plaza_id = ? AND planta_id = ? AND rubro_venta_id = ? 
                    AND num_red = ? AND sub_red = ? AND mangueras.estatus = 1";
                    $rQuery = $oConexion->query($query, [
                    $records['plaza_id'],
                    $records['planta_id'],
                    $records['rubro_venta_id'],
                    $records['num_red'],
                    $records['sub_red']
                    ]);
                }

                if (empty($rQuery)) {
                    $query = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, rubro_venta, 
                    num_manguera, descrip_manguera, descrip_rubro_venta, num_bascula, num_bomba, num_eco, num_estacion, 
                    num_red, sub_red, fecha_alta, fecha_baja) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                    $sQuery = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, rubro_venta, 
                    num_manguera, descrip_manguera, descrip_rubro_venta, num_bascula, num_bomba, num_eco, num_estacion, 
                    num_red, sub_red, fecha_alta, fecha_baja, servidor) 
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                        
                    foreach ($conexiones as $key => $value) {
                        try {
                            $value->begin();
                            $manguerasReInsertar->begin();
                            $rQuery = $value->query($query, [
                            $records['plaza_id'],
                            $records['plaza'],//pendiente de verificar
                            $records['cvecia'],
                            $records['planta_id'],
                            $records['rubro_venta_id'],
                            $records['rubro_venta'],
                            $records['num_manguera'],
                            $records['descrip_manguera'],
                            $records['descrip_rubro_venta'],
                            (isset($records['num_bascula'])== true ? $records['num_bascula'] : 0),
                            (isset($records['num_bomba']) == true ? $records['num_bomba'] : 0),
                            (isset($records['num_eco']) == true ?  $records['num_eco'] : 0),
                            (isset($records['num_estacion']) == true  ? $records['num_estacion'] : 0),
                            (isset($records['num_red']) == true ? $records['num_red'] : 0),
                            (isset($records['sub_red']) == true ? $records['sub_red'] : 0),
                            date("Y-m-d"),
                            '0000-00-00'
                            ]);

                            $records['clientId'] = $records['id_manguera'];
                            $records['id_manguera'] = $oConexion->lastInsertId();
                            $records['estatus'] = 1;
                        } catch (Exception $e) {
                            $rQuery = $manguerasReInsertar->query($sQuery, [
                            $records['plaza_id'],
                            $records['plaza'],//pendiente de verificar
                            $records['cvecia'],
                            $records['planta_id'],
                            $records['rubro_venta_id'],
                            $records['rubro_venta'],
                            $records['num_manguera'],
                            $records['descrip_manguera'],
                            $records['descrip_rubro_venta'],
                            (isset($records['num_bascula'])== true ? $records['num_bascula'] : 0),
                            (isset($records['num_bomba']) == true ? $records['num_bomba'] : 0),
                            (isset($records['num_eco']) == true ?  $records['num_eco'] : 0),
                            (isset($records['num_estacion']) == true  ? $records['num_estacion'] : 0),
                            (isset($records['num_red']) == true ? $records['num_red'] : 0),
                            (isset($records['sub_red']) == true ? $records['sub_red'] : 0),
                            date("Y-m-d"),
                            '0000-00-00',
                            $key
                            ]);
                            
                            $records['clientId'] = $records['id_manguera'];
                            $records['id_manguera'] = $manguerasReInsertar->lastInsertId();
                            $records['estatus'] = 1;
                        }
                        $value->commit();
                        $manguerasReInsertar->commit();
                    }

                    // $data['clientId'] = $data['id_manguera'];
                    // $data['id_manguera'] = $oLink->lastInsertId();
                    // $data['estatus'] = 1;
                    $records = [$records];

                    $msg = "<center>Se agrego correctamente!</center>";
                    $success = true;
                } else {
                    $msg = "<center>Ya existe la información, verifique porfavor</center>";
                    $success = false;
                }
            }
        } catch (Exception $e) {
            $success = "false";
            $records = "";
            if (strpos($e->getMessage(), '1451')) {
                $msg = "El registro ya fue ligado por lo tanto no se puede eliminar.";
            } elseif (strpos($e->getMessage(), '1052')) {
                $msg = "Existen nombres de columna ambiguos en la consulta.";
            } else {
                $msg = "Contiene un error en el servidor!";
            }
        }
            return $this->asJson([
                "success" => $success,
                "message" => $msg,
                "records" => $records
            ]);
    }

    public function insertManguerasReInsertar()
    {
        $oLink = $this->getConexion('manguerasReInsertar');
        $oConexion = $this->getConexion('mangueras');
        $msg     = "";
        $success = false;
        $records = json_decode($_REQUEST['records'], true);
        // $records = $records[0];

        set_time_limit(300);
        $conexiones = array();

        foreach ($records as $key => $value) {
            $servidor = $value['servidor'];
            if (array_key_exists($servidor, $conexiones)) {
                $cone = $conexiones[$servidor];
            } elseif ($servidor == 'mangueras') {
                $cone = $oConexion;
            } else {
                $query = "SELECT * FROM conexion WHERE nom_servidor = ?";
                $rQuery = $oConexion->query($query, [
                    $servidor
                ]);

                $info = $rQuery[0];
                $cone = $this->getConexion(
                    $servidor,
                    array(
                        "host" => $info["ip"],
                        "username" => $info["usuario"],
                        "password" => $info["password"],
                        "database" => $info["base"]
                    )
                );
                $conexiones[$servidor] = $cone;
            }
            try {
                // $oLink->begin();
                // $cone->begin();
               
                $query = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, rubro_venta,
                    num_manguera, descrip_manguera, descrip_rubro_venta, num_bascula, num_bomba, num_eco, num_estacion,
                    num_red, sub_red, fecha_alta, fecha_baja)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
                $rQuery = $cone->query($query, [
                    $value['plaza_id'],
                    $value['plaza'],//pendiente de verificar
                    $value['cvecia'],
                    $value['planta_id'],
                    $value['rubro_venta_id'],
                    $value['rubro_venta'],
                    $value['num_manguera'],
                    $value['descrip_manguera'],
                    $value['descrip_rubro_venta'],
                    (isset($value['num_bascula'])== true ? $value['num_bascula'] : 0),
                    (isset($value['num_bomba']) == true ? $value['num_bomba'] : 0),
                    (isset($value['num_eco']) == true ?  $value['num_eco'] : 0),
                    (isset($value['num_estacion']) == true  ? $value['num_estacion'] : 0),
                    (isset($value['num_red']) == true ? $value['num_red'] : 0),
                    (isset($value['sub_red']) == true ? $value['sub_red'] : 0),
                    date("Y-m-d"),
                    '0000-00-00'
                ]);

                if ($rQuery > 0) {
                    $sQuery = "DELETE FROM mangueras WHERE id_manguera = ?";
                    $rQuery = $oLink->query($sQuery, [
                    $value['id_manguera']
                    ]);
                }
                // $oLink->commit();
                // $cone->begin();
            } catch (Exception $e) {
                $success = "false";
                $records = "";
                if (strpos($e->getMessage(), '1451')) {
                    $msg = "El registro ya fue ligado por lo tanto no se puede eliminar.";
                } elseif (strpos($e->getMessage(), '1052')) {
                    $msg = "Existen nombres de columna ambiguos en la consulta.";
                } else {
                    $msg = "Contiene un error en el servidor!";
                }
            }
        }

        $msg = "<center>Se agrego correctamente!</center>";
        $success = true;

        return $this->asJson([
            "success" => $success,
            "message" => $msg,
            "records" => $records
        ]);
    }

    /**
     * Elimina el registro seleccionado por el usuario
     * @return JsonResponse
     */
    public function destroy()
    {
        $oConexion = $this->getConexion();
        $records = json_decode($_REQUEST["records"], true);
        $records = $records[0];
        $success = "";
        $msg = "";
        try {
            $query = "UPDATE mangueras SET estatus = ?, fecha_baja = ? WHERE id_manguera = ?";
            $rQuery = $oConexion->query($query, [
                0,
                date("Y-m-d"),
                $records['id_manguera']
            ]);

            $success = true;
            $msg = "<center>Se elimino correctamente!</center>";
        } catch (Exception $e) {
            if (strpos($e->getMessage(), '1451')) {
                $msg = "El registro ya fue ligado por lo tanto no se puede eliminar.";
            } elseif (strpos($e->getMessage(), '1052')) {
                $msg = "Existen nombres de columna ambiguos en la consulta.";
            } else {
                $msg = "Contiene un error";
            }
        }

        return $this->asJson(array(
            "success" => $success,
            "message" => $msg
        ));
    }

    public function excel()
    {
        $msg     = "";
        // require_once 'Classes/PHPExcel.php';
        $success = false;
        $records = "";
        set_time_limit(0);
        ini_set('memory_limit', '256M');
        date_default_timezone_set('America/Mazatlan');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getProperties()->setCreator("Pyansa, S.A. de C.V.")
                    ->setLastModifiedBy("Pyansa, S.A. de C.V.");

        $n_sheet = 0;
        //inicio estilos
        $o_titulo = new PHPExcel_Style(); //nuevo estilo
        $o_titulo->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                    'font' => array(//fuente
                        'bold' => true,
                        'size' => 12,
                        'name' => 'Arial'
                    )
            )
        );
        $o_encabezados = new PHPExcel_Style(); //nuevo estilo
        $o_encabezados->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                    'font' => array(//fuente
                        'bold' => true,
                        'size' => 9,
                        'name' => 'Arial',
                        'color' => array(
                            'rgb' => '000000'
                        )
                    )
            )
        );
        $o_textitos = new PHPExcel_Style();
        $o_textitos->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                    'font' => array(//fuente
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );
        $o_textitosL = new PHPExcel_Style();
        $o_textitosL->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    ),
                    'font' => array(//fuente
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );

        $o_textitosR = new PHPExcel_Style();
        $o_textitosR->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                    ),
                    'font' => array(//fuente
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );


        $o_textitosNeg = new PHPExcel_Style();
        $o_textitosNeg->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
                    ),
                    'font' => array(//fuente
                        'bold' => true,
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );
        $o_textitosLNeg = new PHPExcel_Style();
        $o_textitosLNeg->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT
                    ),
                    'font' => array(//fuente
                        'bold' => true,
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );

        $o_textitosRNeg = new PHPExcel_Style();
        $o_textitosRNeg->applyFromArray(
            array('alignment' => array(//alineacion
                        'wrap' => false,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
                    ),
                    'font' => array(//fuente
                        'bold' => true,
                        'name' => 'Verdana',
                        'size' => 8
                    )
            )
        );

        //margenes
        $n_margin = 0.5 / 2.54; // 0.5 centimetros
        //Crea una nueva hoja
        $objPHPExcel->createSheet($n_sheet);
        //Titulo de la hoja
        $objPHPExcel->setActiveSheetIndex($n_sheet)->setTitle('Mangueras');
        //Orientación de la página
        $objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        //Tamaño de papel
        $objPHPExcel->getActiveSheet()->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A3);
        //Agrega estilos a las celdas
        $objPHPExcel->getActiveSheet()->setSharedStyle($o_titulo, 'A1');
        $objPHPExcel->getActiveSheet()->setSharedStyle($o_titulo, 'A2');
        $objPHPExcel->getActiveSheet()->setSharedStyle($o_titulo, 'A3');
        $objPHPExcel->getActiveSheet()->setSharedStyle($o_encabezados, 'A4:K5');
        //Define los margenes (arriba,abajo,izquierda,derecha)
        $objPHPExcel->getActiveSheet()->getPageMargins()->setTop($n_margin);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($n_margin);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($n_margin);
        $objPHPExcel->getActiveSheet()->getPageMargins()->setRight($n_margin);

        $s_plaza = $_REQUEST['plaza'];

        $s_titulo1 = "GAS DEL PACIFICIO, SA. DE CV.";
        $s_titulo2 = "MANGUERAS";
        $s_titulo3 = "PLAZA - ".$s_plaza;

        $objPHPExcel->setActiveSheetIndex($n_sheet)
            ->mergeCells('A1:J1')
            ->setCellValue('A1', $s_titulo1)
            ->mergeCells('A2:J2')
            ->setCellValue('A2', $s_titulo2)
            ->mergeCells('A3:J3')
            ->setCellValue('A3', $s_titulo3)
            ->setCellValue('A4', 'Plaza')
            ->setCellValue('B4', 'Canal de venta')
            ->setCellValue('C4', 'Manguera')
            ->setCellValue('D4', 'Descripción')
            ->setCellValue('E4', 'Num. Economico')
            ->setCellValue('F4', 'Num. Estación')
            ->setCellValue('G4', 'Num. Bascula')
            ->setCellValue('H4', 'Num. Red')
            ->setCellValue('I4', 'Sub-red')
            ->setCellValue('J4', 'Num. Bomba');

        $a_result = json_decode($_REQUEST['records'], true);
        $n_celda = 5;
        $fh = new DateTime();
        $fh->sub(new DateInterval('P10Y'));
        
        if (count($a_result) > 0) {
                //$a_result = $this->parsearCakeQueryResultArray($a_result);
            foreach ($a_result as $a_key) {
                $objPHPExcel->setActiveSheetIndex($n_sheet)
                    ->setCellValue('A' . $n_celda, $a_key['plaza'])
                    ->setCellValue('B' . $n_celda, $a_key['rubro_venta'])
                    ->setCellValue('C' . $n_celda, $a_key['num_manguera'])
                    ->setCellValue('D' . $n_celda, $a_key['descrip_manguera'])
                    ->setCellValue('E' . $n_celda, $a_key['num_eco'])
                    ->setCellValue('F' . $n_celda, $a_key['num_estacion'])
                    ->setCellValue('G' . $n_celda, $a_key['num_bascula'])
                    ->setCellValue('H' . $n_celda, $a_key['num_red'])
                    ->setCellValue('I' . $n_celda, $a_key['sub_red'])
                    ->setCellValue('J' . $n_celda, $a_key['num_bomba']);

                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosL, 'A'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosL, 'B'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosL, 'C'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosL, 'D'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'E'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'F'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'G'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'H'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'I'. $n_celda);
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosR, 'J'. $n_celda);
                $objPHPExcel->getActiveSheet()->getStyle('F'.$n_celda)->getNumberFormat()->setFormatCode('#,##');

                $n_celda++;
            }
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);

        $objPHPExcel->setActiveSheetIndex(0);
        $objPHPExcel->removeSheetByIndex($objPHPExcel->getSheetCount() - 1);

        $s_file = 'Mangueras.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . str_replace(" ", "_", $s_file) . '"');
        header('Cache-Control: max-age=0');
        $s_ruta = "files/" . str_replace(" ", "_", $s_file);
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($s_ruta);
        $success = true;
        $records = $s_ruta;
        // $this->success = 1;
        // $this->records = '{ruta:"' . $s_ruta . '"}';

        return $this->asJson(array(
            "success" => $success,
            "message" => "Reporte enviado",
            "records" => $records
        ));
    }

    public function descarga()
    {
        try {
            $s_file = $_REQUEST['file'];
            if (file_exists($s_file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename=' . basename($s_file));
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($s_file));
                ob_clean();
                flush();
                readfile($s_file);
                unlink($s_file);
                exit();
            }
        } catch (Exception $o_ex) {
            $s_error = str_replace("'", "", $o_ex->getMessage());
            $s_error = str_replace('"', "", $s_error);
            $this->message = $s_error;
        }
        exit;
    }


    // public function descarga()
    // {
    //     $s_file = $_REQUEST['file'];
    //     if (file_exists($s_file)) {
    //         header('Content-Description: File Transfer');
    //         header('Content-Type: text/csv');
    //         header('Content-Disposition: attachment; filename=' . basename($s_file));
    //         header('Expires: 0');
    //         header('Cache-Control: must-revalidate');
    //         header('Pragma: public');
    //         header('Content-Length: ' . filesize($s_file));
    //         ob_clean();
    //         flush();
    //         readfile($s_file);
    //         unlink($s_file);
    //         exit();
    //     }
    // }
}
