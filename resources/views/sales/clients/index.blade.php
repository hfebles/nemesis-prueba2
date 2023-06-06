@extends('layouts.app')

@section('title-section', $conf['title-section'])

@section('btn')
    <x-btns :group="$conf['group']" />
@endsection

@section('content')
    <div class="row">
        <x-cards>
            <table id='myTable' class="table table-sm table-bordered table-hover mb-0">
                <thead class="text-white bg-gray-900">
                    <tr>
                        <th width="80%" class="mb-0 text-uppercase">Empresa</th>
                        <th width="20%" colspan="3" class="mb-0 text-uppercase">Acciones</th>
                    </tr>
                </thead>

                <tr>

                    <td width="5%" class="align-middle mb-0">
                        
                    </td>

                    <td width="10%"><a onclick="addRow()" class="btn btn-info btn-sm btn-block"> Nuevo campo</a></td>
                    <td><button type="button" onclick="filtro();" id='btnConsulta'
                            class="btn btn-success btn-sm btn-block">Ejecutar</button></td>
                    <td width="5%" class="align-middle mb-0"><button class="btn btn-danger btn-sm btn-block"
                            type="button" onclick="location.reload()" name="reset" id="reset">Limpiar</button></td>

                </tr>


            </table>
        </x-cards>

        <div class="col-12 mt-3">
            <div class="card" style="display:none;" id="devseto">
                <div class="card-body" style="height:500px; overflow:scroll;" id="body-tabla"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')

    <script>
        var div = document.getElementById('devseto');
        var tabla = document.getElementById('body-tabla');
        var i = 0;





        function addRow() {
            var table = document.getElementById("myTable");
            var row = table.insertRow(-1);
            row.id = 'tr_' + i

            var tablax = @json($tablas);

            var lineeeas = '';

            var keeys = Object.keys(tablax)

            // console.log(keeys)

            var cell2 = row.insertCell(-1);
            var cell3 = row.insertCell(-1);
            lineeeas += '<select class="form-select form-select-sm ops" id="combo_' + i +
                '"  onchange="cambioTipo(this.value, ' + i + ')" name="opts[]">'
            for (let d = 0; d < keeys.length; d++) {
                lineeeas += `<option>${keeys[d]}</option>`
                            }
            lineeeas += `</select>`;
            cell2.innerHTML = lineeeas;
            cell3.innerHTML =
                `<input class="form-control form-control-sm valores" type='text' name='valor[]' id='valor_${i}'>`;
            i++

        }

        function filtro() {

            const csrfToken = "{{ csrf_token() }}";


            var valores = document.querySelectorAll('.valores');
            var op = document.querySelectorAll('.ops');
            var obj = {};

            for (let i = 0; i < op.length; i++) {

                if (op[i] && op[i].value === 'monto') {
                    obj['min'] = valores[i].value;
                    obj['max'] = valores[i + 1].value;
                    continue
                } else if (op[i]) {
                    obj[op[i].value] = valores[i].value;
                }
            }




            fetch('/sales/clients/filter', {
                method: 'POST',
                body: JSON.stringify({
                    obj
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                div.style.display = 'block'
                var linea = "";

                linea += `<h4>Conteo de registros: ${data.length}</h4>`
                linea += `<table class="table table-bordered table-hover mb-0 text-uppercase">
                    <thead class="text-white bg-gray-900">
                        <tr>
                            <th>ID_TIPO_DOC</th>
                            <th>ID_CONTRATO</th>
                            <th>CODIGO_RNUT</th>
                            <th>TIPO_DOC</th>
                            <th>NUM_DOC</th>
                            <th>FEC_EMI</th>
                            <th>FEC_VEN</th>
                            <th>MONTO</th>
                            <th>INTERES</th>
                            <th>GASTO_COB</th>
                            <th>PORC_IMP</th>
                            <th>TOTAL_DOC</th>
                            <th>MORA</th>
                            <th>TRAMO_MORA</th>
                            <th>TRAMO_MONTO</th>
                            <th>NAME_CLIENT</th>
                            <th>IDCARD_CLIENT</th>
                            <th>ADDRESS_CLIENT</th>
                            <th>COMPLEMENT_ADDRESS_CLIENT</th>
                            <th>PHONE_CLIENT</th>
                            <th>PHONE2_CLIENT</th>
                            <th>EMAIL_CLIENT</th>
                            <th>EMAIL2_CLIENT</th>
                            <th>PERSONERIA</th>
                            <th>ESTADO</th>
                            <th>MUNICIPIO</th>
                            <th>CIUDAD</th>
                        </tr>
                    </thead>`
                for(let l in data){
                    linea += ` <tr>`
                    for(let m in data[l]){
                        linea +=`<td>${data[l][m]}</td>`
                    }
                    linea += ` </tr>`
                }
                linea += ` </table>`
                tabla.innerHTML = linea;
            });

        }

        function cambioTipo(tipo, count) {
            var datax = @json($data);
            var pro = @json($tablas)

            console.log(pro)
            
            if (tipo == 'monto') {
                var table = document.getElementById("myTable");
                nuevoTd = document.createElement("td");
                const ultimaFila = table.rows[table.rows.length - 1];
                const nuevoInput = document.createElement("input");
                nuevoInput.type = "number";
                nuevoInput.name = "max";
                nuevoInput.className = 'form-control form-control-sm valores'
                nuevoTd.appendChild(nuevoInput);
                ultimaFila.appendChild(nuevoTd);
                document.getElementById('valor_' + count).name = 'min'
            }

            if (pro.hasOwnProperty(tipo) == true) {
                switch (pro[tipo]) {
                    case 'integer':
                        document.getElementById('valor_' + count).type = 'number'
                        document.getElementById('valor_' + count).focus()
                        document.getElementById('valor_' + count).setAttribute("onkeypress", "return validoNumeros(event)");
                        break;
                    case 'email':
                        document.getElementById('valor_' + count).type = 'email'
                        document.getElementById('valor_' + count).focus()
                        document.getElementById('valor_' + count).setAttribute("onkeypress", "return validoEmail(event)");
                        break;
                    case 'date':
                        document.getElementById('valor_' + count).type = 'date'
                        ocument.getElementById('valor_' + count).focus()

                        break;
                    default:
                        document.getElementById('valor_' + count).type = 'text'
                        document.getElementById('valor_' + count).focus()
                        document.getElementById('valor_' + count).setAttribute("onkeypress", "return validoLetrasNumeros(event)");
                        
                        
                        break;
                }
            }
            
        }

        function listaClientes(id_cliente) {
            const csrfToken = "{{ csrf_token() }}";
            fetch('/sales/clients/dataempresa', {
                method: 'POST',
                body: JSON.stringify({
                    id_cliente: id_cliente,
                }),
                headers: {
                    'content-type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                return response.json();
            }).then(data => {
                div.style.display = 'block'
                var linea = "";
                linea += `<span>Conteo: ${data.length}</span>`
                linea += `<table  class="table table-bordered table-hover mb-0 text-uppercase overflow-scroll">
                            <tr>
                                <th class="bg-dark text-white">name_client</th>
                                <th class="bg-dark text-white">idcard_client</th>
                                <th class="bg-dark text-white">estado</th>
                                <th class="bg-dark text-white">municipio</th>
                                <th class="bg-dark text-white">ciudad</th>
                                <th class="bg-dark text-white">monto</th>
                                <th class="bg-dark text-white">id_tipo_doc</th>
                                <th class="bg-dark text-white">id_contrato</th>
                                <th class="bg-dark text-white">fec_emi</th>
                                <th class="bg-dark text-white">fec_ven</th>
                                <th class="bg-dark text-white">mora</th>
                                <th class="bg-dark text-white">interes</th>
                            </tr>`
                for (let d in data) {
                    linea += `<tr>
                                <td>${data[d].name_client}</td>
                                <td>${data[d].idcard_client}</td>
                                <td>${data[d].estado}</td>
                                <td>${data[d].municipio}</td>
                                <td>${data[d].ciudad}</td>
                                <td>${data[d].monto}</td>
                                <td>${data[d].id_tipo_doc}</td>
                                <td>${data[d].id_contrato}</td>
                                <td>${data[d].fec_emi}</td>
                                <td>${data[d].fec_ven}</td>
                                <td>${data[d].mora}</td>
                                <td>${data[d].interes}</td>
                            </tr>`;
                }
                linea += `</table>`;
                //cargo la tabla con datos
                tabla.innerHTML = linea;
                //console.log(linea);

                //habilito la de consulta de montos, y la region
                if (document.getElementById('btnConsulta').disabled) {
                    document.getElementById('btnConsulta').disabled = false
                }
            });

        }

        function validoNumeros(e) {
            key = e.keyCode || e.which;
            tecla = String.fromCharCode(key).toLowerCase();
            letras = "0123456789";
            especiales = [];

            tecla_especial = false
            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
        }
        function validoEmail(e) {
            key = e.keyCode || e.which;
            tecla = String.fromCharCode(key).toLowerCase();
            letras = "0123456789abcdefghijklmnopqrstuvwxyz@.";
            especiales = [];

            tecla_especial = false
            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
        }
        function validoLetras(e) {
            key = e.keyCode || e.which;
            tecla = String.fromCharCode(key).toLowerCase();
            letras = "abcdefghijklmnopqrstuvwxyz";
            especiales = [];

            tecla_especial = false
            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
        }
        function validoLetrasNumeros(e) {
            key = e.keyCode || e.which;
            tecla = String.fromCharCode(key).toLowerCase();
            letras = "abcdefghijklmnopqrstuvwxyz123456789-";
            especiales = [];

            tecla_especial = false
            for (var i in especiales) {
                if (key == especiales[i]) {
                    tecla_especial = true;
                    break;
                }
            }

            if (letras.indexOf(tecla) == -1 && !tecla_especial)
                return false;
        }
    </script>

@endsection
