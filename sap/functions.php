<?php 

    include "connection.php";

    class SAP_Function
    {
        public $sap_conn;
        public $login_data;

		public function __construct(){

			// parent::__construct();

			$conn = new Connection();
            $this->sap_conn = $conn->connect();

        }

        public function test_connection(){

            $conn = $this->sap_conn;

            return $conn;

        }

        public function obtenerIngresos($interlocutor, $fecha = "31.12.2020"){

            $id_function = "ZPSCD_FM_CAJA_017";

            $result = $this->sap_conn->callFunction($id_function,
                        array(
                            array("IMPORT","GPART", $interlocutor),
                            array("IMPORT","PSOBTYP_VTREF", "IUSI"),
                            array("IMPORT","DATE", $fecha), 
                            array("EXPORT", "BETRZ"),
                            array("EXPORT", "ZZMONTOTOTAL"),
                        ));

            return $result;

        }
    }
    
?>