@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h1 class="text-center mb-4">Lista de Tareas</h1>

    <!-- Botón para abrir modal de crear -->
    <div class="text-center mb-4">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#crearModal">Añadir Tarea</button>
    </div>

    <!-- Modal Crear -->
    <div class="modal fade" id="crearModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="crearForm" onsubmit="event.preventDefault(); crearTarea()">
                    <div class="modal-header">
                        <h5 class="modal-title">Nueva Tarea</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" name="titulo" class="form-control mb-3" placeholder="Título" required>
                        <textarea name="descripcion" class="form-control mb-3" placeholder="Descripción"></textarea>
                        <input type="file" name="imagen" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Lista de tareas -->
    <div class="row justify-content-center g-4" id="lista-tareas">
        @foreach ($tareas as $tarea)
        <div class="col-12 col-md-6 col-lg-4" id="tarea-{{ $tarea->id }}">
            <div class="card h-100">
                <div class="card-body text-center">
                    <h5 class="card-title"><strong>{{ $tarea->titulo }}</strong></h5>
                    <p class="text-muted">{{ $tarea->descripcion }}</p>
                    @if ($tarea->imagen)
                    <img src="{{ asset('storage/' . $tarea->imagen) }}" alt="Imagen de la tarea" class="img-fluid rounded mt-3" style="max-height: 200px;">
                    @endif
                </div>
                <div class="card-footer d-flex justify-content-around">
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal-{{ $tarea->id }}">Editar</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarTarea({{ $tarea->id }})">Eliminar</button>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div class="modal fade" id="editarModal-{{ $tarea->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editarForm-{{ $tarea->id }}" onsubmit="event.preventDefault(); actualizarTarea({{ $tarea->id }})">
                        <div class="modal-header">
                            <h5 class="modal-title">Editar Tarea</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <input type="text" name="titulo" class="form-control mb-3" value="{{ $tarea->titulo }}" required>
                            <textarea name="descripcion" class="form-control mb-3">{{ $tarea->descripcion }}</textarea>
                            <input type="file" name="imagen" class="form-control">
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Paginación -->
<div class="d-flex justify-content-center mt-4">
    {{ $tareas->links() }}
</div>
@endsection



<script>
    // Función para cerrar un modal
    function cerrarModal(modalId) {
        let modal = bootstrap.Modal.getInstance(document.getElementById(modalId));
        if (modal) {
            modal.hide();
        }
    }

    // Función para crear una tarea
    function crearTarea() {
        let form = document.getElementById('crearForm');
        let formData = new FormData(form);

        fetch("{{ route('tareas.store') }}", {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                let tareaHtml = `
                <div class="col-md-4 mb-4" id="tarea-${data.id}">
                    <div class="card h-100 text-center">
                        <div class="card-body">
                            <h5 class="card-title"><strong>${data.titulo}</strong></h5>
                            <p class="text-muted">${data.descripcion || ''}</p>
                            ${data.imagen ? `<img src="/storage/${data.imagen}" class="img-fluid rounded mt-3" style="max-height: 200px;">` : ''}
                        </div>
                        <div class="card-footer d-flex justify-content-around">
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarModal-${data.id}">Editar</button>
                            <button class="btn btn-danger btn-sm" onclick="eliminarTarea(${data.id})">Eliminar</button>
                        </div>
                    </div>
                </div>`;
                document.getElementById('lista-tareas').innerHTML += tareaHtml;
                form.reset();
                cerrarModal('crearModal');
            }
        });
    }

    // Función para actualizar una tarea
    function actualizarTarea(id) {
        let form = document.getElementById(`editarForm-${id}`);
        let formData = new FormData(form);

        fetch(`/tareas/${id}`, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-HTTP-Method-Override": "PUT"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data) {
                let tarea = document.getElementById(`tarea-${id}`);
                if (tarea) {
                    tarea.querySelector('.card-title strong').innerText = data.titulo;
                    tarea.querySelector('.text-muted').innerText = data.descripcion || '';
                    if (data.imagen) {
                        let img = tarea.querySelector('img');
                        if (img) {
                            img.src = `/storage/${data.imagen}`;
                        } else {
                            tarea.querySelector('.card-body').innerHTML += `<img src="/storage/${data.imagen}" class="img-fluid rounded mt-3" style="max-height: 200px;">`;
                        }
                    }
                }
                cerrarModal(`editarModal-${id}`);
            }
        });
    }

    // Función para eliminar una tarea
    function eliminarTarea(id) {
        if (confirm("¿Estás seguro de que deseas eliminar esta tarea?")) {
            fetch(`/tareas/${id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-HTTP-Method-Override": "DELETE"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`tarea-${id}`).remove();
                }
            });
        }
    }
</script>
