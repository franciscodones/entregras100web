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
                $sQuery = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plazas,
                rubros_ventas.nombre AS rubro_venta, 
                IF(mangueras.rubro_venta_id = 4,(SELECT nom_estac FROM estaciones 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS nom_estac,
                IF(mangueras.rubro_venta_id = 4,(SELECT permiso FROM estaciones 
                    LEFT JOIN permisos ON permisos.id_permiso = estaciones.permiso_id 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS permiso,
                    IF(mangueras.rubro_venta_id = 4,(SELECT tiene_perm FROM estaciones 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS tiene_perm,
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
            $sQuery = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plazas,
                    rubros_ventas.nombre AS rubro_venta, 
                    IF(mangueras.rubro_venta_id = 4,(SELECT nom_estac FROM estaciones 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS nom_estac,
                    IF(mangueras.rubro_venta_id = 4,(SELECT permiso FROM estaciones 
                    LEFT JOIN permisos ON permisos.id_permiso = estaciones.permiso_id 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS permiso,
                    IF(mangueras.rubro_venta_id = 4,(SELECT tiene_perm FROM estaciones 
                    WHERE estaciones.num_estac = mangueras.num_estacion),'') AS tiene_perm,
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
        $query = "SELECT mangueras.*, permisos.nompla_est AS nombre_planta, plazas.nom_plaza AS plazas,
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

     /**
    * Carga la informacion de permisos dependiendo la planta seleccionada
    * @return JsonResponse
    */
    public function Permisos()
    {
        $oConexion = $this->getConexion('mangueras');
        $planta_id = $_REQUEST['planta_id'];

        $msg = "";
        $success = "";

        // obtiene los permisos correspondientes
        $sQuery = "SELECT * FROM permisos WHERE planta_id = ?";
        $rQuery = $oConexion->query($sQuery, [
            $planta_id
        ]);
        
        if (count($rQuery) > 0) {
            $records = $rQuery;
            $success = true;
        } else {
            $msg = "No contienen permisos la planta!";
            $success = false;
            $records = [];
        }

        return $this->asJson(array(
            "success" => $success,
            "message" => $msg,
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

    public function searchNumEstacion()
    {
        $oConexion = $this->getConexion('mangueras');
        $numEstacion = $_REQUEST['num_estacion'];
        $success = "";
        $msg = "";

        // Busca si existe la estacion
        $sQuery = "SELECT estaciones.*, permisos.permiso 
            FROM estaciones 
            LEFT JOIN permisos ON permisos.id_permiso = estaciones.permiso_id
            WHERE estaciones.num_estac = ? AND estatus = 1";
        $rQuery = $oConexion->query($sQuery, [
            $numEstacion
        ]);

        if (count($rQuery) > 0) {
            $records = $rQuery;
            $success = true;
            $msg = "Existe el numero de estacion";
        } else {
            $records = [];
            $success = false;
            $msg = "No existe el numero de estacion";
        }

        return $this->asJson(array(
            "success" => $success,
            "message" => $msg,
            "records" => $records,
            "metadata" => array(
                "total_registros" => count($records)
            )
        ));
    }


    /**
    * Busca la estación en db2
    * @return JsonResponse
    */
    public function searchPermiso()
    {
        $oConexion = $this->getConexion('mangueras');
        $success = "";
        $msg = "";
        // $plaza_id = $_REQUEST['plaza_id'];
        // $cvecia = $_REQUEST['cvecia'];
        $permiso = $_REQUEST['permiso'];

        // Verifica si existe el permiso
        $sQuery = "SELECT * FROM permisos WHERE permiso = ?";
        $rQuery = $oConexion->query($sQuery, [
            $permiso
        ]);

        if (count($rQuery) > 0) {
            $msg = "Existe el permiso, verifique por favor!";
            $records = $rQuery;
            $success = true;
        }

        return $this->asJson(array(
            "success" => $success,
            "message" => $msg,
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
            // $records = $records[0];
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
                // $permisos = json_decode($_REQUEST['info'], true);

                $query = "SELECT * FROM mangueras WHERE num_estacion = ? AND num_bomba = ? AND planta_id = ? 
                    AND mangueras.estatus = 1";
                $rQuery = $oConexion->query($query, [
                    $records['num_estacion'],
                    $records['num_bomba'],
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
                            $query = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, 
                        rubro_venta, num_manguera, descrip_manguera, descrip_rubro_venta, num_eco, num_bascula, 
                        num_red, num_bomba, num_estacion, sub_red, fecha_alta, fecha_baja, estatus) 
                        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                            $permi = "INSERT INTO permisos (permiso, plaza_id, plaza, cvecia, pla_est, planta_id, estac_id, 
                        num_estac, tiene_perm, nompla_est, direccion) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

                            $updatePermiso = "UPDATE permisos SET estac_id = ? WHERE id_permiso = ?";

                            $estacion = "INSERT INTO estaciones (empres_id, plaza_id, plaza, cvecia, num_estac, nom_estac, 
                        tiene_perm, permiso_id, planta_id, estatus) VALUES (?,?,?,?,?,?,?,?,?,?)";

                            $folio = "INSERT INTO folio_servicios (plaza_id, plaza, cvecia, rubro_venta, num_Estacion,
                        folio_servicio) VALUES (?,?,?,?,?,?)";

                            $sQuery = "INSERT INTO mangueras (plaza_id, plaza, cvecia, planta_id, rubro_venta_id, 
                            cvecia_id, rubro_venta, num_manguera, descrip_manguera, descrip_rubro_venta, num_eco, 
                            num_bascula, num_red, num_bomba, num_estacion, sub_red, fecha_alta, fecha_baja, servidor)
                            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

                        if (empty($_REQUEST['info']) == false) {
                            $permisos = json_decode($_REQUEST['info'], true);
                        }

                        $querySelect = "SELECT empresa_id, empresas.* FROM plazas 
                        LEFT JOIN empresas ON empresas.id_empresa = plazas.empresa_id
                        WHERE plazas.id_plaza = ? AND empresas.activa = 1";
                        $rQueryS = $oConexion->query($querySelect, [
                            $records['plaza_id']
                        ]);

                        if (!empty($rQueryS)) {
                                $empresa_id = $rQueryS[0]['empresa_id'];

                            foreach ($conexiones as $key => $value) {
                                try {
                                        $value->begin();
                                        $manguerasReInsertar->begin();

                                    if (isset($permisos) != false) {
                                        $sQuery = $value->query($permi, [
                                        $permisos['permiso'],
                                        $permisos['plaza_id'],
                                        $permisos['plaza'],
                                        $permisos['cvecia'],
                                        $permisos['pla_est'],
                                        $permisos['planta_id'],
                                        "",
                                        $permisos['num_estac'],
                                        $permisos['tiene_perm'],
                                        $permisos['nompla_est'],
                                        ""
                                        ]);

                                        $id_permiso = $oConexion->lastInsertId();
                                    }

                                    if (isset($records['est_existe']) != true) {
                                        //agrega la estacion
                                        $eQuery = $value->query($estacion, [
                                            $empresa_id,
                                            $records['plaza_id'],
                                            $records['plaza'],
                                            $records['cvecia'],
                                            $records['num_estacion'],
                                            (isset($records['nom_estac']) == true ? $records['nom_estac'] : $permisos['nompla_est']),
                                            (isset($records['tiene_perm']) == true ? $records['tiene_perm'] : $permisos['tiene_perm']),
                                            (isset($records['permiso_id']) == true ? $records['permiso_id'] : $id_permiso),
                                            $records['planta_id'],
                                            "1"
                                        ]);

                                        //obtengo el ultimo id agregado de la estacion.
                                        $id_estacion = $oConexion->lastInsertId();
                                        
                                        //Se agrega en la tabla de folio
                                        $fQuery = $value->query($folio, [
                                            $records['plaza_id'],
                                            $records['plaza'],
                                            $records['cvecia'],
                                            $records['rubro_venta'],
                                            $records['num_estacion'],
                                            "0"
                                        ]);
                                    }

                                        
                                    if (isset($permisos) != false) {
                                        $rUpdate = $value->query($updatePermiso, [
                                            $id_estacion,
                                            $id_permiso
                                        ]);
                                    }

                                    //si la key es diferente a la db de mangueras, entra a ingresar informacion
                                    if ($key != "mangueras") {
                                        //si la variable permiso_id esta vacia entra y busca el ultimo permiso de
                                        //la bd de mangueras.
                                        if (isset($records['permiso_id']) != true) {
                                            $qP = "SELECT MAX(id_permiso)AS id_permiso FROM permisos";
                                            $rP = $oConexion->query($qP);
                                        }

                                        //obtiene el ultimo id_estacion de la db mangueras.
                                        $qE = "SELECT MAX(id_estac)AS id_estacion FROM estaciones";
                                        $rE = $oConexion->query($qE);

                                        //verifica si esta vacio, si esta vacio entra
                                        if (isset($records['permiso_id']) != true) {
                                            //actualiza la informacion en permisos y estaciones
                                            
                                            $uP = "UPDATE permisos SET id_permiso = ?, estac_id = ? WHERE num_estac = ?";
                                            $rUp = $value->query($uP, [
                                                $rP[0]['id_permiso'],
                                                $rE[0]['id_estacion'],
                                                $records['num_estacion']
                                            ]);

                                            $uE = "UPDATE estaciones SET id_estac = ?, permiso_id = ?
                                                WHERE num_estac = ?";
                                            $rUe = $value->query($uE, [
                                                $rE[0]['id_estacion'],
                                                $rP[0]['id_permiso'],
                                                $records['num_estacion']
                                            ]);
                                        } else {
                                            //solo actualiza el id_estac
                                            
                                            $uE = "UPDATE estaciones SET id_estac = ? WHERE num_estac = ?";
                                            $rUe = $value->query($uE, [
                                                $rE[0]['id_estacion'],
                                                $records['num_estacion']
                                            ]);
                                        }
                                    }
                                
                                    //agrega informacion a mangueras.
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
                                        (isset($records['num_eco']) == true ?  $records['num_eco'] : 0),
                                        (isset($records['num_bascula'])== true ? $records['num_bascula'] : 0),
                                        (isset($records['num_red']) == true ? $records['num_red'] : 0),
                                        (isset($records['num_bomba']) == true ? $records['num_bomba'] : 0),
                                        (isset($records['num_estacion']) == true  ? $records['num_estacion'] : 0),
                                        (isset($records['sub_red']) == true ? $records['sub_red'] : 0),
                                        date("Y-m-d"),
                                        '0000-00-00',
                                        1
                                    ]);
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

                                    // $records['clientId'] = $records['id_manguera'];
                                    // $records['id_manguera'] = $manguerasReInsertar->lastInsertId();
                                    // $records['estatus'] = 1;
                                    // $data = [$data];
                                }

                                $value->commit();
                                $manguerasReInsertar->commit();
                            }
                                // $records = [$records];

                                $msg = "<center>Se agrego correctamente!</center>";
                                $success = true;
                        } else {
                            $msg = "<center>La empresa se encuentra desactivada, verifique porfavor</center>";
                            $success = false;
                        }
                    } else {
                        $msg = "<center>Ya existe la bomba <b>".$records['num_bomba']." </b> en la estación <b>
                        ".$records['num_estacion']." </b>, verifique porfavor</center>";
                        $success = false;
                    }
                } else {
                    $msg = "<center>Ya existe el numero de estación <b>".$records['num_estacion']." </b> y el 
                        numero de bomba <b>".$records['num_bomba']."</b> en la Planta <b>"
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

    /*Actualizara la información de permisos*/
    public function update()
    {
        $records  = json_decode($_REQUEST['records'], true);

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

        foreach ($conexiones as $key => $value) {
            try {
                $value->begin();
                $manguerasReInsertar->begin();
                $iPermiso = "INSERT INTO permisos (permiso, plaza_id, plaza, cvecia, pla_est, planta_id, estac_id,
                    num_estac, tiene_perm, nompla_est, direccion) VALUES (?,?,?,?,?,?,?,?,?,?,?)";

                $uEstacion = "UPDATE estaciones SET tiene_perm = ?, permiso_id = ? WHERE num_estac = ?";

                $uFolio = "UPDATE folio_servicios SET folio_servicio = ? WHERE num_Estacion = ?";

                $uPermiso = "UPDATE permisos SET id_permiso = ? WHERE num_estac = ?";

                if ($records['rubro_venta_id'] == 4) {
                    $rubro_venta = 'E';
                }

                $qE = "SELECT id_estac FROM estaciones WHERE num_estac = ?";
                $rE = $oConexion->query($qE, [
                    $records['num_estacion']
                ]);

                //verificar cuando se agregue una planta.
                $rPermiso = $value->query($iPermiso, [
                    $records['permiso'],
                    $records['plaza_id'],
                    $records['plaza'],
                    $records['cvecia'],
                    $rubro_venta,
                    '0',
                    $rE[0]['id_estac'],
                    $records['num_estacion'],
                    "1",
                    $records['nom_estac'],
                    ""
                ]);

                $id_permiso = $oConexion->lastInsertId();

                $rEstacion = $value->query($uEstacion, [
                    '1',
                    $id_permiso,
                    $records['num_estacion']
                ]);

                if ($key != "mangueras") {
                    $qP = "SELECT MAX(id_permiso)AS id_permiso FROM permisos";
                    $rP = $oConexion->query($qP);

                    $rUp = $value->query($uPermiso, [
                        $rP[0]['id_permiso'],
                        $records['num_estacion']
                    ]);

                    $uE = "UPDATE estaciones SET tiene_perm = ?, permiso_id = ? WHERE num_estac = ?";
                    $rUe = $value->query($uE, [
                        '1',
                        $rP[0]['id_permiso'],
                        $records['num_estacion']
                    ]);
                }

                $rFolioServicio = $value->query($uFolio, [
                    '0',
                    $records['num_estacion']
                ]);
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
            }
            $value->commit();
            $manguerasReInsertar->commit();
        }

        $success = true;
        $msg = "<center>Se actualizo correctamente</center>";

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
        $oConexion = $this->getConexion('mangueras');
        $records = json_decode($_REQUEST["records"], true);
        $success = "";
        $msg = "";
        // $records = $records[0];
        
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

        foreach ($conexiones as $key => $value) {
            try {
                $querySelect = "SELECT id_manguera FROM mangueras WHERE plaza_id = ? AND planta_id = ? 
                    AND rubro_venta_id = ? AND num_manguera = ?";
                $rQuerySelect = $value->query($querySelect, [
                    $records['plaza_id'],
                    $records['planta_id'],
                    $records['rubro_venta_id'],
                    $records['num_manguera']
                ]);
                 
                $query = "UPDATE mangueras SET estatus = ?, fecha_baja = ? WHERE id_manguera = ?";
                    $rQuery = $value->query($query, [
                    '0',
                    date("Y-m-d"),
                    $rQuerySelect[0]['id_manguera']
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
            ->setCellValue('J4', 'Num. Bomba')
            ->setCellValue('K4', 'Permiso')
            ;

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
                    ->setCellValue('J' . $n_celda, $a_key['num_bomba'])
                    ->setCellValue('K' . $n_celda, $a_key['permiso']);

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
                $objPHPExcel->getActiveSheet()->setSharedStyle($o_textitosL, 'K'. $n_celda);
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
        $objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(25);

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
