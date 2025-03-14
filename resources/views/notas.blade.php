<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Inversiones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>[x-cloak] { display: none !important; }</style>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            })
            Alpine.store('editModal', {
                open: false,
                note: null
            })
            Alpine.store('deleteModal', {
                open: false,
                note: null
            })
            Alpine.store('notification', {
                show: false,
                message: '',
                type: 'success',
                timeout: null,
                showNotification(message, type = 'success') {
                    this.message = message;
                    this.type = type;
                    this.show = true;
                    
                    // Clear any existing timeout
                    if (this.timeout) {
                        clearTimeout(this.timeout);
                    }
                    
                    // Auto-hide after 3 seconds
                    this.timeout = setTimeout(() => {
                        this.show = false;
                    }, 3000);
                }
            })
        })
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Notification Component -->
    <div x-data x-cloak
         x-show="$store.notification.show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed top-4 right-4 z-50 max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden"
         :class="{
            'bg-green-50': $store.notification.type === 'success',
            'bg-red-50': $store.notification.type === 'error',
            'bg-amber-50': $store.notification.type === 'warning'
         }">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas" :class="{
                        'fa-check-circle text-green-400': $store.notification.type === 'success',
                        'fa-exclamation-circle text-red-400': $store.notification.type === 'error',
                        'fa-exclamation-triangle text-amber-400': $store.notification.type === 'warning'
                    }"></i>
                </div>
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-medium" :class="{
                        'text-green-800': $store.notification.type === 'success',
                        'text-red-800': $store.notification.type === 'error',
                        'text-amber-800': $store.notification.type === 'warning'
                    }" x-text="$store.notification.message"></p>
                </div>
                <div class="ml-4 flex-shrink-0 flex">
                    <button @click="$store.notification.show = false" class="bg-transparent rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="sr-only">Cerrar</span>
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Main Content Area -->
            <main class="p-6" x-data="{ 
                newNote: {title: '', content: '', category: 'personal', color: 'blue', isPinned: false}, 
                notes: @auth
                    [
                        @foreach($notas as $nota)
                            {
                                id: {{ $nota->id }},
                                title: '{{ addslashes($nota->titulo) }}',
                                content: '{{ addslashes($nota->contenido) }}',
                                category: '{{ $nota->categoria }}',
                                color: '{{ $nota->color }}',
                                date: '{{ $nota->created_at->format('d M Y') }}',
                                isPinned: {{ $nota->isPinned ? 'true' : 'false' }},
                                isArchived: {{ $nota->isArchived ? 'true' : 'false' }}
                            }@if(!$loop->last),@endif
                        @endforeach
                    ]
                @else
                    // For non-logged-in users, load notes from localStorage or use example notes
                    JSON.parse(localStorage.getItem('guestNotes')) || [
                        {id: 1, title: 'Reunión importante', content: 'Reunión con el cliente el viernes a las 10:00 AM', date: '10 Jun 2024', color: 'blue', category: 'trabajo', isPinned: true, isArchived: false}, 
                        {id: 2, title: 'Pago de facturas', content: 'Recordar pagar las facturas de servicios antes del día 15', date: '12 Jun 2024', color: 'red', category: 'finanzas', isPinned: false, isArchived: false}, 
                        {id: 3, title: 'Ideas para proyecto', content: 'Investigar nuevas tecnologías para implementar en el próximo proyecto', date: '15 Jun 2024', color: 'green', category: 'trabajo', isPinned: false, isArchived: false}
                    ]
                @endauth,
                categories: ['personal', 'trabajo', 'finanzas', 'ideas', 'otros'],
                colors: ['blue', 'green', 'red', 'purple', 'yellow', 'teal', 'orange', 'pink'],
                searchQuery: '',
                activeFilter: 'all',
                showArchived: false,
                togglePin(note) {
                    note.isPinned = !note.isPinned;
                },
                toggleArchive(note) {
                    note.isArchived = !note.isArchived;
                },
                filteredNotes() {
                    return this.notes.filter(note => {
                        // Filter by search query
                        const matchesSearch = this.searchQuery === '' || 
                            note.title.toLowerCase().includes(this.searchQuery.toLowerCase()) || 
                            note.content.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                            note.category.toLowerCase().includes(this.searchQuery.toLowerCase());
                        
                        // Filter by category
                        const matchesCategory = this.activeFilter === 'all' || note.category === this.activeFilter;
                        
                        // Filter by archive status
                        const matchesArchiveStatus = (this.showArchived && note.isArchived) || (!this.showArchived && !note.isArchived);
                        
                        return matchesSearch && matchesCategory && matchesArchiveStatus;
                    }).sort((a, b) => {
                        // Sort pinned notes first
                        if (a.isPinned && !b.isPinned) return -1;
                        if (!a.isPinned && b.isPinned) return 1;
                        return 0;
                    });
                }
            }">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Title -->
                    <div class="mb-6 text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Mis Notas</h1>
                        <p class="text-xl text-gray-600">Crea y organiza tus notas y recordatorios personales</p>
                    </div>

                    @guest
                    <!-- Warning for non-authenticated users -->
                    <div class="bg-amber-100 border-l-4 border-amber-500 text-amber-700 p-4 mb-6 rounded-md shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-amber-500 mr-2"></i>
                            </div>
                            <div>
                                <p class="font-medium">Estas son notas de ejemplo</p>
                                <p class="text-sm">Inicia sesión para crear y gestionar tus propias notas.</p>
                            </div>
                        </div>
                    </div>
                    @endguest

                    <!-- Search and Filter Bar -->
                    <div class="bg-white rounded-xl shadow-md p-4 mb-8">
                        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                            <!-- Search Bar -->
                            <div class="relative flex-grow">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-search text-gray-400"></i>
                                </div>
                                <input type="text" x-model="searchQuery" class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Buscar notas...">
                            </div>
                            
                            <!-- Category Filter -->
                            <div class="flex items-center space-x-2">
                                <label class="text-sm font-medium text-gray-700">Categoría:</label>
                                <select x-model="activeFilter" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="all">Todas</option>
                                    <template x-for="category in categories">
                                        <option :value="category" x-text="category.charAt(0).toUpperCase() + category.slice(1)"></option>
                                    </template>
                                </select>
                            </div>
                            
                            <!-- Archive Toggle -->
                            <div class="flex items-center">
                                <button @click="showArchived = !showArchived" class="flex items-center px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                    <i class="fas" :class="showArchived ? 'fa-box-open text-purple-500' : 'fa-archive text-gray-600'"></i>
                                    <span class="ml-2" x-text="showArchived ? 'Ver Notas Activas' : 'Ver Archivadas'"></span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Management Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Add New Note Form -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                                <h2 class="text-xl font-semibold mb-4 flex items-center">
                                    <i class="fas fa-plus-circle text-blue-500 mr-2"></i> Nueva Nota
                                </h2>
                                <form @submit.prevent="@auth fetch('/notas', { 
                                method: 'POST', 
                                headers: { 
                                'Content-Type': 'application/json', 
                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
                                }, 
                                body: JSON.stringify({ 
                                titulo: newNote.title, 
                                contenido: newNote.content, 
                                categoria: newNote.category, 
                                color: newNote.color, 
                                isPinned: newNote.isPinned 
                                }) 
                                }).then(response => {
                                if (!response.ok) {
                                throw new Error('Error en la respuesta del servidor');
                                }
                                // Check if the response is JSON before trying to parse it
                                const contentType = response.headers.get('content-type');
                                if (contentType && contentType.includes('application/json')) {
                                  return response.json();
                                } else {
                                  // If not JSON, just reload the page as the form was likely submitted successfully
                                  window.location.reload();
                                  return { success: true };
                                }
                                }).then(data => { 
                                if(data.success) { 
                                $store.notification.showNotification('Nota creada correctamente', 'success');
                                window.location.reload(); 
                                } else { 
                                $store.notification.showNotification(data.message || 'Error al crear la nota', 'error');
                                } 
                                }).catch(error => {
                                console.error('Error:', error);
                                $store.notification.showNotification('Error al crear la nota: ' + error.message, 'error');
                                }); @else notes.push({id: Date.now(), title: newNote.title, content: newNote.content, category: newNote.category, color: newNote.color, date: new Date().toLocaleDateString('es-ES', {day: '2-digit', month: 'short', year: 'numeric'}), isPinned: newNote.isPinned, isArchived: false}); @endauth newNote.title = ''; newNote.content = '';" class="space-y-4">
                                    <div>
                                        <label for="note-title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                        <input type="text" id="note-title" x-model="newNote.title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Título de la nota" required>
                                    </div>
                                    <div>
                                        <label for="note-content" class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                        <textarea id="note-content" x-model="newNote.content" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe tu nota aquí..." required></textarea>
                                    </div>
                                    
                                    <!-- Category Selection -->
                                    <div>
                                        <label for="note-category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                        <select id="note-category" x-model="newNote.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                            <template x-for="category in categories">
                                                <option :value="category" x-text="category.charAt(0).toUpperCase() + category.slice(1)"></option>
                                            </template>
                                        </select>
                                    </div>
                                    
                                    <!-- Color Selection -->
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                        <div class="flex flex-wrap gap-2">
                                            <template x-for="color in colors">
                                                <button type="button" @click="newNote.color = color" 
                                                    class="w-8 h-8 rounded-full border-2 transition-all duration-200" 
                                                    :class="{
                                                        'border-blue-500 bg-blue-500': color === 'blue',
                                                        'border-green-500 bg-green-500': color === 'green',
                                                        'border-red-500 bg-red-500': color === 'red',
                                                        'border-purple-500 bg-purple-500': color === 'purple',
                                                        'border-yellow-500 bg-yellow-500': color === 'yellow',
                                                        'border-teal-500 bg-teal-500': color === 'teal',
                                                        'border-orange-500 bg-orange-500': color === 'orange',
                                                        'border-pink-500 bg-pink-500': color === 'pink',
                                                        'ring-4 ring-offset-2': newNote.color === color
                                                    }"></button>
                                            </template>
                                        </div>
                                    </div>
                                    
                                    <!-- Pin Option -->
                                    <div class="flex items-center">
                                        <input id="pin-note" type="checkbox" x-model="newNote.isPinned" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <label for="pin-note" class="ml-2 text-sm font-medium text-gray-700">Fijar nota</label>
                                    </div>
                                    
                                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:ring-4 focus:ring-blue-300/50 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 ease-out active:scale-95">
                                        <i class="fas fa-save mr-2"></i> Guardar Nota
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Notes List -->
                        <div class="lg:col-span-2">
                            @auth
                                @if(count($notas ?? []) === 0)
                                    <!-- Empty state for authenticated users with no notes -->
                                    <div class="flex flex-col items-center justify-center h-64 bg-white rounded-xl shadow-md p-6 text-center">
                                        <div class="text-gray-400 mb-4">
                                            <i class="fas fa-sticky-note text-5xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">No tienes notas todavía</h3>
                                        <p class="text-gray-500 mb-4">Crea tu primera nota utilizando el formulario de la izquierda</p>
                                        <div class="text-blue-500">
                                            <i class="fas fa-arrow-left animate-pulse text-xl"></i>
                                        </div>
                                    </div>
                                @else
                                    <!-- Check if there are notes but none are displayed because they're all archived -->
                                    <template x-if="!showArchived && notes.length > 0 && filteredNotes().length === 0">
                                        <div class="flex flex-col items-center justify-center h-64 bg-white rounded-xl shadow-md p-6 text-center">
                                            <div class="text-gray-400 mb-4">
                                                <i class="fas fa-archive text-5xl"></i>
                                            </div>
                                            <h3 class="text-xl font-semibold text-gray-700 mb-2">Tienes tus notas archivadas</h3>
                                            <p class="text-gray-500 mb-4">Crea una nueva nota utilizando el formulario de la izquierda o haz clic en "Ver Archivadas" para ver tus notas archivadas</p>
                                            <div class="flex space-x-4">
                                                <div class="text-blue-500">
                                                    <i class="fas fa-arrow-left animate-pulse text-xl"></i>
                                                </div>
                                                <button @click="showArchived = true" class="text-purple-500 hover:text-purple-700 transition-colors duration-200">
                                                    <i class="fas fa-box-open mr-1"></i> Ver Archivadas
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                    
                                    <div x-show="filteredNotes().length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <template x-for="note in filteredNotes()" :key="note.id">
                                            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border-t-4" :class="{
                                                'border-blue-500': note.color === 'blue',
                                                'border-green-500': note.color === 'green',
                                                'border-red-500': note.color === 'red',
                                                'border-purple-500': note.color === 'purple',
                                                'border-yellow-500': note.color === 'yellow',
                                                'border-teal-500': note.color === 'teal',
                                                'border-orange-500': note.color === 'orange',
                                                'border-pink-500': note.color === 'pink',
                                                'ring-4 ring-blue-300': note.isPinned
                                            }">
                                @endif
                            @else
                                <!-- Check if there are notes but none are displayed because they're all archived (for guest users) -->
                                <template x-if="!showArchived && notes.length > 0 && filteredNotes().length === 0">
                                    <div class="flex flex-col items-center justify-center h-64 bg-white rounded-xl shadow-md p-6 text-center">
                                        <div class="text-gray-400 mb-4">
                                            <i class="fas fa-archive text-5xl"></i>
                                        </div>
                                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Tienes tus notas archivadas</h3>
                                        <p class="text-gray-500 mb-4">Crea una nueva nota utilizando el formulario de la izquierda o haz clic en "Ver Archivadas" para ver tus notas archivadas</p>
                                        <div class="flex space-x-4">
                                            <div class="text-blue-500">
                                                <i class="fas fa-arrow-left animate-pulse text-xl"></i>
                                            </div>
                                            <button @click="showArchived = true" class="text-purple-500 hover:text-purple-700 transition-colors duration-200">
                                                <i class="fas fa-box-open mr-1"></i> Ver Archivadas
                                            </button>
                                        </div>
                                    </div>
                                </template>
                                
                                <div x-show="filteredNotes().length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <template x-for="note in filteredNotes()" :key="note.id">
                                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border-t-4" :class="{
                                            'border-blue-500': note.color === 'blue',
                                            'border-green-500': note.color === 'green',
                                            'border-red-500': note.color === 'red',
                                            'border-purple-500': note.color === 'purple',
                                            'border-yellow-500': note.color === 'yellow',
                                            'border-teal-500': note.color === 'teal',
                                            'border-orange-500': note.color === 'orange',
                                            'border-pink-500': note.color === 'pink',
                                            'ring-4 ring-blue-300': note.isPinned
                                        }">
                            @endauth
                                        <!-- Only show note content if we're in the notes loop -->
                                        <template x-if="note">
                                            <div class="p-6">
                                                <div class="flex justify-between items-start mb-4">
                                                    <div>
                                                        <h3 class="font-bold text-lg text-gray-800" x-text="note.title"></h3>
                                                        <div class="flex items-center mt-1 space-x-2">
                                                            <span class="px-2 py-1 text-xs font-medium rounded-full" 
                                                                :class="{
                                                                    'bg-blue-100 text-blue-800': note.category === 'personal',
                                                                    'bg-green-100 text-green-800': note.category === 'trabajo',
                                                                    'bg-red-100 text-red-800': note.category === 'finanzas',
                                                                    'bg-purple-100 text-purple-800': note.category === 'ideas',
                                                                    'bg-gray-100 text-gray-800': note.category === 'otros'
                                                                }" x-text="note.category.charAt(0).toUpperCase() + note.category.slice(1)"></span>
                                                            <span x-show="note.isPinned" class="flex items-center text-xs text-blue-600">
                                                                <i class="fas fa-thumbtack mr-1"></i> Fijada
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <!-- Action Buttons -->
                                                    <!-- Action Buttons -->
                                                    <div class="flex space-x-1">
                                                        <!-- Edit Button -->
                                                        <button @click.stop="
                                                            @auth
                                                            $store.editModal.open = true; 
                                                            $store.editModal.note = JSON.parse(JSON.stringify(note))
                                                            @else
                                                            $store.notification.showNotification('Esto es solo una muestra, inicie sesión para usar', 'warning')
                                                            @endauth
                                                        " class="p-1 text-gray-500 hover:text-blue-500 transition-colors duration-200" title="Editar nota">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <!-- Pin/Unpin Button -->
                                                        <button @click.stop="
                                                            @auth
                                                            fetch(`/notas/${note.id}/toggle-pin`, {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if(data.success) {
                                                                    note.isPinned = data.isPinned;
                                                                    $store.notification.showNotification(
                                                                        note.isPinned ? 'Nota fijada correctamente' : 'Nota desfijada correctamente', 
                                                                        'success'
                                                                    );
                                                                } else {
                                                                    $store.notification.showNotification(data.message || 'Error al actualizar la nota', 'error');
                                                                }
                                                            })
                                                            .catch(error => {
                                                                console.error('Error:', error);
                                                                $store.notification.showNotification('Error al actualizar la nota: ' + error.message, 'error');
                                                            })
                                                            @else
                                                            note.isPinned = !note.isPinned;
                                                            // Update the note in localStorage
                                                            let localNotes = JSON.parse(localStorage.getItem('guestNotes') || '[]');
                                                            const noteIndex = localNotes.findIndex(n => n.id === note.id);
                                                            if (noteIndex !== -1) {
                                                                localNotes[noteIndex] = note;
                                                                localStorage.setItem('guestNotes', JSON.stringify(localNotes));
                                                            }
                                                            $store.notification.showNotification(
                                                                note.isPinned ? 'Nota fijada correctamente' : 'Nota desfijada correctamente', 
                                                                'success'
                                                            );
                                                            @endauth" 
                                                            class="p-1 transition-colors duration-200" 
                                                            :class="note.isPinned ? 'text-blue-500 hover:text-blue-700' : 'text-gray-500 hover:text-blue-500'" 
                                                            title="Fijar/Desfijar nota">
                                                            <i class="fas fa-thumbtack"></i>
                                                        </button>
                                                        <!-- Archive/Unarchive Button -->
                                                        <button @click.stop="
                                                            @auth
                                                            fetch(`/notas/${note.id}/toggle-archive`, {
                                                                method: 'POST',
                                                                headers: {
                                                                    'Content-Type': 'application/json',
                                                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                                                                }
                                                            })
                                                            .then(response => response.json())
                                                            .then(data => {
                                                                if(data.success) {
                                                                    note.isArchived = data.isArchived;
                                                                    $store.notification.showNotification(
                                                                        note.isArchived ? 'Nota archivada correctamente' : 'Nota desarchivada correctamente', 
                                                                        'success'
                                                                    );
                                                                } else {
                                                                    $store.notification.showNotification(data.message || 'Error al actualizar la nota', 'error');
                                                                }
                                                            })
                                                            .catch(error => {
                                                                console.error('Error:', error);
                                                                $store.notification.showNotification('Error al actualizar la nota: ' + error.message, 'error');
                                                            })
                                                            @else
                                                            note.isArchived = !note.isArchived;
                                                            // Update the note in localStorage
                                                            let localNotes = JSON.parse(localStorage.getItem('guestNotes') || '[]');
                                                            const noteIndex = localNotes.findIndex(n => n.id === note.id);
                                                            if (noteIndex !== -1) {
                                                                localNotes[noteIndex] = note;
                                                                localStorage.setItem('guestNotes', JSON.stringify(localNotes));
                                                            }
                                                            $store.notification.showNotification(
                                                                note.isArchived ? 'Nota archivada correctamente' : 'Nota desarchivada correctamente', 
                                                                'success'
                                                            );
                                                            @endauth" 
                                                            class="p-1 transition-colors duration-200" 
                                                            :class="note.isArchived ? 'text-purple-500 hover:text-purple-700' : 'text-gray-500 hover:text-purple-500'" 
                                                            title="Archivar/Desarchivar nota">
                                                            <i class="fas" :class="note.isArchived ? 'fa-box-open' : 'fa-archive'"></i>
                                                        </button>
                                                        <!-- Delete Button -->
                                                        <button @click.stop="$store.deleteModal.open = true; $store.deleteModal.note = JSON.parse(JSON.stringify(note))" 
                                                            class="p-1 text-gray-500 hover:text-red-500 transition-colors duration-200" 
                                                            title="Eliminar nota">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <p class="text-gray-600 mb-4" x-text="note.content"></p>
                                                <div class="flex justify-between items-center text-sm text-gray-500">
                                                    <span><span x-text="note.date"></span></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Note Detail Modal -->
                    <div x-data="{ showModal: false, currentNote: null }" @keydown.escape="showModal = false">
                        <!-- Trigger -->
                        <template x-for="note in notes">
                            <div @click.stop="currentNote = note; showModal = true" x-show="false"></div>
                        </template>
                        
                        <!-- Modal -->
                        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                            <div class="flex items-center justify-center min-h-screen px-4">
                                <!-- Backdrop -->
                                <div x-show="showModal" @click="showModal = false" class="fixed inset-0 transition-opacity">
                                    <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                                </div>
                                
                                <!-- Modal Content -->
                                <div x-show="showModal" class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-2xl transform transition-all sm:max-w-lg" @click.stop>
                                    <div class="border-t-4" :class="{
                                        'border-blue-500': currentNote?.color === 'blue',
                                        'border-green-500': currentNote?.color === 'green',
                                        'border-red-500': currentNote?.color === 'red',
                                        'border-purple-500': currentNote?.color === 'purple',
                                        'border-yellow-500': currentNote?.color === 'yellow',
                                        'border-teal-500': currentNote?.color === 'teal',
                                        'border-orange-500': currentNote?.color === 'orange',
                                        'border-pink-500': currentNote?.color === 'pink'
                                    }"></div>
                                    
                                    <!-- Header -->
                                    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                        <h3 class="text-xl font-semibold text-gray-800" x-text="currentNote?.title"></h3>
                                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-600">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Body -->
                                    <div class="px-6 py-4">
                                        <div class="flex items-center space-x-2 mb-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded-full" 
                                                :class="{
                                                    'bg-blue-100 text-blue-800': currentNote?.category === 'personal',
                                                    'bg-green-100 text-green-800': currentNote?.category === 'trabajo',
                                                    'bg-red-100 text-red-800': currentNote?.category === 'finanzas',
                                                    'bg-purple-100 text-purple-800': currentNote?.category === 'ideas',
                                                    'bg-gray-100 text-gray-800': currentNote?.category === 'otros'
                                                }" x-text="currentNote?.category?.charAt(0).toUpperCase() + currentNote?.category?.slice(1)"></span>
                                            <span class="text-sm text-gray-500"><i class="far fa-calendar-alt mr-1"></i> <span x-text="currentNote?.date"></span></span>
                                        </div>
                                        <p class="text-gray-600 whitespace-pre-line" x-text="currentNote?.content"></p>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="px-6 py-4 bg-gray-50 flex justify-between">
                                        <div class="flex space-x-2">
                                            <button @click="togglePin(currentNote); showModal = false" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                                <i class="fas" :class="currentNote?.isPinned ? 'fa-thumbtack text-blue-500' : 'fa-thumbtack text-gray-600'"></i>
                                                <span class="ml-2" x-text="currentNote?.isPinned ? 'Desfijar' : 'Fijar'"></span>
                                            </button>
                                            <button @click="toggleArchive(currentNote); showModal = false" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200 flex items-center">
                                                <i class="fas" :class="currentNote?.isArchived ? 'fa-box-open text-purple-500' : 'fa-archive text-gray-600'"></i>
                                                <span class="ml-2" x-text="currentNote?.isArchived ? 'Desarchivar' : 'Archivar'"></span>
                                            </button>
                                        </div>
                                        <div class="flex space-x-2">
                                            <button @click="showModal = false" class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors duration-200">
                                                <i class="fas fa-times mr-1"></i> Cerrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Delete Note Modal -->
                    <div x-cloak x-show="$store.deleteModal.open" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <!-- Backdrop -->
                            <div @click="$store.deleteModal.open = false" class="fixed inset-0 transition-opacity">
                                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                            </div>
                            
                            <!-- Modal Content -->
                            <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-md transform transition-all" @click.stop>
                                <div class="border-t-4 border-red-500"></div>
                                
                                <!-- Header -->
                                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-xl font-semibold text-gray-800">Eliminar Nota</h3>
                                    <button @click="$store.deleteModal.open = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Content -->
                                <div class="px-6 py-4">
                                    <div class="flex items-center mb-4 text-red-500">
                                        <i class="fas fa-exclamation-triangle text-2xl mr-3"></i>
                                        <p class="text-gray-700">¿Estás seguro de que deseas eliminar esta nota? Esta acción no se puede deshacer.</p>
                                    </div>
                                    <div class="bg-gray-50 p-3 rounded-lg mb-4">
                                        <h4 class="font-medium text-gray-800" x-text="$store.deleteModal.note?.title"></h4>
                                    </div>
                                </div>
                                
                                <!-- Footer -->
                                <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-3">
                                    <button @click="$store.deleteModal.open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors duration-200">
                                        Cancelar
                                    </button>
                                    <button @click="
                                        @auth
                                        fetch(`/notas/${$store.deleteModal.note.id}`, {
                                            method: 'POST',
                                            headers: {
                                                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                                'Content-Type': 'application/json'
                                            },
                                            body: JSON.stringify({
                                                _method: 'DELETE'
                                            })
                                        })
                                        .then(response => {
                                            if(response.ok) {
                                                $store.notification.showNotification('Nota eliminada correctamente', 'success');
                                                window.location.reload();
                                            } else {
                                                $store.notification.showNotification('Error al eliminar la nota', 'error');
                                            }
                                        })
                                        .catch(error => {
                                            console.error('Error:', error);
                                            $store.notification.showNotification('Error al eliminar la nota: ' + error.message, 'error');
                                        });
                                        
                                        $store.deleteModal.open = false;
                                        @else
                                        // For non-logged-in users, show a warning message instead of deleting
                                        $store.notification.showNotification('Esto solo es una muestra, para que funcione inicie sesión', 'warning');
                                        $store.deleteModal.open = false;
                                        @endauth
                                    " class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors duration-200">
                                        Eliminar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Edit Note Modal -->
                    <div x-cloak x-show="$store.editModal.open" class="fixed inset-0 z-50 overflow-y-auto">
                        <div class="flex items-center justify-center min-h-screen px-4">
                            <!-- Backdrop -->
                            <div @click="$store.editModal.open = false" class="fixed inset-0 transition-opacity">
                                <div class="absolute inset-0 bg-gray-900 opacity-75"></div>
                            </div>
                            
                            <!-- Modal Content -->
                            <div class="bg-white rounded-xl shadow-2xl overflow-hidden w-full max-w-2xl transform transition-all sm:max-w-lg" @click.stop>
                                <div class="border-t-4" :class="{
                                    'border-blue-500': $store.editModal.note?.color === 'blue',
                                    'border-green-500': $store.editModal.note?.color === 'green',
                                    'border-red-500': $store.editModal.note?.color === 'red',
                                    'border-purple-500': $store.editModal.note?.color === 'purple',
                                    'border-yellow-500': $store.editModal.note?.color === 'yellow',
                                    'border-teal-500': $store.editModal.note?.color === 'teal',
                                    'border-orange-500': $store.editModal.note?.color === 'orange',
                                    'border-pink-500': $store.editModal.note?.color === 'pink'
                                }"></div>
                                
                                <!-- Header -->
                                <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-xl font-semibold text-gray-800">Editar Nota</h3>
                                    <button @click="$store.editModal.open = false" class="text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                
                                <!-- Form -->
                                <form @submit.prevent="
                                    @auth
                                    fetch(`/notas/${$store.editModal.note.id}`, { 
                                        method: 'POST', 
                                        headers: { 
                                            'Content-Type': 'application/json', 
                                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content 
                                        }, 
                                        body: JSON.stringify({ 
                                            _method: 'PUT',
                                            titulo: $store.editModal.note.title, 
                                            contenido: $store.editModal.note.content, 
                                            categoria: $store.editModal.note.category, 
                                            color: $store.editModal.note.color, 
                                            isPinned: $store.editModal.note.isPinned,
                                            isArchived: $store.editModal.note.isArchived
                                        }) 
                                    })
                                    .then(response => {
                                        if (!response.ok) {
                                            throw new Error('Error en la respuesta del servidor');
                                        }
                                        // Check if the response is JSON before trying to parse it
                                        const contentType = response.headers.get('content-type');
                                        if (contentType && contentType.includes('application/json')) {
                                            return response.json().then(data => {
                                                if(data.success) {
                                                    $store.notification.showNotification('Nota editada correctamente', 'success');
                                                } else {
                                                    $store.notification.showNotification(data.message || 'Error al actualizar la nota', 'error');
                                                }
                                                window.location.reload();
                                            });
                                        } else {
                                            // If not JSON, just reload the page as the form was likely submitted successfully
                                            $store.notification.showNotification('Nota editada correctamente', 'success');
                                            window.location.reload();
                                        }
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        $store.notification.showNotification('Error al actualizar la nota: ' + error.message, 'error');
                                    });
                                    @else
                                    // For non-logged-in users, update the note in the local array and localStorage
                                    const noteIndex = notes.findIndex(n => n.id === $store.editModal.note.id);
                                    if (noteIndex !== -1) {
                                        notes[noteIndex] = {
                                            ...$store.editModal.note,
                                            // Ensure we keep the original ID and date
                                            id: notes[noteIndex].id,
                                            date: notes[noteIndex].date
                                        };
                                        
                                        // Update localStorage
                                        let localNotes = JSON.parse(localStorage.getItem('guestNotes') || '[]');
                                        const localIndex = localNotes.findIndex(n => n.id === $store.editModal.note.id);
                                        if (localIndex !== -1) {
                                            localNotes[localIndex] = notes[noteIndex];
                                        } else {
                                            localNotes.push(notes[noteIndex]);
                                        }
                                        localStorage.setItem('guestNotes', JSON.stringify(localNotes));
                                        
                                        $store.notification.showNotification('Nota editada correctamente', 'success');
                                    }
                                    @endauth
                                    $store.editModal.open = false;
                                ">
                                    <div class="px-6 py-4 space-y-4">
                                        <!-- Title -->
                                        <div>
                                            <label for="edit-note-title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                            <input type="text" id="edit-note-title" x-model="$store.editModal.note.title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Título de la nota" required>
                                        </div>
                                        
                                        <!-- Content -->
                                        <div>
                                            <label for="edit-note-content" class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                            <textarea id="edit-note-content" x-model="$store.editModal.note.content" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe tu nota aquí..." required></textarea>
                                        </div>
                                        
                                        <!-- Category Selection -->
                                        <div>
                                            <label for="edit-note-category" class="block text-sm font-medium text-gray-700 mb-1">Categoría</label>
                                            <select id="edit-note-category" x-model="$store.editModal.note.category" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                <template x-for="category in categories">
                                                    <option :value="category" x-text="category.charAt(0).toUpperCase() + category.slice(1)"></option>
                                                </template>
                                            </select>
                                        </div>
                                        
                                        <!-- Color Selection -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Color</label>
                                            <div class="flex flex-wrap gap-2">
                                                <template x-for="color in colors">
                                                    <button type="button" @click="$store.editModal.note.color = color" 
                                                        class="w-8 h-8 rounded-full border-2 transition-all duration-200" 
                                                        :class="{
                                                            'border-blue-500 bg-blue-500': color === 'blue',
                                                            'border-green-500 bg-green-500': color === 'green',
                                                            'border-red-500 bg-red-500': color === 'red',
                                                            'border-purple-500 bg-purple-500': color === 'purple',
                                                            'border-yellow-500 bg-yellow-500': color === 'yellow',
                                                            'border-teal-500 bg-teal-500': color === 'teal',
                                                            'border-orange-500 bg-orange-500': color === 'orange',
                                                            'border-pink-500 bg-pink-500': color === 'pink',
                                                            'ring-4 ring-offset-2': $store.editModal.note.color === color
                                                        }"></button>
                                                </template>
                                            </div>
                                        </div>
                                        
                                        <!-- Options -->
                                        <div class="flex space-x-4">
                                            <!-- Pin Option -->
                                            <div class="flex items-center">
                                                <input id="edit-pin-note" type="checkbox" x-model="$store.editModal.note.isPinned" class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                                <label for="edit-pin-note" class="ml-2 text-sm font-medium text-gray-700">Fijar nota</label>
                                            </div>
                                            
                                            <!-- Archive Option -->
                                            <div class="flex items-center">
                                                <input id="edit-archive-note" type="checkbox" x-model="$store.editModal.note.isArchived" class="w-4 h-4 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                                                <label for="edit-archive-note" class="ml-2 text-sm font-medium text-gray-700">Archivar nota</label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Footer -->
                                    <div class="px-6 py-4 bg-gray-50 flex justify-end space-x-2">
                                        <button type="button" @click="$store.editModal.open = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-lg transition-colors duration-200">
                                            Cancelar
                                        </button>
                                        <button type="submit" class="px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:ring-4 focus:ring-blue-300/50 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 ease-out active:scale-95">
                                            <i class="fas fa-save mr-2"></i> Guardar Cambios
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Notes Button -->
                    <div class="fixed bottom-6 right-6">
                        <div class="flex flex-col space-y-2">
                            <button class="bg-gradient-to-r from-blue-500 to-blue-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300" title="Exportar notas">
                                <i class="fas fa-file-export"></i>
                            </button>
                            <button class="bg-gradient-to-r from-purple-500 to-purple-600 text-white p-3 rounded-full shadow-lg hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300" title="Imprimir notas">
                                <i class="fas fa-print"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>