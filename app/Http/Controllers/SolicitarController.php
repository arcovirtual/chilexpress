<?php

namespace App\Http\Controllers;

use App\Comuna;
use Illuminate\Http\Request;
use Sunra\PhpSimple\HtmlDomParser;
use App\Http\Controllers\chilexpress_obtener_valores;

class SolicitarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $comunas=Comuna::All();

        return view('solicitar')->with(['comunas'=>$comunas]);
    }



    public function obtener(Request $request)
    {
        
        $origen=$request->get('origen');
        $destino=$request->get('destino');
        $kilos=$request->get('kilos');
        $largo=$request->get('largo');
        $alto=$request->get('alto');
        $largo=$request->get('largo');
        $ancho=$request->get('ancho');
        $dim = array($largo,$alto,$ancho);


    
         function chilexpress_obtener_valores($comunaOrigen, $comunaDestino, $peso, $dimensiones){

            
                $request = array(
                    'http' => array(
                        'method' => 'POST',
                        'header'=> 'Content-type: application/x-www-form-urlencoded\r\n',
                        'timeout' => 10, // Máximo 10 segundos esperando una respuesta
                            'content' => http_build_query(array(
                            'text_gls_origen' => trim($comunaOrigen),
                            'text_gls_destino' => trim($comunaDestino),
                            'text_gls_producto' => 'ENCOMIENDA',
                            'accion' => 'lista_cotizador',
                            'cmb_lst_origen' => trim($comunaOrigen),
                            'cmb_lst_destino' => trim($comunaDestino),
                            'cmb_lst_producto' => 3,
                            'peso' => $peso, //KG
                            'Dimension3' => $dimensiones[0], // Largo
                            'Dimension1' => $dimensiones[1], // Alto
                            'Dimension2'=> $dimensiones[2] // Ancho
                            )
                        )
                    )
                );
                
            $context = stream_context_create($request);
            $htmlValores = @HtmlDomParser::str_get_html('http://www.chilexpress.cl/cotizadorweb/nacional_resultado.asp', false, $context);
                if($htmlValores !== false){
                $filas = $htmlValores->find('table', 4)->find('tr');
                    foreach ($filas as $fila) {
                    $tipoEnvio = trim($fila->find('td', 0)->plaintext);
                    $valor = trim($fila->find('td', 1)->plaintext);
                        if($tipoEnvio !== 'Plazo Entrega' and $tipoEnvio !== '' and $tipoEnvio !== 'NO EXISTE COBERTURA PARA LA CIUDAD DE ORIGEN'){
                        $index = trim($comunaDestino);
                            if(!isset($destinosValores[$index])){
                            $destinosValores[$index] = array();
                            }
                        $valor = str_replace('$', '', $valor);
                        $valor = str_replace('.', '', $valor);
                        $destinosValores[$index][$tipoEnvio] = $valor;
                        }
                    }
                if(isset($destinosValores)){
                return reset($destinosValores);
                }
                }
            return false;
            }

            return $array = (chilexpress_obtener_valores($origen, $destino, $kilos, $dim));
    }
   
}
