<?php

namespace App\Http\Controllers\Sales;

use App\Http\Controllers\Controller;
use App\Models\Conf\Compania;
use App\Models\Conf\Country\Ciudades;
use Illuminate\Http\Request;

use App\Models\Sales\Client;
use App\Models\Conf\Country\Estados;
use App\Models\Conf\Country\Municipios;
use Illuminate\Support\Facades\DB;


class ClientController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:sales-clients-list|adm-list', ['only' => ['index']]);
        $this->middleware('permission:adm-create|sales-clients-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:adm-edit|sales-clients-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:adm-delete|sales-clients-delete', ['only' => ['destroy']]);
    }

    public function index(Request $request)
    {

        
        $conf = [
            'title-section' => 'Gestion de clientes',
            'group' => 'sales-clients',
            'create' => ['route' => 'clients.create', 'name' => 'Nuevo cliente'],
            'url' => '/sales/clients/create'
        ];

        

        $compania = Compania::all();
        

            // $tiposTdsClientes = array();
            // $tiposTdsDatos = array();
            // $tiposTdsRegiones = array();
            // $tiposTdsMuicipios = array();
            // $tiposTdsComunas = array();


            // $tds_clientes = DB::getSchemaBuilder()->getColumnListing('clients');
            // foreach($tds_clientes as $k => $tds){
            //     $tiposTdsClientes[$k] = DB::getSchemaBuilder()->getColumnType('clients', $tds);
            // }
            // $tds_combine_cliente = array_combine($tds_clientes, $tiposTdsClientes);




            // $tds_datos = DB::getSchemaBuilder()->getColumnListing('datos');
            // foreach($tds_datos as $k => $tds){
            //     $tiposTdsDatos[$k] = DB::getSchemaBuilder()->getColumnType('datos', $tds);
            // }
            // $tds_combine_datos = array_combine($tds_datos, $tiposTdsDatos);




            // $tds_regiones = DB::getSchemaBuilder()->getColumnListing('estados');
            // foreach($tds_regiones as $k => $tds){
            //     $tiposTdsRegiones[$k] = DB::getSchemaBuilder()->getColumnType('estados', $tds);
            // }
            // $tds_combine_regiones = array_combine($tds_regiones, $tiposTdsRegiones);




            // $tds_municipios = DB::getSchemaBuilder()->getColumnListing('municipios');
            // foreach($tds_municipios as $k => $tds){
            //     $tiposTdsMuicipios[$k] = DB::getSchemaBuilder()->getColumnType('municipios', $tds);
            // }
            // $tds_combine_municipios = array_combine($tds_municipios, $tiposTdsMuicipios);




            // $tds_comunas = DB::getSchemaBuilder()->getColumnListing('ciudades');
            // foreach($tds_comunas as $k => $tds){
            //     $tiposTdsComunas[$k] = DB::getSchemaBuilder()->getColumnType('ciudades', $tds);
            // }
            // $tds_combine_comunas = array_combine($tds_comunas, $tiposTdsComunas);

            // //$data = array_merge($tds_combine_cliente, $tds_combine_datos, $tds_combine_regiones, $tds_combine_municipios, $tds_combine_comunas);

            $data = [
                'name_client', 'idcard_client', 'address_client', 'complement_address_client', 'phone_client', 'phone2_client', 'email_client', 'email2_client', 'personeria', 
                'id_tipo_doc', 'id_contrato', 'codigo_rnut', 'tipo_doc', 'num_doc', 'fec_emi', 'fec_ven', 'monto', 'interes', 'gasto_cob', 'porc_imp', 'total_doc', 'mora', 'tramo_mora', 'tramo_monto',
                'estado',
                'municipio',
            ];

            
            //$tablas = ['clients' => $tds_combine_cliente, 'datos' =>$tds_combine_datos, 'regiones' => $tds_combine_regiones, 'provincias' => $tds_combine_municipios, 'comunas' => $tds_combine_comunas];
            //return $tablas['clients']['id_client'];
        
            $tablas = [
                    'name_client' => 'string', 
                    'idcard_client' => 'string', 
                    'address_client' => 'string', 
                    'complement_address_client' => 'string', 
                    'phone_client' => 'integer', 
                    'phone2_client' => 'integer', 
                    'email_client' => 'email', 
                    'email2_client' => 'email', 
                    'personeria' => 'string', 
                    'id_tipo_doc' => 'integer', 
                    'id_contrato' => 'integer', 
                    'codigo_rnut' => 'string', 
                    'tipo_doc' => 'integer', 
                    'num_doc' => 'integer', 
                    'fec_emi' => 'date', 
                    'fec_ven' => 'date', 
                    'monto' => 'integer', 
                    'interes' => 'integer', 
                    'gasto_cob' => 'integer', 
                    'porc_imp' => 'integer', 
                    'total_doc' => 'integer', 
                    'mora' => 'integer', 
                    'tramo_mora' => 'string', 
                    'tramo_monto' => 'string',
                    'estado' => 'string',
                    'municipio' => 'string', 
                    'ciudad' => 'string'
            ];
        // return $tablas;

        // $data

        return view('sales.clients.index', compact('conf', 'data', 'compania', 'tablas'));
    }

    public function filter(Request $request){

        $arr = $request->obj;
        $texto = '';
        $akey = array_keys($arr);

        foreach($arr as $k => $campos){
            switch ($k) {
                case 'name_client':
                    $texto .= $k.' like "%'.$campos.'%" AND ';
                    break;
                
                case 'min':
                    $texto .= 'monto between '.$arr['min'];
                    break;
                case 'max':
                    $texto .= ' AND '.$arr['max'];
                    break;
                default:
                    $texto .= $k.' = "'.$campos.'" AND ';
                    break;
            }
        }

        $texto = rtrim($texto, 'AND ');
        
        $query = "SELECT
                data.id_tipo_doc, data.id_contrato, data.codigo_rnut, data.tipo_doc, data.num_doc, data.fec_emi, data.fec_ven, data.monto, data.interes, data.gasto_cob, data.porc_imp, data.total_doc, data.mora, data.tramo_mora, data.tramo_monto, 
                clients.name_client, clients.idcard_client, clients.address_client, clients.complement_address_client, clients.phone_client, clients.phone2_client, clients.email_client, clients.email2_client, clients.personeria, 
                e.estado, 
                m.municipio, 
                c.ciudad
                from clients
                inner join estados as e on clients.id_state = e.id_estado 
                inner join datos as data on clients.id_client = data.id_cliente 
                inner join municipios as m on clients.region = m.id_municipio 
                inner join ciudades as c on clients.comuna = c.id_ciudad
                where $texto";

        $filtroR = DB::select($query);
        
        return $filtroR;

    }


    public function dataEmpresa(Request $request)
    {

        $data = $request;
        //Consulta para obtener los datos de la compania a la cual le queremos consultar los clientes


        $empresa = Client::select('data.*',  'clients.*', 'e.estado', 'm.municipio', 'c.ciudad')
        ->join('estados as e', 'clients.id_state', '=', 'e.id_estado')
        ->join('datos as data', 'clients.id_client', '=', 'data.id_cliente')
        ->join('municipios as m', 'clients.region', '=', 'm.id_municipio')
        ->join('ciudades as c', 'clients.comuna', '=', 'c.id_ciudad')
        ->whereIdCompany($data->id_cliente)
        ->get();

        //retorno una coleccion de bases de datos con los datos requeridos. 
        return $empresa;
    }
   

    public function filtroMontos(Request $request)
    {

        /**
         * Filtro para los montos
         * 
         * Al ser conjugable con los filtros anteriores vamos a encontrar distintos tipos de verificaciones
         * 
         * 1. Regiones 
         *  1.1 Si en la $request el valor de id_region viene nulo, solo filtro por Compania y montos.
         *  1.2 en caso contario filtro con el valor de la region
         * 
         * 2. Provincias
         *  2.1 Si la $request el valor de id_provincia viene nulo, solo filtro por compania y montos
         *  2.2 en caso contario filtro con los datos anteriores y el valor del id_provincia
         * 
         * 3. Comuna
         *  3.1 Si la $request el valor de id_comuna viene nulo, solo filtro por compania y montos
         *  3.2 en caso contario filtro con los datos anteriores y el valor del id_comuna
         * 
         */
        if ($request->id_region == null) { // 1
            // 1.1
            $filtroMontos = Client::select('data.*',  'clients.*', 'e.estado', 'm.municipio', 'c.ciudad')
                ->join('datos as data', 'clients.id_client', '=', 'data.id_cliente')
                ->join('estados as e', 'clients.id_state', '=', 'e.id_estado')
                ->join('municipios as m', 'clients.region', '=', 'm.id_municipio')
                ->join('ciudades as c', 'clients.comuna', '=', 'c.id_ciudad')
                ->whereIdCompany($request->id_cliente)
                ->whereBetween('data.monto', [$request->min, $request->max])
                ->get();
        } else {
            // 1.2
            if ($request->id_provincia == null) {// 2
                // 2.1
                $filtroMontos = Client::select('data.*', 'e.estado', 'm.municipio', 'c.ciudad', 'clients.*')
                    ->join('datos as data', 'clients.id_client', '=', 'data.id_cliente')
                    ->join('estados as e', 'clients.id_state', '=', 'e.id_estado')
                    ->join('municipios as m', 'clients.region', '=', 'm.id_municipio')
                    ->join('ciudades as c', 'clients.comuna', '=', 'c.id_ciudad')
                    ->whereIdCompany($request->id_cliente)
                    ->whereIdState($request->id_region)
                    ->whereBetween('data.monto', [$request->min, $request->max])
                    ->get();
            } else {
                // 2.2
                if ($request->id_comuna == null) { // 3
                    // 3.1
                    $filtroMontos = Client::select('data.*', 'm.municipio', 'e.estado', 'clients.*', 'c.ciudad')
                    ->join('datos as data', 'clients.id_client', '=', 'data.id_cliente')
                    ->join('municipios as m', 'clients.region', '=', 'm.id_municipio')
                    ->join('estados as e', 'clients.id_state', '=', 'e.id_estado')
                    ->join('ciudades as c', 'clients.comuna', '=', 'c.id_ciudad')
                    ->whereIdCompany($request->id_cliente)
                    ->whereRegion($request->id_provincia)
                    ->whereIdState($request->id_region)
                    ->whereBetween('data.monto', [$request->min, $request->max])
                    ->get();
                }else{
                    // 3.2
                    $filtroMontos = Client::select('data.*', 'e.estado', 'm.municipio', 'c.ciudad', 'clients.*')
                    ->join('estados as e', 'clients.id_state', '=', 'e.id_estado')
                    ->join('municipios as m', 'clients.region', '=', 'm.id_municipio')
                    ->join('ciudades as c', 'clients.comuna', '=', 'c.id_ciudad')
                    ->join('datos as data', 'clients.id_client', '=', 'data.id_cliente')
                    ->whereComuna($request->id_comuna)
                    ->whereRegion($request->id_provincia)
                    ->whereIdState($request->id_region)
                    ->whereIdCompany($request->id_cliente)
                    ->whereBetween('data.monto', [$request->min, $request->max])
                    ->get();
                }
                
            }
        }

        // Retorno la colleccion de objetos
        return $filtroMontos;
    }



    public function create()
    {
        $conf = [
            'title-section' => 'Crear un nuevo cliente',
            'group' => 'sales-clients',
            'back' => 'clients.index',
            'url' => '/sales/clients'
        ];

        $estados = Estados::pluck('estado', 'id_estado');
        return view('sales.clients.create', compact('conf', 'estados'));
    }

    public function store(Request $request)
    {

        $data = $request->except('_token');

        $save = new Client();
        $save->name_client = strtoupper($data['name_client']);
        $save->idcard_client = strtoupper($data['letra']) . $data['idcard_client'];
        $save->address_client = strtoupper($data['address_client']);
        $save->id_state = $data['id_state'];

        if (isset($data['phone_client'])) {
            $save->phone_client = $data['phone_client'];
        }
        if (isset($data['email_client'])) {
            $save->email_client = strtoupper($data['email_client']);
        }
        if (isset($data['zip_client'])) {
            $save->zip_client = $data['zip_client'];
        }
        if (isset($data['taxpayer_client'])) {
            $save->taxpayer_client = $data['taxpayer_client'];
        }

        $save->save();

        $message = [
            'type' => 'success',
            'message' => 'El cliente, se registro con éxito',
        ];

        return redirect()->route('clients.index')->with('message', $message);
    }

    public function show($id)
    {

        $getClient = Client::whereIdClient($id)->whereEnabledClient(1)->get()[0];
        $getState = Estados::whereIdEstado($getClient->id_state)->get()[0]->estado;

        $conf = [
            'title-section' => 'Datos del cliente: ' . $getClient->name_client,
            'group' => 'sales-clients',
            'back' => 'clients.index',
            'edit' => ['route' => 'clients.edit', 'id' => $getClient->id_client],
            'url' => '/sales/clients',
            'delete' => ['name' => 'Eliminar cliente']
        ];

        return view('sales.clients.show', compact('conf', 'getClient', 'getState'));
    }

    public function edit($id)
    {
        $client = Client::whereIdClient($id)->whereEnabledClient(1)->get()[0];
        $estados = Estados::pluck('estado', 'id_estado');
        $letra = substr($client->idcard_client, 0, 1);
        $numero = substr($client->idcard_client, 1);
        $client->idcard_client = $numero;

        $conf = [
            'title-section' => 'Editar cliente: ' . $client->name_client,
            'group' => 'sales-clients',
            'back' => ['route' => "./", 'show' => true],
            'url' => '/sales/clients',
        ];

        return view('sales.clients.edit', compact('conf', 'letra', 'client', 'estados'));
    }

    public function update(Request $request, $id)
    {

        $data = $request->except('_token', '_method', 'letra');
        $data['name_client'] = strtoupper($data['name_client']);
        $data['idcard_client'] = strtoupper($request->letra) . $data['idcard_client'];
        $data['address_client'] = strtoupper($data['address_client']);
        $data['id_state'] = $data['id_state'];

        if (isset($data['phone_client'])) {
            $data['phone_client'] = $data['phone_client'];
        }
        if (isset($data['email_client'])) {
            $data['email_client'] = strtoupper($data['email_client']);
        }
        if (isset($data['zip_client'])) {
            $data['zip_client'] = $data['zip_client'];
        }
        if (isset($data['taxpayer_client'])) {
            $data['taxpayer_client'] = $data['taxpayer_client'];
        }
        if (isset($data['porcentual_amount_tax_client'])) {
            $data['porcentual_amount_tax_client'] = $data['porcentual_amount_tax_client'];
        }

        Client::whereIdClient($id)->update($data);
        //return isset($data['porcentual_amount_tax_client']);



        $message = [
            'type' => 'warning',
            'message' => 'El cliente, se actualizo con éxito',
        ];




        return redirect()->route('clients.index')->with('message', $message);
    }

    public function destroy($id)
    {

        $data = Client::whereIdClient($id)->update(
            ['enabled_client' => 0]
        );

        return redirect()->route('clients.index')->with('success', 'Usuario eliminado con exito');
    }






    function searchCliente(Request $request)
    {
        $data = Client::whereIdcardClient($request->text)->get();
        if (count($data) > 0) {
            return response()->json(['res' => false, 'msg' => 'El DNI ó RIF ya fueregistrado']);
        } else {
            return response()->json(['res' => true, 'msg' => 'El DNI ó RIF es valido']);
        }
        return $data;
    }


    public function search(Request $request)
    {
        $data = DB::select('SELECT id_client, phone_client, name_client, idcard_client, address_client 
                            FROM clients 
                            WHERE name_client LIKE "%' . $request->text . '%" 
                            OR idcard_client LIKE "%' . $request->text . '%"
                            AND enabled_client = 1');
        return response()->json(['lista' => $data]);
    }
}
