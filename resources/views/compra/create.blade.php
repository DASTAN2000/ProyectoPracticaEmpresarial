@extends('layouts.app')

@section('title','Realizar compra')

@push('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/css/bootstrap-select.min.css">
{{-- Font Awesome para el icono de basura y potencialmente otros --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    /* Estilos adicionales para el switch si es necesario */
    .form-switch .form-check-input {
        cursor: pointer;
    }
    .form-check-label {
        cursor: pointer;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Crear Compra</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('compras.index')}}">Compras</a></li>
        <li class="breadcrumb-item active">Crear Compra</li>
    </ol>
</div>

<form action="{{ route('compras.store') }}" method="post">
    @csrf

    <div class="container-lg mt-4">
        <div class="row gy-4">
            <div class="col-xl-8">
                <div class="text-white bg-primary p-1 text-center">
                    Detalles de la compra
                </div>
                <div class="p-3 border border-3 border-primary">
                    <div class="row">
                        <div class="col-12 mb-4">
                            <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="3" title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                <option value="{{$item->id}}">{{$item->codigo.' '.$item->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control">
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="precio_compra" class="form-label">Precio de compra:</label>
                            <input type="number" name="precio_compra" id="precio_compra" class="form-control" step="0.01">
                        </div>

                        <div class="col-sm-4 mb-2">
                            <label for="precio_venta" class="form-label">Precio de venta:</label>
                            <input type="number" name="precio_venta" id="precio_venta" class="form-control" step="0.01">
                        </div>

                        <div class="col-12 mb-4 mt-2 text-end">
                            <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                        </div>

                        <div class="col-12">
                            <div class="table-responsive">
                                <table id="tabla_detalle" class="table table-hover">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Producto</th>
                                            <th class="text-white">Cantidad</th>
                                            <th class="text-white">Precio compra</th>
                                            <th class="text-white">Precio venta</th>
                                            <th class="text-white">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Fila inicial vacía o con mensaje --}}
                                        <tr>
                                            <td colspan="7" class="text-center">Aún no hay productos agregados</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Sumas</th>
                                            <th colspan="2"><span id="sumas">0.00</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">IVA (<span id="iva_percentage_display_footer"></span>%)</th> {{-- Muestra el % de IVA aplicado en el footer --}}
                                            <th colspan="2"><span id="igv_valor">0.00</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Total</th>
                                            <th colspan="2">
                                                <input type="hidden" name="total" value="0" id="inputTotal">
                                                <span id="total">0.00</span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-2">
                            <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Cancelar compra
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-xl-4">
                <div class="text-white bg-success p-1 text-center">
                    Datos generales
                </div>
                <div class="p-3 border border-3 border-success">
                    <div class="row">
                        <div class="col-12 mb-2">
                            <label for="proveedore_id" class="form-label">Proveedor:</label>
                            <select name="proveedore_id" id="proveedore_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona" data-size='2'>
                                @foreach ($proveedores as $item)
                                <option value="{{$item->id}}">{{$item->persona->razon_social}}</option>
                                @endforeach
                            </select>
                            @error('proveedore_id')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-2">
                            <label for="comprobante_id" class="form-label">Comprobante:</label>
                            <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker" title="Selecciona">
                                @foreach ($comprobantes as $item)
                                <option value="{{$item->id}}">{{$item->tipo_comprobante}}</option>
                                @endforeach
                            </select>
                            @error('comprobante_id')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <div class="col-12 mb-2">
                            <label for="numero_comprobante" class="form-label">Numero de comprobante:</label>
                            <input required type="text" name="numero_comprobante" id="numero_comprobante" class="form-control">
                            @error('numero_comprobante')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6 mb-2">
                            <label for="tasa_iva_display_info" class="form-label">Tasa IVA (%):</label>
                            <input readonly type="text" id="tasa_iva_display_info" class="form-control border-success">
                        </div>

                        <div class="col-sm-6 mb-2">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input readonly type="date" name="fecha" id="fecha" class="form-control border-success" value="<?php echo date("Y-m-d") ?>">
                            <?php
                            use Carbon\Carbon;
                            $fecha_hora = Carbon::now()->toDateTimeString();
                            ?>
                            <input type="hidden" name="fecha_hora" value="{{$fecha_hora}}">
                        </div>

                        <div class="col-12 mb-3 mt-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="aplicarIvaSwitch" name="aplicar_iva_switch" value="1" checked>
                                <label class="form-check-label" for="aplicarIvaSwitch">Aplicar IVA (<span id="iva_percentage_label_switch"></span>%)</label>
                            </div>
                        </div>
                        
                        <input type="hidden" name="impuesto" id="inputImpuestoMonto" value="0">
                        {{-- Este campo 'impuesto' es el que probablemente espera tu StoreCompraRequest para el monto del IVA --}}


                        <div class="col-12 mt-4 text-center">
                            <button type="submit" class="btn btn-success" id="guardar">Realizar compra</button>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Advertencia</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Seguro que quieres cancelar la compra?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarCompra" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
                </div>
            </div>
        </div>
    </div>

</form>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/bootstrap-select@1.14.0-beta3/dist/js/bootstrap-select.min.js"></script>
<script>
    $(document).ready(function() {
        // Inicializar el selectpicker
        $('.selectpicker').selectpicker();

        // Configuración inicial del IVA
        $('#iva_percentage_label_switch').text(TASA_IVA); // Mostrar la tasa en el label del switch
        $('#aplicarIvaSwitch').prop('checked', true); // Por defecto, aplicar IVA

        $('#btn_agregar').click(function() {
            agregarProducto();
        });

        $('#btnCancelarCompra').click(function() {
            cancelarCompra();
        });

        // Event listener para el switch de IVA
        $('#aplicarIvaSwitch').change(function() {
            actualizarCalculosGenerales();
            disableButtons(); 
        });

        disableButtons();
        actualizarCalculosGenerales(); // Para establecer los valores iniciales de IVA y total
        
        // Fila inicial de la tabla
        if (cont === 0) {
            $('#tabla_detalle tbody').html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
        }
    });

    //Variables Globales
    let cont = 0;
    let detallesVenta = []; // Array para almacenar los detalles de los productos agregados

    //Constantes
    const TASA_IVA = 19; // Tasa de IVA (ej. 19 para 19%)

    // Función para redondear a dos decimales
    function round(num, decimales = 2) {
        var signo = (num >= 0 ? 1 : -1);
        num = num * signo;
        if (decimales === 0) 
            return signo * Math.round(num);
        num = num.toString().split('e');
        num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
        num = num.toString().split('e');
        return signo * (+(num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales))).toFixed(decimales);
    }

    // Función para actualizar todos los cálculos (sumas, IVA, total) y la UI
    function actualizarCalculosGenerales() {
        let sumas = 0;
        detallesVenta.forEach(detalle => {
            sumas += parseFloat(detalle.subtotal);
        });

        let igvCalculado = 0;
        const aplicarIva = $('#aplicarIvaSwitch').is(':checked');
        let ivaPorcentajeAplicado = 0;

        if (aplicarIva) {
            igvCalculado = round(sumas * (TASA_IVA / 100));
            ivaPorcentajeAplicado = TASA_IVA;
            $('#tasa_iva_display_info').val(TASA_IVA + '%'); // Mostrar tasa en el input informativo
        } else {
            igvCalculado = 0;
            ivaPorcentajeAplicado = 0;
            $('#tasa_iva_display_info').val('0%'); // Mostrar 0% en el input informativo
        }

        let totalGeneral = round(parseFloat(sumas) + parseFloat(igvCalculado));

        $('#sumas').html(round(sumas));
        $('#igv_valor').html(round(igvCalculado)); // Actualiza el span del valor del IVA
        $('#iva_percentage_display_footer').text(ivaPorcentajeAplicado); // Actualiza el % de IVA en el footer de la tabla
        $('#total').html(round(totalGeneral));
        $('#inputTotal').val(round(totalGeneral)); // Actualiza el input hidden para el form
        $('#inputImpuestoMonto').val(round(igvCalculado)); // **Importante: Actualiza el input hidden con el monto del IVA para el backend**
    }


    function cancelarCompra() {
        detallesVenta = [];
        cont = 0;

        $('#tabla_detalle tbody').empty().html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
        
        $('#aplicarIvaSwitch').prop('checked', true);
        $('#iva_percentage_label_switch').text(TASA_IVA);

        actualizarCalculosGenerales();
        limpiarCamposProducto();
        disableButtons();
    }

    function disableButtons() {
        let totalActual = parseFloat($('#inputTotal').val()) || 0;
        if (totalActual <= 0 || detallesVenta.length === 0) { // También deshabilita si no hay productos
            $('#guardar').hide();
            $('#cancelar').hide();
        } else {
            $('#guardar').show();
            $('#cancelar').show();
        }
    }

    function agregarProducto() {
        let idProducto = $('#producto_id').val();
        let textoProducto = $('#producto_id option:selected').text();
        let codigoYNombre = textoProducto.split(' ');
        let nombreProducto = codigoYNombre.slice(1).join(' '); 

        let cantidad = $('#cantidad').val();
        let precioCompra = $('#precio_compra').val();
        let precioVenta = $('#precio_venta').val();

        if (!idProducto) {
            showModal('Debe seleccionar un producto.');
            return;
        }
        if (nombreProducto === '' || nombreProducto === undefined || nombreProducto.trim() === codigoYNombre[0]) { // Mejor validación del nombre
            showModal('Error al obtener el nombre del producto o producto no válido.');
            return;
        }
        if (cantidad === '' || precioCompra === '' || precioVenta === '') {
            showModal('Le faltan campos por llenar (Cantidad, Precio Compra o Precio Venta).');
            return;
        }

        cantidad = parseInt(cantidad);
        precioCompra = parseFloat(precioCompra);
        precioVenta = parseFloat(precioVenta);

        if (isNaN(cantidad) || isNaN(precioCompra) || isNaN(precioVenta)) {
            showModal('Valores numéricos incorrectos.');
            return;
        }
        if (cantidad <= 0 || !(cantidad % 1 === 0)) {
            showModal('La cantidad debe ser un número entero mayor a 0.');
            return;
        }
        if (precioCompra <= 0 || precioVenta <= 0) {
            showModal('Los precios deben ser mayores a 0.');
            return;
        }
        if (parseFloat(precioVenta) <= parseFloat(precioCompra)) {
            showModal('El precio de venta debe ser mayor que el precio de compra.');
            return;
        }
        if (detallesVenta.some(detalle => detalle.idProducto === idProducto)) {
            showModal('El producto ya ha sido agregado a la lista.');
            return;
        }
        
        let subtotalProducto = round(cantidad * precioCompra);

        detallesVenta.push({
            id: cont, 
            idProducto: idProducto,
            nombreProducto: nombreProducto,
            cantidad: cantidad,
            precioCompra: precioCompra,
            precioVenta: precioVenta,
            subtotal: subtotalProducto
        });

        if (cont === 0) {
            $('#tabla_detalle tbody').empty();
        }
        
        let fila = '<tr id="fila' + cont + '">' +
            '<th>' + (detallesVenta.length) + '</th>' + // Usar detallesVenta.length para la numeración
            '<td><input type="hidden" name="arrayidproducto[]" value="' + idProducto + '">' + nombreProducto + '</td>' +
            '<td><input type="hidden" name="arraycantidad[]" value="' + cantidad + '">' + cantidad + '</td>' +
            '<td><input type="hidden" name="arraypreciocompra[]" value="' + precioCompra + '">' + round(precioCompra) + '</td>' +
            '<td><input type="hidden" name="arrayprecioventa[]" value="' + precioVenta + '">' + round(precioVenta) + '</td>' +
            '<td>' + subtotalProducto + '</td>' +
            '<td><button class="btn btn-danger btn-sm" type="button" onClick="eliminarProducto(' + cont + ')"><i class="fa-solid fa-trash"></i></button></td>' +
            '</tr>';

        $('#tabla_detalle tbody').append(fila);
        limpiarCamposProducto();
        cont++; // Incrementar el ID único para la próxima fila
        
        actualizarCalculosGenerales();
        actualizarNumeracionFilas(); // Llamar a la función para renumerar
        disableButtons();
    }
    
    function actualizarNumeracionFilas() {
        $('#tabla_detalle tbody tr').each(function(index) {
            $(this).find('th:first').text(index + 1);
        });
    }

    function eliminarProducto(idFila) { // Cambiado a idFila para claridad
        detallesVenta = detallesVenta.filter(item => item.id !== idFila);
        $('#fila' + idFila).remove();

        if (detallesVenta.length === 0) {
            cont = 0; 
            $('#tabla_detalle tbody').html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
        } else {
            actualizarNumeracionFilas(); // Renumerar filas después de eliminar
        }
        
        actualizarCalculosGenerales();
        disableButtons();
    }

    function limpiarCamposProducto() {
        $('#producto_id').selectpicker('val', ''); 
        $('#cantidad').val('');
        $('#precio_compra').val('');
        $('#precio_venta').val('');
    }

    function showModal(message, icon = 'error') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        })

        Toast.fire({
            icon: icon,
            title: message
        })
    }
</script>
@endpush
