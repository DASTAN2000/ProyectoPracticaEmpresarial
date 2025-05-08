@extends('layouts.app')

@section('title','categorías')

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

@include('layouts.partials.alert') {{-- Asumiendo que tienes este parcial para mostrar alertas generales --}}
{{-- Script para SweetAlert2 (si se usa para notificaciones de éxito como en el ejemplo) --}}
@if (session('success'))
    <script>
        // Pequeño script para mostrar notificaciones Toast con SweetAlert2
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
    <h1 class="mt-4 text-center">Categorías</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Categorías</li>
    </ol>

    @can('crear-categoria') {{-- Asumiendo que usas Spatie Permissions o similar para los @can --}}
    <div class="mb-4">
        <a href="{{route('categorias.create')}}">
            <button type="button" class="btn btn-primary">Añadir nuevo registro</button>
        </a>
    </div>
    @endcan

    <div class="card mb-4"> {{-- Añadido mb-4 como en el ejemplo para consistencia --}}
        <div class="card-header">
            <i class="fas fa-table me-1"></i> {{-- Font Awesome para el icono de tabla --}}
            Tabla categorías
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped fs-6"> {{-- fs-6 para tamaño de fuente más pequeño --}}
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categorias as $categoria)
                    <tr>
                        <td>
                            {{$categoria->caracteristica->nombre}}
                        </td>
                        <td>
                            {{$categoria->caracteristica->descripcion}}
                        </td>
                        <td>
                            {{-- Mantenemos el estilo de badge anterior, puedes cambiarlo si prefieres el del ejemplo --}}
                            @if ($categoria->caracteristica->estado == 1)
                            <span class="badge rounded-pill text-bg-success">activo</span>
                            @else
                            <span class="badge rounded-pill text-bg-danger">eliminado</span>
                            @endif
                        </td>
                        <td>
                            {{-- Grupo de botones de Bootstrap --}}
                            <div class="btn-group" role="group" aria-label="Acciones de categoría">

                                {{-- Botón Editar como formulario GET --}}
                                @can('editar-categoria')
                                <form action="{{ route('categorias.edit', ['categoria' => $categoria]) }}" method="GET" style="display: inline;">
                                    <button type="submit" class="btn btn-warning btn-sm">Editar</button> {{-- btn-sm para botones más pequeños --}}
                                </form>
                                @endcan

                                {{-- Botón Eliminar o Restaurar --}}
                                @can('eliminar-categoria') {{-- Asumiendo que la misma directiva controla ambas acciones --}}
                                    @if ($categoria->caracteristica->estado == 1)
                                    {{-- Botón para Eliminar (abre modal) --}}
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$categoria->id}}" title="Eliminar">
                                        Eliminar
                                    </button>
                                    @else
                                    {{-- Botón para Restaurar (abre modal) --}}
                                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$categoria->id}}" title="Restaurar">
                                        Restaurar
                                    </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>

                    {{-- Modal de Confirmación (sin cambios funcionales, solo asegurando IDs únicos para aria-labelledby) --}}
                    <div class="modal fade" id="confirmModal-{{$categoria->id}}" tabindex="-1" aria-labelledby="confirmModalLabel-{{$categoria->id}}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="confirmModalLabel-{{$categoria->id}}">Mensaje de confirmación</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    {{ $categoria->caracteristica->estado == 1 ? '¿Seguro que quieres eliminar la categoría?' : '¿Seguro que quieres restaurar la categoría?' }}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    {{-- El formulario de eliminación/restauración sigue igual --}}
                                    <form action="{{ route('categorias.destroy',['categoria'=>$categoria->id]) }}" method="post" style="display: inline;">
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
<script src="{{ asset('js/datatables-simple-demo.js') }}"></script> {{-- Asegúrate que este archivo exista y sea correcto --}}
@endpush
