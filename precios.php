<?php
/**
 * Una simple función para obtener los costos de envío de un paquete mediante Chilexpress.
 * Como única dependencia se necesita de la liberia PHP Simple HTML DOM Parser: http://simplehtmldom.sourceforge.net/
 *
 * Para poder comunicarse con Chilexpress, se debe tener la lista de todas las comunas que ellos utilizan y el código
 * que le asignan a cada una. En este archivo, al final, podrás encontrar el listado, el cual podrás parsear fácilmente
 */
include 'simple_html_dom.php';
/**
 * Obtiene costos de envío para un paquete dado
 * @param  string $comunaOrigen      Código interno de Chilexpress para la comuna de origen
 * @param  string $comunaDestino      Código interno de Chilexpress para la comuna de destino
 * @param  float $peso        Peso en Kilogramos
 * @param  array $dimensiones Dimensiones del paquete (largo, alto, ancho) en centímetros
 * @return mixed              Un arreglo con los distintos valores y servicios o 'false' ante cualquier error
 */
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
  $htmlValores = @file_get_html('http://www.chilexpress.cl/cotizadorweb/nacional_resultado.asp', false, $context);
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
/**
 * Ejemplo de uso con un paquete que va desde Las Condes, Santiago a Ancúd, Chiloé
 * con un peso de 1KG y con dimensiones de 10 x 10 x 10 cm
 *
 * var_dump(chilexpress_obtener_valores('LCON', 'ANCU', 1, array(10, 10, 10)));
 *
 * El output obtenido es:
 *
 * array (size=2)
 *   'DIA HABIL SIGUIENTE' => string '5100' (length=4)
 *   'ENTREGA DIA SABADO' => string '7650' (length=4)
 *
 * Tener en cuenta que sólo se listan los servicios disponibles para la comuna de destino,
 * por lo que el largo del array puede variar.
 */
/*
 *********** Listado de comunas ***********
|ACHAO;ACHA
|ALGARROBO;ALGA
|ALTO HOSPICIO;AHOS
|ALTO JAHUEL;AJAH
|ANCUD;ANCU
|ANDACOLLO;ANDA
|ANGOL;ANGO
|ANTOFAGASTA;ANTO
|ARAUCO;ARAU
|ARICA;ARIC
|ARTIFICIO;ARTI
|BALMACEDA;BALM
|BARRANCAS;BARR
|BATUCO;BATU
|BUIN;BUIN
|BULNES;BULN
|CABILDO;CABI
|CABRERO;CABR
|CALAMA;CALA
|CALBUCO;CALB
|CALDERA;CALD
|CALERA DE TANGO;CTAN
|CANETE;CANE
|CARAHUE;CARA
|CARTAGENA;CART
|CASABLANCA;CASA
|CASTRO;CAST
|CAUQUENES;CAUQ
|CERRILLOS;LOSC
|CERRO NAVIA;CNAV
|CHANARAL;CHAN
|CHEPICA;CHEP
|CHICUREO;CHIC
|CHIGUAYANTE;CHIG
|CHILE CHICO;CHCH
|CHILLAN;CHIL
|CHIMBARONGO;CHIM
|CHOLGUAN;CHOL
|CHONCHI;CHON
|COCHRANE;COCH
|CODELCO RADOMIRO TOMIC;RTOM
|COELEMU;COEL
|COIHUECO;COIC
|COINCO;COIN
|COLINA;COLI
|COLLIPULLI;COLL
|COLTAUCO;COLT
|COMBARBALA;COMB
|CONCEPCION;CONC
|CONCHALI;CCHA
|CONCON;CCON
|CONSTITUCION;CONS
|COPIAPO;COPI
|COQUIMBO;COQU
|CORONEL;CORO
|COYHAIQUE;COYH
|CUMPEO;CUMP
|CURACAUTIN;CURC
|CURACAVI;CRCV
|CURANILAHUE;CURA
|CURICO;CURI
|DALCAHUE;DALC
|DIEGO DE ALMAGRO;DIEG
|DONIHUE;DONI
|EL BELLOTO;ELBE
|EL BOSQUE;ELBO
|EL CARMEN;ECAR
|EL MELON;EMEL
|EL MONTE;ELMO
|EL PAICO;ELPA
|EL QUISCO;QSCO
|EL SALADO;ESAL
|EL SALVADOR;ELSA
|EL TABITO;TABI
|EL TABO;TABO
|ENTRE LAGOS;ELAG
|ESTACION CENTRAL;ECEN
|ESTACION PAIPOTE;EPAI
|FREIRE;FRER
|FREIRINA;FREI
|FRUTILLAR;FRUT
|FUERTE BAQUEDANO;FBAQ
|FUTRONO;FUTR
|GORBEA;GORB
|GRANEROS;GRAN
|HIJUELAS;HIJU
|HORNOPIREN;HORP
|HUALPEN;HPEN
|HUASCO;HUAS
|HUECHURABA;HUEC
|ILLAPEL;ILLA
|INDEPENDENCIA;INDE
|IQUIQUE;IQUI
|ISLA DE MAIPO;IMAI
|ISLA NEGRA;INEG
|ISLA TEJA;ITEJ
|ITAHUE;ITAH
|LA CALERA;LACA
|LA CISTERNA;LACI
|LA CRUZ;LACR
|LA FLORIDA;LAFL
|LA GRANJA;LAGR
|LA JUNTA;LAJU
|LA LIGUA;LALI
|LA PINTANA;LAPI
|LA REINA;LARE
|LA SERENA;LASE
|LA UNION;LAUN
|LAGO RANCO;LRAN
|LAJA;LAJA
|LAMPA;LAMP
|LANCO;LANC
|LAS CABRAS;LCAB
|LAS CONDES;LCON
|LAS CRUCES;LCRU
|LAUTARO;LAUT
|LEBU;LEBU
|LIMACHE;LIMA
|LINARES;LINA
|LITUECHE;LCHE
|LLANQUIHUE;LLAN
|LLAY LLAY;LLAY
|LLO LLEO;LLOL
|LO BARNECHEA;LOBA
|LO ESPEJO;LOES
|LO MIRANDA;LOMI
|LO PRADO;LOPR
|LOLOL;LOLO
|LONCOCHE;LONC
|LONGAVI;LONG
|LONGOVILO;LONV
|LONQUEN;LONQ
|LONTUE;LONT
|LOS ANDES;LAND
|LOS ANGELES;LANG
|LOS LAGOS;LLAG
|LOS MUERMOS;LMUE
|LOS VILOS;LVIL
|LOTA;LOTA
|MACHALI;MACH
|MACUL;MACU
|MAFIL;MAFI
|MAIPU;MIPU
|MALLOA;MALO
|MALLOCO;MALL
|MARCHIGUE;MARC
|MARIA ELENA;MARI
|MAULLIN;MAUL
|MEJILLONES;MEJI
|MELINKA;MELK
|MELIPILLA;MELI
|MININCO;MINI
|MOLINA;MOLI
|MONTE PATRIA;MOPA
|MULCHEN;MULC
|NACIMIENTO;NACI
|NANCAGUA;NANC
|NATALES;PNAT
|NOGALES;NOGA
|NOS;NOSO
|NUEVA ALDEA;NALD
|NUEVA IMPERIAL;NVAI
|NUNOA;NUNO
|OLIVAR;OLIV
|OLMUE;OLMU
|OSORNO;OSOR
|OVALLE;OVAL
|PADRE HURTADO;PHUR
|PADRE LAS CASAS;PLCA
|PAILLACO;PAIL
|PAINE;PAIN
|PAIPOTE;PAIP
|PALMILLA;PALM
|PANGUIPULLI;PANG
|PARGUA;PARG
|PARRAL;PARR
|PEDRO AGUIRRE CERDA;PEDR
|PELEQUEN;PELE
|PEMUCO;PEMU
|PENABLANCA;PBLA
|PENAFLOR;PENA
|PENALOLEN;PLOL
|PENCO;PENC
|PERALILLO;PERA
|PEUMO;PEUM
|PICA;PICA
|PICHIDEGUA;PICD
|PICHILEMU;PICH
|PINTO;PINT
|PIRQUE;PIRQ
|PITRUFQUEN;PITR
|PLACILLA QUINTA REGION;PLAV
|PLACILLA SEXTA REGION;PLAC
|PORVENIR;PORV
|POZO ALMONTE;POZO
|PROVIDENCIA;PROV
|PUCHUNCAVI;PUCH
|PUCON;PUCO
|PUDAHUEL;PUDA
|PUEBLO SECO;PSEC
|PUENTE ALTO;PALT
|PUERTO AGUIRRE;PAGU
|PUERTO AYSEN;PAYS
|PUERTO CHACABUCO;PCHA
|PUERTO CISNES;PCIS
|PUERTO MONTT;PMON
|PUERTO VARAS;PVAR
|PUERTO WILLIAMS;PWIL
|PUNITAQUI;PUNI
|PUNTA ARENAS;PUNT
|PUREN;PURE
|PURRANQUE;PURR
|PUTRE;PUTR
|PUYUHUAPI;PUYU
|QUELLON;QUEL
|QUEMCHI;QUEM
|QUEPE;QUEP
|QUILICURA;QILI
|QUILLON;QULL
|QUILLOTA;QLTA
|QUILPUE;QUIL
|QUINTA DE TILCOCO;QTIL
|QUINTA NORMAL;QNOR
|QUINTERO;QUIN
|QUIRIHUE;QUIR
|QUIRIQUINA;QUIQ
|RANCAGUA;RANC
|RECOLETA;RECO
|RENACA;RENA
|RENAICO;RNCO
|RENCA;RENC
|RENGO;RENG
|REQUINOA;REQU
|RIO BUENO;RIOB
|RIO NEGRO;RNEG
|ROMERAL;ROME
|ROSARIO;ROSA
|SALAMANCA;SALA
|SAN ANTONIO;SANT
|SAN BERNARDO;SBER
|SAN CARLOS;SCAR
|SAN CLEMENTE;SCLE
|SAN FELIPE;SFEL
|SAN FERNANDO;SFER
|SAN FRANCISCO DE LIMACHE;SFLI
|SAN FRANCISCO DE MOSTAZAL;SFRA
|SAN IGNACIO;SIGN
|SAN JAVIER;SJAV
|SAN JOAQUIN;SJOA
|SAN JOSE DE LA MARIQUINA;SJMA
|SAN MIGUEL;SMIG
|SAN PABLO;SPAB
|SAN PEDRO DE ATACAMA;SPAT
|SAN PEDRO DE LA PAZ;SPED
|SAN PEDRO QUINTA REGION;SPDO
|SAN RAMON;SRAM
|SAN ROSENDO;SANR
|SAN SEBASTIAN;SSEB
|SAN VICENTE DE TAGUA TAGUA;SVIC
|SANTA BARBARA;SBAR
|SANTA CRUZ;SCRU
|SANTA JUANA;SJUN
|SANTIAGO CENTRO;STGO
|SANTO DOMINGO;SDGO
|TALAGANTE;TALA
|TALCA;TALC
|TALCAHUANO;THNO
|TALTAL;TALT
|TEMUCO;TEMU
|TENO;TENO
|TIERRA AMARILLA;TAMA
|TIL TIL;TILT
|TOCOPILLA;TOCO
|TOME;TOME
|TONGOY;TONG
|TRAIGUEN;TRAI
|VALDIVIA;VALD
|VALLENAR;VALL
|VALPARAISO;VALP
|VICTORIA;VICT
|VICUNA;VICU
|VILLA ALEGRE;VALG
|VILLA ALEMANA;VALE
|VILLA MANIHUALES;VMAN
|VILLARRICA;VILL
|VINA DEL MAR;VINA
|VITACURA;VITA
|YUMBEL;YUMB
 */
error_reporting(E_ERROR | E_WARNING | E_PARSE);

$origen=$_POST['origen'];
$destino=$_POST['destino'];
$kilos=$_POST['kilos'];
$dimensiones=$_POST['dimensiones'];
$dim = explode(",", $dimensiones);

$array = (chilexpress_obtener_valores($origen, $destino, $kilos, $dim));

foreach ($array as $k => $v) {
    ?>
		<input type="radio" name="valor" value="<?php echo $k.'/'. $v; ?>"> <?php echo $k.'/'.$v ?><br>
  		
    <?php
}
?>
