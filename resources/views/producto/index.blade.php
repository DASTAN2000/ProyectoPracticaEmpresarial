@extends('layouts.app')

@section('title','Productos')

@push('css-datatable')
<link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
@endpush

@push('css')
{{-- SweetAlert2 --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
{{-- Font Awesome - Asegúrate de que esté cargado en tu layout principal (layouts.app) si lo usas en otras partes --}}
{{-- Ejemplo: <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"> --}}
@endpush

@section('content')

@include('layouts.partials.alert') {{-- Para mostrar alertas generales --}}

{{-- Script para SweetAlert2 Toast Notificaciones --}}
@if (session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "{{ session('success') }}"
            });
        });
    </script>
@endif
 
<div class="container-fluid px-4">
    <h1 class="mt-4 text-center">Productos</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Productos</li>
    </ol>

    @can('crear-producto') {{-- Permiso para crear productos --}}
    <div class="mb-4">
        <a href="{{route('productos.create')}}">
            <button type="button" class="btn btn-primary">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    <div class="card mb-4"> {{-- Añadido mb-4 para consistencia --}}
        <div class="card-header">
            <i class="fas fa-table me-1"></i> {{-- Icono de tabla Font Awesome --}}
            Tabla productos
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped fs-6"> {{-- fs-6 para texto más pequeño --}}
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Marca</th>
                        <th>Presentación</th>
                        <th>Categorías</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($productos as $item) {{-- Variable $item para cada producto --}}
                    <tr>
                        <td>
                            {{$item->codigo}}
                        </td>
                        <td>
                            {{$item->nombre}}
                        </td>
                        <td>
                            {{$item->marca->caracteristica->nombre}}
                        </td>
                        <td>
                            {{$item->presentacione->caracteristica->nombre}}
                        </td>
                        <td>
                            @foreach ($item->categorias as $category)
                            <div class="container" style="font-size: small;">
                                <div class="row">
                                    {{-- Usar d-inline-block para que los badges se alineen mejor si hay varios --}}
                                    <span class="m-1 rounded-pill p-1 bg-secondary text-white text-center d-inline-block">{{$category->caracteristica->nombre}}</span>
                                </div>
                            </div>
                            @endforeach
                        </td>
                        <td>
                            @if ($item->estado == 1)
                            <span class="badge rounded-pill text-bg-success">activo</span>
                            @else
                            <span class="badge rounded-pill text-bg-danger">eliminado</span>
                            @endif
                        </td>
                        <td>
                            {{-- Grupo de botones Bootstrap para acciones --}}
                            <div class="btn-group" role="group" aria-label="Acciones de producto">

                                {{-- Botón Ver (abre modal) --}}
                                @can('ver-producto')
                                <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#verModal-{{$item->id}}" title="Ver">
                                    Ver
                                </button>
                                @endcan

                                {{-- Botón Editar --}}
                                @can('editar-producto')
                                <form action="{{ route('productos.edit', ['producto' => $item]) }}" method="GET" style="display: inline;">
                                    <button type="submit" class="btn btn-warning btn-sm">Editar</button>
                                </form>
                                @endcan

                                {{-- Botón Eliminar o Restaurar --}}
                                @can('eliminar-producto')
                                    @if ($item->estado == 1)
                                    {{-- Botón para Eliminar (abre modal) --}}
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Eliminar">
                                        Eliminar
                                    </button>
                                    @else
                                    {{-- Botón para Restaurar (abre modal) --}}
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Restaurar">
                                        Restaurar
                                    </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>

                    {{-- Modal para Ver Producto --}}
                    @can('ver-producto')
                    <div class="modal fade" id="verModal-{{$item->id}}" tabindex="-1" aria-labelledby="verModalLabel-{{$item->id}}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="verModalLabel-{{$item->id}}">Detalles del producto</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-12 mb-2"> {{-- mb-2 para un poco de espacio --}}
                                            <p><span class="fw-bolder">Código: </span>{{$item->codigo}}</p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Nombre: </span>{{$item->nombre}}</p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Descripción: </span>{{$item->descripcion}}</p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Marca: </span>{{$item->marca->caracteristica->nombre}}</p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Presentación: </span>{{$item->presentacione->caracteristica->nombre}}</p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Categorías: </span>
                                                @foreach ($item->categorias as $index => $category)
                                                    {{$category->caracteristica->nombre}}{{$index < count($item->categorias) - 1 ? ', ' : ''}}
                                                @endforeach
                                            </p>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Fecha de vencimiento: </span>{{$item->fecha_vencimiento=='' ? 'No tiene' : \Carbon\Carbon::parse($item->fecha_vencimiento)->format('d/m/Y')}}</p> {{-- Formato de fecha --}}
                                        </div>
                                        <div class="col-12 mb-2">
                                            <p><span class="fw-bolder">Stock: </span>{{$item->stock}}</p>
                                        </div>
                                        <div class="col-12">
                                            <p class="fw-bolder">Imagen:</p>
                                            <div>
                                                @if ($item->img_path != null)
                                                {{-- Asegúrate que la ruta Storage::url sea accesible públicamente y esté bien configurada --}}
                                                <img src="{{ Storage::url('public/productos/'.$item->img_path) }}" alt="{{$item->nombre}}" class="img-fluid img-thumbnail border border-4 rounded" style="max-height: 300px;">
                                                @else
                                                <p>No hay imagen disponible.</p> {{-- Mensaje si no hay imagen --}}
                                                {{-- <img src="" alt="{{$item->nombre}}"> Opcional: Placeholder si prefieres --}}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endcan

                    {{-- Modal de Confirmación para Eliminar/Restaurar --}}
                    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-labelledby="confirmModalLabel-{{$item->id}}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="confirmModalLabel-{{$item->id}}">Mensaje de confirmación</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ $item->estado == 1 ? '¿Seguro que quieres eliminar el producto?' : '¿Seguro que quieres restaurar el producto?' }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <form action="{{ route('productos.destroy',['producto'=>$item->id]) }}" method="post" style="display: inline;">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('js')
<script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script> {{-- Asegúrate que este archivo exista --}}
@endpush
