@extends('layouts.app')

@section('title','usuarios')

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
    <h1 class="mt-4 text-center">Usuarios</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('panel') }}">Inicio</a></li>
        <li class="breadcrumb-item active">Usuarios</li>
    </ol>

    @can('crear-user') {{-- Permiso para crear usuarios --}}
    <div class="mb-4">
        <a href="{{route('users.create')}}">
            <button type="button" class="btn btn-primary">Añadir nuevo usuario</button>
        </a>
    </div>
    @endcan

    <div class="card mb-4"> {{-- Añadido mb-4 para consistencia --}}
        <div class="card-header">
            <i class="fas fa-table me-1"></i> {{-- Icono de tabla Font Awesome --}}
            Tabla de usuarios
        </div>
        <div class="card-body">
            <table id="datatablesSimple" class="table table-striped fs-6"> {{-- fs-6 para texto más pequeño --}}
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $item) {{-- Variable $item para cada usuario --}}
                    <tr>
                        <td>{{$item->name}}</td>
                        <td>{{$item->email}}</td>
                        <td>
                            {{-- Muestra el primer nombre de rol asignado al usuario --}}
                            {{$item->getRoleNames()->first()}}
                        </td>
                        <td>
                            {{-- Grupo de botones Bootstrap para acciones --}}
                            <div class="btn-group" role="group" aria-label="Acciones de usuario">

                                {{-- Botón Editar --}}
                                @can('editar-user')
                                <form action="{{ route('users.edit', ['user' => $item]) }}" method="GET" style="display: inline;">
                                    <button type="submit" class="btn btn-warning btn-sm">Editar</button>
                                </form>
                                @endcan

                                {{-- Botón Eliminar --}}
                                @can('eliminar-user')
                                    {{-- No se permite eliminar al usuario con ID 1 (generalmente el superadmin) --}}
                                    @if ($item->id != 1)
                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#confirmModal-{{$item->id}}" title="Eliminar">
                                        Eliminar
                                    </button>
                                    @endif
                                @endcan
                            </div>
                        </td>
                    </tr>

                    {{-- Modal de Confirmación para Eliminar Usuario --}}
                    {{-- Solo se genera si el usuario no es el ID 1 --}}
                    @if ($item->id != 1)
                    <div class="modal fade" id="confirmModal-{{$item->id}}" tabindex="-1" aria-labelledby="confirmModalLabel-{{$item->id}}" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h1 class="modal-title fs-5" id="confirmModalLabel-{{$item->id}}">Mensaje de confirmación</h1>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    ¿Seguro que quieres eliminar el usuario?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <form action="{{ route('users.destroy',['user'=>$item->id]) }}" method="post" style="display: inline;">
                                        @method('DELETE')
                                        @csrf
                                        <button type="submit" class="btn btn-danger">Confirmar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
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
