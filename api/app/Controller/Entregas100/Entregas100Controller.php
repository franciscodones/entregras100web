<?php

App::uses('AppGaseraController', 'Controller');

class Entregas100Controller extends AppGaseraController {

    public function servicios() {

        $s_url = trim(Router::url('/', true));

        echo "1.-<a target='_blank' href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Comunicacion&unidad=600&longitud=100&direccion=1&comando=E&informacion={algo:aaa}&checksum=1233&tipo=T'>Comunicacion</a><br>";
        echo "2.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Sesion&unidad=600&nip=281963&nip2=468532'>Sesion</a><br>";
        echo "3.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Sesionout&unidad=600'>Cerrar Sesion</a><br>";
        echo "4.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Posicion&unidad=600&latitud=1.02&longitud=-100.25&rssi=15'>"
        . "Posicion</a><br>";
        echo "5.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Configuracionunidad&unidad=600'>"
        . "Configuracion Unidad</a><br>";
        echo "6.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Banco&unidad=600'>"
        . "Bancos</a><br>";
        echo "7.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Formapago&unidad=600'>"
        . "Formas de Pago</a><br>";
        echo "8.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Puntos&unidad=600'>"
        . "Puntos</a><br>";
        echo "9.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Puntosoro&unidad=600'>"
        . "Puntos oro</a><br>";
        echo "10.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Litrogas&unidad=600'>"
        . "Litrogas</a><br>";
        echo "11.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Llenafacil&unidad=600'>"
        . "LlenaFacil</a><br>";
        echo "12.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Tipoventa&unidad=600'>"
        . "Tipo de Venta</a><br>";
        echo "13.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Motivonosurtido&unidad=600'>"
        . "Motivos no surtido</a><br>";
        echo "14.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Alarma&unidad=600&alarma_id=1&numero_control=1520&servicio=12&litros=120.20&litros_no=0&presion_referencia=12&presion_mangerazo=0&fecha=2016-08-17&hora=17:52:00'>"
        . "Logs Alarma</a><br>";
        echo "15.-<a target='_blank' "
        . "href=' " . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Calle&unidad=600'>"
        . "Calles</a><br>";
        echo "16.-<a target='_blank' "
        . "href=' " . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Colonia&unidad=600'>"
        . "Colonias</a><br>";
        echo "17.-<a target='_blank' "
        . "href=' " . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Solicitarservicio&unidad=600&tipo_pedido=A'>"
        . "Clientes Servicios Online</a><br>";
        echo "18.-<a target='_blank' "
        . "href=' " . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Solicitarlista&unidad=600'>"
        . "Clientes Lista</a><br>";
        echo "19.-<a target='_blank' "
        . "href=' " . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Solicitarpadron&unidad=600&pagina=1'>"
        . "Clientes Padron</a><br>";
        echo "20.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Iniciofinruta&unidad=600&nip1=1452&nip2=2546&porcentaje_tanque=50&porcentaje_tanque_carburacion=80&odometro=1520&horometro=14579&totalizador=19548&carga_bateria=90&tipo=1'>"
        . "Inicio de Ruta</a><br>";
        echo "21.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Iniciofinruta&unidad=600&nip1=1452&nip2=2546&porcentaje_tanque=50&porcentaje_tanque_carburacion=80&odometro=1520&horometro=14579&totalizador=19548&carga_bateria=90&tipo=2'>"
        . "Fin de Ruta</a><br>";
        echo "22.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Motivorecirculacion&unidad=600'>"
        . "Motivos Recirculacion</a><br>";
        echo "23.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Nosurtido&unidad=600&numero_control=1&motivo_id=10&porcentaje_tanque=15&fecha_compromiso=2016-08-31&horasurtido=2016-08-25%2013:00:00&capacidad=300'>"
        . "No surtido</a><br>";
        echo "24.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Operador&unidad=600'>"
        . "Operador</a><br>";
        echo "25.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Surtido&unidad=600'>"
        . "Surtido</a><br>";
        echo "26.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Surtidorecirculacion&unidad=600&motivo_id=1&litros=100.00&numero_servicio=52&hora_inicio=10:08:50&hora_final=10:10:50'>"
        . "Surtido recirculacion</a><br>";
        echo "27.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Surtidorevision&unidad=600&numero_control=10245&litros=100.00&numero_servicio=52&hora_inicio=10:08:50&hora_final=10:10:50'>"
        . "Surtido revision</a><br>";
        echo "28.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Capacidad&unidad=600'>"
        . "Capacidad</a><br>";
        echo "29.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Tipocliente&unidad=600'>"
        . "Tipos Cliente</a><br>";
        echo "30.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Combinacioncliente_pago&unidad=600'>"
        . "Combinacion clientes con formas de pago</a><br>";
        echo "31.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Combinacionforma_pago&unidad=600'>"
        . "Combinacion formas de pago con formas de pago</a><br>";
        echo "32.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Alarmas&unidad=600'>"
        . "Alarmas</a><br>";
        echo "33.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Configuracion&unidad=600'>"
        . "Configuraciones</a><br>";
        echo "34.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Combinacionforma_plaza&unidad=600'>"
        . "Combinacion formas de pago y plaza</a><br>";
        echo "35.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Error&unidad=600&fecha=2017-01-27&hora=16:00:00&error=error'>"
        . "Error</a><br>";
        echo "36.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Tarifa&unidad=600'>"
        . "Tarifas</a><br>";
        echo "37.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Presion&unidad=600&numero_control=1&servicio=1&litros=100&presion=12&fecha=" . date('Y-m-d') .
        "&hora=" . date('H:i:s') . "&tipo=1'>"
        . "Presion</a><br>";
        echo "38.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Conciliarservicio&"
        . "unidad=600&version=1.20&conexion=wifi&"
        . "servicios={\"servicios\":[{\"numero_control\":100,\"numero_servicio\":1},{\"numero_control\":100,\"numero_servicio\":2}]}'>"
        . "Conciliar</a><br>";

        echo "39.-<a target='_blank' "
        . "href='" . $s_url . "APPGasera/?key=wPi_BZ2EFT4i.&funcion=Procedimiento&"
        . "unidad=600&version=1.20&conexion=wifi'>"
        . "Procedimientos</a><br>";
        exit();
    }

    /**
     * Maneja el request instanciando el controller adecuado, ejecutando la funcion adecuado y retornando
     * la respuesta de acuerdo al parametro `funcion`.
     * @return [type] [description]
     */
    public function index() {
        $aDatos = $this->request->data;
        $respuesta = null;

        // si no existen los params generales
        if (
            empty($aDatos['key']) ||
            empty($aDatos['funcion']) ||
            empty($aDatos['unidad']) ||
            empty($aDatos['version']) ||
            empty($aDatos['conexion']) ||
            empty($aDatos["empresa"])
        ) {
            return $this->asJson(array(
                "success" => false,
                "message" => "Numero de parametros erroneo al iniciar busqueda de controlador"
            ));
        }

        $sKey = $aDatos['key'];
        $sVersion = $aDatos['version'];
        $sConexion = $aDatos['conexion'];
        $sFuncion = $aDatos['funcion'];
        $nUnidad = $aDatos['unidad'];
        $nEmpresa = $aDatos['empresa'];

        // verifica que la unidad exista
        try {
            // verifica que la unidad exista en la plaza
            $aUnidad = $this->getUnidad($nUnidad, $nEmpresa);
            if ($aUnidad == null) {
                return $this->asJson(array(
                    "success" => false,
                    "message" => "La unidad " . $nUnidad . " no se encuentra dada de alta en la plaza"
                ));
            }
        } catch(Exception $ex) {
            $this->logExceptionTrace("entregas100", $ex);
            return $this->asJson(array(
                "success" => false,
                "message" => "Error interno. " . $ex->getMessage()
            ));
        }

        $sControllerFuncion = strtolower($sFuncion) . "_fn";
        $sController = $sFuncion . 'AppGaseraController';

        // verifica que el controlador exista
        if (!file_exists(__DIR__ . "/" . $sController . ".php")) {
            $respuesta = $this->asJson(array(
                "success" => false,
                "message" => "Error interno. El controlador " . $sController . " no existe"
            ));
            // deja un log del response antes de retornarlo
            $this->logWS(
                $sKey,
                $aUnidad['id'],
                $sControllerFuncion,
                $aUnidad == null ? 0 : 1,
                json_encode($aDatos, JSON_NUMERIC_CHECK),
                $respuesta,
                $sVersion,
                $sConexion
            );
            return $respuesta;
        }

        App::uses($sController, 'Controller');
        $oController = new $sController($this->request, $this->response);

        // verifica que el metodo exista
        if (!method_exists($oController, $sControllerFuncion)) {
            $respuesta = $this->asJson(array(
                "success" => false,
                "message" => "Error interno. La funcion " . $sControllerFuncion . " no existe en el controlador " . $sController
            ));
            // deja un log del response antes de retornarlo
            $this->logWS(
                $sKey,
                $aUnidad['id'],
                $sControllerFuncion,
                $aUnidad == null ? 0 : 1,
                json_encode($aDatos, JSON_NUMERIC_CHECK),
                $respuesta,
                $sVersion,
                $sConexion
            );
            return $respuesta;
        }

        // estas variables aun no se eliminan debido a que se necesita cambiar
        // en cada uno de los controllers antes de elimninarlo
        $this->n_numero_clientes = 0;
        $this->n_parametros_ws = 6;
        $this->n_unidad_id = $aUnidad['id'];
        $this->s_plaza = $aUnidad['plaza'];
        $this->n_zona = $aUnidad['zona'];
        $this->n_zona_id = $aUnidad['zona_id'];

        $respuesta = $oController->$sControllerFuncion($this, $aDatos, $aUnidad);
        // deja un log del response antes de retornarlo
        $this->logWS(
            $sKey,
            $aUnidad['id'],
            $sControllerFuncion,
            $aUnidad == null ? 0 : 1,
            json_encode($aDatos, JSON_NUMERIC_CHECK),
            $respuesta,
            $sVersion,
            $sConexion
        );
        return $respuesta;
    }
}
