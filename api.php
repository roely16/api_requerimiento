<?php
        
    error_reporting(E_ERROR | E_PARSE);

    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
    header("Allow: GET, POST, OPTIONS, PUT, DELETE");

    require_once __DIR__ . '/vendor/autoload.php';

    include $_SERVER['DOCUMENT_ROOT'] . '/apis/api_requerimiento/sap/functions.php';

    class Api extends Rest{

        public $dbConn;

		public function __construct(){

			parent::__construct();

			$db = new Db();
			$this->dbConn = $db->connect();

        }

        public function generar_requerimiento(){

            $interlocutor = $this->validateParameter('interlocutor', $this->param['interlocutor'], STRING);

            $sap = new SAP_Function();
            $result = $sap->obtenerIngresos($interlocutor, $fecha);

            $sap_return = $result;

            $this->returnResponse(SUCCESS_RESPONSE, $sap_return);


            /*    
            $query = "  SELECT 
                            interlocutor, nota, substr(nombre, 1, 67) AS nombre_part1, 
                            substr(nombre, 68, 134) AS nombre_part2,  
                            substr(direccion, 1, 67) AS direccion_part1,
                            substr(direccion, 68, 134) AS direccion_part2, 
                            nit, 
                            to_char(total, 'FM999,999,990.00') AS total_pagar,
                            '15 DE MARZO DE 2020' AS fecha_corte, 
                            '30 DE ABRIL DE 2020' AS fecha_vence,
                            '15.03.2020' AS fecha_referencia, 
                            '30.03.2020' AS vencimiento,
                            '1er. Trimestre de 2020' AS periodo_fiscal
                        FROM canal_1002
                        WHERE interlocutor = '$interlocutor'";

            $stid = oci_parse($this->dbConn, $query);
            oci_execute($stid);

            $result = oci_fetch_array($stid, OCI_ASSOC);

            if (empty($result)) {
                
                $this->throwError(100, "No se han encontrado datos para el interlocutor ingresado");

            }

            $query = "  SELECT 
                            interlocutor_predio, 
                            SUBSTR(direccion_predio,1,26) AS direccionPredio1, 
                            SUBSTR(direccion_predio,27,26) AS direccionPredio2,
                            matricula, 
                            SUBSTR(registro,1,19) AS registro, 
                            to_char(valor_predio,'FM999,999,990.00') AS valorPredio,  
                            to_char(tasa,'FM999,999,990.00') AS tasa, 
                            to_char(impuesto,'FM999,999,990.00') AS cargos, 
                            to_char(saldo,'FM999,999,990.00') AS saldo, 
                            to_char(multa,'FM999,999,990.00') AS multas,
                            to_char(saldo_convenio,'FM999,999,990.00') AS saldoConvenio, 
                            to_char(total_predio,'FM999,999,990.00') AS totalInmueble
                        FROM canal_2
                        WHERE interlocutor = '$interlocutor'";

            $stid = oci_parse($this->dbConn, $query);
            oci_execute($stid);

            $predios = [];

            while($data = oci_fetch_array($stid, OCI_ASSOC)){
                
                $predios [] = $data;

            }

            $result["predios"] = $predios;

            //$this->returnResponse(SUCCESS_RESPONSE, $result);

            $mpdf = new \Mpdf\Mpdf(['orientation' => 'L', 'setAutoTopMargin' => 'stretch']);
    
            $obj_result = (object) $result;

            //$mpdf->WriteHTML($content);

            $html = '

                <style>
                    p {
                        margin: 0;
                        padding: 0;
                    }

                    body {
                        font-family: "Courier Roman", Courier, monospace;
                        font-size: 12px
                    }

                    table {
                        border-collapse: collapse;
                    }

                    table, th, td {
                        border: 1px solid black;
                    
                    }

                </style>

                <htmlpageheader name="MyHeader1">
                    <div>
                        <div style="display: table; clear: both;">
                            <div style="float: left; width: 15%">
                                <img width="150" src="logo.png" alt="">
                            </div>
                        <div>
                            <div style="text-align: center;">
                                <span style="font-size: 16px; text-align: center;"><strong>REQUERIMIENTO DE PAGO NO. ' .$obj_result->NOTA . '</strong></span>
                                <p style="font-size: 16px; margin-bottom: 5px">Fecha Referencia: ' . $obj_result->FECHA_REFERENCIA . '</p>
                            </div>
                            <p>NUMERO DE CUENTA: '.$obj_result->INTERLOCUTOR.'</p>
                            <p style="margin-bottom: 5px">NOMBRE DEL CONTRIBUYENTE: '.$obj_result->NOMBRE_PART1.' '.$obj_result->NOMBRE_PART2.'</p>
                            <p>DOMICILIO: '.$obj_result->DIRECCION_PART1.' '.$obj_result->DIRECCION_PART2.'</p>
                        </div>
                    </div>
                </htmlpageheader>

                <htmlpagefooter name="MyFooter1">
                    <hr>
                    <p><strong>NOTA: EL TOTAL A CANCELAR PUEDE VARIAR DESPÚES DE LA FECHA DE REFERENCIA, POR ACTUALIZACIÓN OPERADA EN LA BASE DE DATOS.</strong></p>
                    <div style="display: table; clear: both; margin-top: 5px">
                        <div style="float: left; width: 33%">
                            <p>Fecha de vencimiento: '.$obj_result->VENCIMIENTO.'</p>
                        </div>
                        <div style="float: left; width: 33%; text-align: center">
                            <p>Operador Responsable: <strong>MAIL.MUNI</strong></p>
                        </div>
                        <div style="float: left; width: 33%; text-align: right">
                            <p>PAGINA {PAGENO} DE {nbpg}</p>
                        </div>
                    </div>
                </htmlpagefooter>

                <sethtmlpageheader name="MyHeader1" value="on" show-this-page="1" />
                <sethtmlpagefooter name="MyFooter1" value="on" show-this-page="1" />';
                


            $html.=  '
                    <br>
                    <div class="row">
                        <div class="col-12">
                            <table width="100%" style="font-size: 10px; margin-top: 65px">
                                <thead>
                                    <tr >
                                        <th height="40">INTERLOCUTOR</th>
                                        <th>MATRICULA</th>
                                        <th>DIRECCIÓN DEL INMUEBLE</th>
                                        <th>REGISTRO</th>
                                        <th>VALOR TOTAL</th>
                                        <th>TASA</th>
                                        <th>CARGO TRIMESTRAL</th>
                                        <th>SALDO</th>
                                        <th>MULTA</th>
                                        <th>CONVENIO</th>
                                        <th>TOTAL</th>
                                    </tr>
                                </thead>
                                <tbody>';

            foreach ($predios as $predio) {

                $obj_predio = (object) $predio;

                $html.= ' <tr>
                <td width="8%" height="40">'.$obj_predio->INTERLOCUTOR_PREDIO.'</td>
                <td width="8%">'.$obj_predio->MATRICULA.'</td>
                <td width="15%">'.$obj_predio->DIRECCIONPREDIO1.' '.$obj_predio->DIRECCIONPREDIO2.'</td>
                <td>'.$obj_predio->REGISTRO.'</td>
                <td align="right">'.$obj_predio->VALORPREDIO.'</td>
                <td align="right">'.$obj_predio->TASA.'</td>
                <td width="12%" align="right">'.$obj_predio->CARGOS.'</td>
                <td align="right">'.$obj_predio->SALDO.'</td>
                <td align="right">'.$obj_predio->MULTAS.'</td>
                <td align="right">'.$obj_predio->SALDOCONVENIO.'</td>
                <td align="right">'.$obj_predio->TOTALINMUEBLE.'</td>
                </tr>';


            }

                        
            $html.=  '</tbody>
                            </table>
                            <table style="margin-left: 515px">
                                <tr>
                                    <td height="40" width="322" style="font-size: 16px;"><strong>TOTAL A CANCELAR</strong></td>
                                    <td align="right" width="250" style="font-size: 16px"><strong>'.$obj_result->TOTAL_PAGAR.'</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
            ';

            //$mpdf->WriteHTML($html);


            //$mpdf->Output();

            */

        }

    }

?>