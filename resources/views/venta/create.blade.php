@extends('layouts.app')

@section('title','Realizar venta')

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
        width: 3em; /* Ancho del switch */
        height: 1.5em; /* Alto del switch */
    }
    .form-check-label {
        cursor: pointer;
        padding-left: 0.5em; /* Espacio entre switch y label */
    }
    /* Ajustar el tamaño del texto dentro del label si es necesario */
    .form-check-label span {
        vertical-align: middle;
    }
     /* Estilo para campos deshabilitados pero legibles */
    input:disabled {
        background-color: #e9ecef !important; /* Color de fondo Bootstrap para deshabilitado */
        opacity: 1 !important; /* Asegurar que el texto sea completamente visible */
        color: #495057 !important; /* Color de texto Bootstrap */
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Realizar Venta</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item"><a href="{{ route('ventas.index')}}">Ventas</a></li>
        <li class="breadcrumb-item active">Realizar Venta</li>
    </ol>
</div>

<form action="{{ route('ventas.store') }}" method="post">
    @csrf
    <div class="container-lg mt-4">
        <div class="row gy-4">

            <div class="col-xl-8">
                <div class="text-white bg-primary p-1 text-center">
                    Detalles de la venta
                </div>
                <div class="p-3 border border-3 border-primary">
                    <div class="row gy-3"> {{-- gy-3 para espacio vertical entre filas del grid --}}

                        <div class="col-12">
                            <label for="producto_id" class="form-label">Producto:</label>
                            <select name="producto_id" id="producto_id" class="form-control selectpicker" data-live-search="true" data-size="3" title="Busque un producto aquí">
                                @foreach ($productos as $item)
                                {{-- El value contiene: id-stock-precio_venta --}}
                                <option value="{{$item->id}}-{{$item->stock}}-{{$item->precio_venta}}">{{$item->codigo.' '.$item->nombre}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-sm-6 offset-sm-6"> {{-- Alineado a la derecha --}}
                             <label for="stock" class="form-label">Stock disponible:</label>
                             <input disabled id="stock" type="number" class="form-control">
                        </div>


                        <div class="col-sm-4">
                            <label for="precio_venta" class="form-label">Precio de venta:</label>
                            <input disabled type="number" id="precio_venta_display" class="form-control" step="0.01">
                             {{-- Este es solo para mostrar, el valor real se maneja en JS --}}
                        </div>

                        <div class="col-sm-4">
                            <label for="cantidad" class="form-label">Cantidad:</label>
                            <input type="number" name="cantidad" id="cantidad" class="form-control" min="1">
                        </div>

                        <div class="col-sm-4">
                            <label for="descuento" class="form-label">Descuento (por unidad):</label>
                            <input type="number" name="descuento" id="descuento" class="form-control" step="0.01" value="0" min="0">
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button id="btn_agregar" class="btn btn-primary" type="button">Agregar</button>
                        </div>

                        <div class="col-12 mt-3">
                            <div class="table-responsive">
                                <table id="tabla_detalle" class="table table-hover">
                                    <thead class="bg-primary">
                                        <tr>
                                            <th class="text-white">#</th>
                                            <th class="text-white">Producto</th>
                                            <th class="text-white">Cantidad</th>
                                            <th class="text-white">Precio Unit.</th>
                                            <th class="text-white">Desc. Unit.</th>
                                            <th class="text-white">Subtotal</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td colspan="7" class="text-center">Aún no hay productos agregados</td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Subtotal Venta (sin IVA)</th>
                                            <th colspan="2"><span id="sumas_sin_iva">0.00</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">IVA (<span id="iva_percentage_display_footer"></span>%)</th>
                                            <th colspan="2"><span id="igv_valor">0.00</span></th>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <th colspan="4">Total Venta</th>
                                            <th colspan="2">
                                                <input type="hidden" name="total" value="0" id="inputTotalVenta">
                                                <span id="total_general_venta">0.00</span>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>

                        <div class="col-12 mt-3">
                            <button id="cancelar" type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                Cancelar venta
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
                    <div class="row gy-3"> {{-- gy-3 para espacio vertical --}}
                        <div class="col-12">
                            <label for="cliente_id" class="form-label">Cliente:</label>
                            <select name="cliente_id" id="cliente_id" class="form-control selectpicker show-tick" data-live-search="true" title="Selecciona un cliente" data-size='3'>
                                @foreach ($clientes as $item)
                                <option value="{{$item->id}}">{{$item->persona->razon_social}}</option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="comprobante_id" class="form-label">Comprobante:</label>
                            <select name="comprobante_id" id="comprobante_id" class="form-control selectpicker" title="Selecciona un comprobante">
                                @foreach ($comprobantes as $item)
                                <option value="{{$item->id}}">{{$item->tipo_comprobante}}</option>
                                @endforeach
                            </select>
                            @error('comprobante_id')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label for="numero_comprobante" class="form-label">Número de comprobante:</label>
                            <input required type="text" name="numero_comprobante" id="numero_comprobante" class="form-control">
                            @error('numero_comprobante')
                            <small class="text-danger">{{ '*'.$message }}</small>
                            @enderror
                        </div>
                        
                        <div class="col-sm-6">
                            <label for="tasa_iva_display_info" class="form-label">Tasa IVA (%):</label>
                            <input readonly type="text" id="tasa_iva_display_info" class="form-control border-success">
                        </div>

                        <div class="col-sm-6">
                            <label for="fecha" class="form-label">Fecha:</label>
                            <input readonly type="date" name="fecha" id="fecha" class="form-control border-success" value="<?php echo date("Y-m-d") ?>">
                            <?php
                            use Carbon\Carbon;
                            $fecha_hora = Carbon::now()->toDateTimeString();
                            ?>
                            <input type="hidden" name="fecha_hora" value="{{$fecha_hora}}">
                        </div>

                        <div class="col-12 mt-2 mb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="aplicarIvaSwitchVenta" name="aplicar_iva_switch_venta" value="1" checked>
                                <label class="form-check-label" for="aplicarIvaSwitchVenta">Aplicar IVA (<span id="iva_percentage_label_switch_venta"></span>%)</label>
                            </div>
                        </div>
                        
                        {{-- El nombre 'impuesto' es el que tu backend espera para el monto del IVA --}}
                        <input type="hidden" name="impuesto" id="inputImpuestoMontoVenta" value="0">


                        <input type="hidden" name="user_id" value="{{ auth()->user()->id }}">

                        <div class="col-12 text-center mt-3">
                            <button type="submit" class="btn btn-success" id="guardarVenta">Realizar venta</button>
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
                    ¿Seguro que quieres cancelar la venta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button id="btnCancelarVenta" type="button" class="btn btn-danger" data-bs-dismiss="modal">Confirmar</button>
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
        // Inicializar todos los selectpicker
        $('.selectpicker').selectpicker();

        // Configuración inicial del IVA para ventas
        $('#iva_percentage_label_switch_venta').text(TASA_IVA_VENTA);
        $('#aplicarIvaSwitchVenta').prop('checked', true); // Por defecto, aplicar IVA

        // Event listeners
        $('#producto_id').change(mostrarValoresProductoSeleccionado);
        $('#btn_agregar').click(agregarProductoDetalle);
        $('#btnCancelarVenta').click(cancelarVentaCompleta);
        $('#aplicarIvaSwitchVenta').change(function() {
            actualizarCalculosGlobalesVenta();
            // No es necesario llamar a disableButtons aquí a menos que el cambio de IVA afecte si se puede guardar.
        });

        // Estado inicial de botones y cálculos
        disableBotonesAccion();
        actualizarCalculosGlobalesVenta();

        // Mensaje inicial en la tabla de detalles
        if (itemsVenta.length === 0) {
            $('#tabla_detalle tbody').html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
        }
    });

    // --- VARIABLES GLOBALES Y CONSTANTES ---
    let itemsVenta = []; // Array para almacenar los detalles de los productos de la venta
    let contadorFilasVenta = 0; // Para asignar IDs únicos a las filas
    const TASA_IVA_VENTA = 19; // Tasa de IVA (ej. 19 para 19%)

    // --- FUNCIONES AUXILIARES ---
    function round(num, decimales = 2) {
        if (isNaN(num) || num === null) return (0).toFixed(decimales); // Manejo de NaN o null
        var signo = (num >= 0 ? 1 : -1);
        num = Math.abs(num); // Usar Math.abs para simplificar
        // Corrección para evitar problemas con números muy pequeños y notación científica
        num = parseFloat(num.toPrecision(15)); 
        num = Math.round(num * Math.pow(10, decimales)) / Math.pow(10, decimales);
        return (signo * num).toFixed(decimales);
    }

    function showModal(message, icon = 'error') {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3500, // Un poco más de tiempo
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
        Toast.fire({ icon: icon, title: message });
    }

    // --- LÓGICA DE PRODUCTO SELECCIONADO ---
    function mostrarValoresProductoSeleccionado() {
        const selectedOption = $('#producto_id').val();
        if (!selectedOption) {
            $('#stock').val('');
            $('#precio_venta_display').val('');
            return;
        }
        const dataProducto = selectedOption.split('-'); // value="id-stock-precio_venta"
        $('#stock').val(dataProducto[1]); // Asigna el stock
        $('#precio_venta_display').val(round(parseFloat(dataProducto[2]))); // Asigna el precio de venta formateado
    }

    // --- LÓGICA DE LA TABLA DE DETALLES ---
    function agregarProductoDetalle() {
        const selectedOption = $('#producto_id').val();
        if (!selectedOption) {
            showModal('Seleccione un producto.', 'warning');
            return;
        }
        const dataProducto = selectedOption.split('-');
        const idProducto = dataProducto[0];
        const nombreProducto = $('#producto_id option:selected').text();
        const stockDisponible = parseInt(dataProducto[1]);
        const precioVentaUnitario = parseFloat(dataProducto[2]);

        let cantidad = parseInt($('#cantidad').val());
        let descuentoUnitario = parseFloat($('#descuento').val()) || 0; // Si está vacío o no es número, es 0

        // Validaciones
        if (isNaN(cantidad) || cantidad <= 0) {
            showModal('Ingrese una cantidad válida (número entero mayor a 0).', 'error');
            return;
        }
        if (isNaN(descuentoUnitario) || descuentoUnitario < 0) {
            showModal('El descuento no puede ser negativo.', 'error');
            $('#descuento').val(0); // Resetea a 0 si es inválido
            return;
        }
        if (descuentoUnitario >= precioVentaUnitario) {
            showModal('El descuento no puede ser igual o mayor al precio de venta.', 'error');
            return;
        }
        if (cantidad > stockDisponible) {
            showModal('La cantidad solicitada excede el stock disponible (' + stockDisponible + ').', 'error');
            return;
        }
        if (itemsVenta.some(item => item.idProducto === idProducto)) {
            showModal('Este producto ya ha sido agregado. Puede modificar la cantidad o eliminarlo y volver a agregarlo.', 'info');
            return;
        }

        // Calcular subtotal del ítem
        const subtotalItem = round((precioVentaUnitario - descuentoUnitario) * cantidad);

        // Agregar al array de itemsVenta
        itemsVenta.push({
            idFila: contadorFilasVenta,
            idProducto: idProducto,
            nombreProducto: nombreProducto,
            cantidad: cantidad,
            precioVentaUnitario: precioVentaUnitario,
            descuentoUnitario: descuentoUnitario,
            subtotal: parseFloat(subtotalItem) // Guardar como número
        });

        // Actualizar UI de la tabla
        if (itemsVenta.length === 1) { // Si es el primer producto
            $('#tabla_detalle tbody').empty();
        }

        const filaHtml = `
            <tr id="filaVenta${contadorFilasVenta}">
                <th>${itemsVenta.length}</th>
                <td><input type="hidden" name="arrayidproducto[]" value="${idProducto}">${nombreProducto}</td>
                <td><input type="hidden" name="arraycantidad[]" value="${cantidad}">${cantidad}</td>
                <td><input type="hidden" name="arrayprecioventa[]" value="${precioVentaUnitario}">${round(precioVentaUnitario)}</td>
                <td><input type="hidden" name="arraydescuento[]" value="${descuentoUnitario}">${round(descuentoUnitario)}</td>
                <td>${subtotalItem}</td>
                <td><button class="btn btn-danger btn-sm" type="button" onclick="eliminarProductoDetalle(${contadorFilasVenta})"><i class="fas fa-trash"></i></button></td>
            </tr>`;
        $('#tabla_detalle tbody').append(filaHtml);

        contadorFilasVenta++;
        limpiarCamposEntradaProducto();
        actualizarCalculosGlobalesVenta();
        disableBotonesAccion();
        actualizarNumeracionFilasVenta();
    }

    function eliminarProductoDetalle(idFila) {
        itemsVenta = itemsVenta.filter(item => item.idFila !== idFila);
        $(`#filaVenta${idFila}`).remove();

        if (itemsVenta.length === 0) {
            $('#tabla_detalle tbody').html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
            // contadorFilasVenta podría resetearse aquí si se desea, pero no es estrictamente necesario
            // ya que los IDs de fila son únicos incluso si se eliminan y agregan nuevos.
        } else {
            actualizarNumeracionFilasVenta();
        }
        
        actualizarCalculosGlobalesVenta();
        disableBotonesAccion();
    }
    
    function actualizarNumeracionFilasVenta() {
        $('#tabla_detalle tbody tr').each(function(index) {
            // Solo actualiza si no es la fila de "no hay productos"
            if ($(this).find('td').length > 1) { 
                 $(this).find('th:first').text(index + 1);
            }
        });
    }

    // --- CÁLCULOS GLOBALES (SUB TOTALES, IVA, TOTAL GENERAL) ---
    function actualizarCalculosGlobalesVenta() {
        let sumasNetas = 0; // Suma de subtotales de items (precio * cantidad - descuento)
        itemsVenta.forEach(item => {
            sumasNetas += item.subtotal;
        });

        let montoIvaCalculado = 0;
        const aplicarIva = $('#aplicarIvaSwitchVenta').is(':checked');
        let ivaPorcentajeAplicadoFooter = 0;

        if (aplicarIva) {
            montoIvaCalculado = round(sumasNetas * (TASA_IVA_VENTA / 100));
            ivaPorcentajeAplicadoFooter = TASA_IVA_VENTA;
            $('#tasa_iva_display_info').val(TASA_IVA_VENTA + '%');
        } else {
            montoIvaCalculado = 0;
            ivaPorcentajeAplicadoFooter = 0;
            $('#tasa_iva_display_info').val('0%');
        }

        const totalGeneralVenta = round(sumasNetas + parseFloat(montoIvaCalculado));

        // Actualizar UI del footer de la tabla y campos hidden
        $('#sumas_sin_iva').text(round(sumasNetas));
        $('#igv_valor').text(round(montoIvaCalculado));
        $('#iva_percentage_display_footer').text(ivaPorcentajeAplicadoFooter);
        $('#total_general_venta').text(totalGeneralVenta);
        $('#inputTotalVenta').val(totalGeneralVenta); // Para el backend
        $('#inputImpuestoMontoVenta').val(round(montoIvaCalculado)); // Para el backend (name="impuesto")
    }

    // --- ACCIONES GENERALES DEL FORMULARIO ---
    function cancelarVentaCompleta() {
        itemsVenta = [];
        contadorFilasVenta = 0; // Resetear contador de filas

        $('#tabla_detalle tbody').empty().html('<tr><td colspan="7" class="text-center">Aún no hay productos agregados</td></tr>');
        
        // Resetear switch de IVA
        $('#aplicarIvaSwitchVenta').prop('checked', true);
        $('#iva_percentage_label_switch_venta').text(TASA_IVA_VENTA);
        
        actualizarCalculosGlobalesVenta(); // Esto pondrá los totales a 0
        limpiarCamposEntradaProducto();
        limpiarCamposGeneralesVenta(); // Limpiar cliente, comprobante, etc.
        disableBotonesAccion();
    }

    function limpiarCamposEntradaProducto() {
        $('#producto_id').selectpicker('val', ''); // Limpiar selectpicker
        $('#stock').val('');
        $('#precio_venta_display').val('');
        $('#cantidad').val('');
        $('#descuento').val('0'); // Resetear descuento a 0
    }

    function limpiarCamposGeneralesVenta() {
        $('#cliente_id').selectpicker('val', '');
        $('#comprobante_id').selectpicker('val', '');
        $('#numero_comprobante').val('');
        // La fecha y el usuario se mantienen.
    }

    function disableBotonesAccion() {
        const totalVentaActual = parseFloat($('#inputTotalVenta').val()) || 0;
        if (totalVentaActual <= 0 || itemsVenta.length === 0) {
            $('#guardarVenta').hide();
            $('#cancelar').hide(); // El botón de cancelar general
        } else {
            $('#guardarVenta').show();
            $('#cancelar').show();
        }
    }

</script>
@endpush
