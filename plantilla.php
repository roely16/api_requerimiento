<?php 

    require_once __DIR__ . '/vendor/autoload.php';

    $registros = [1];

    $mpdf = new \Mpdf\Mpdf(['orientation' => 'L', 'setAutoTopMargin' => 'stretch']);
    
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
                        <span style="font-size: 16px; text-align: center;"><strong>REQUERIMIENTO DE PAGO NO. </strong></span>
                        <p style="font-size: 16px; margin-bottom: 5px">Fecha Referencia: </p>
                    </div>
                    <p>NUMERO DE CUENTA: </p>
                    <p style="margin-bottom: 5px">NOMBRE DEL CONTRIBUYENTE: </p>
                    <p>DOMICILIO: </p>
                </div>
            </div>
        </htmlpageheader>

        <htmlpagefooter name="MyFooter1">
            <hr>
            <p><strong>NOTA: EL TOTAL A CANCELAR PUEDE VARIAR DESPÚES DE LA FECHA DE REFERENCIA, POR ACTUALIZACIÓN OPERADA EN LA BASE DE DATOS.</strong></p>
            <div style="display: table; clear: both; margin-top: 5px">
                <div style="float: left; width: 33%">
                    <p>Fecha de vencimiento: </p>
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

foreach ($registros as $registro) {

$html.= ' <tr>
<td width="8%" height="40">300213003</td>
<td width="8%">01S028556-48</td>
<td width="15%">3 AVENIDA 0 32-61 Z. 02</td>
<td>1852-352-284-E-1</td>
<td align="right">196,488.57</td>
<td align="right">9.00</td>
<td width="12%" align="right">442.10</td>
<td align="right">9,726.20</td>
<td align="right">1,945.24</td>
<td align="right">0.00</td>
<td align="right">11,671.44</td>
</tr>';


}

                        
$html.=  '</tbody>
                </table>
                <table style="margin-left: 515px">
                    <tr>
                        <td height="40" width="322" style="font-size: 16px;"><strong>TOTAL A CANCELAR</strong></td>
                        <td align="right" width="250" style="font-size: 16px"><strong>11,671.44</strong></td>
                    </tr>
                </table>
            </div>
        </div>
';

$mpdf->WriteHTML($html);



$mpdf->Output();

?>

