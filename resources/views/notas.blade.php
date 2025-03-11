<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inversiones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            })
        })
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1">
            <!-- Header -->
            <x-header />
            
            <!-- Main Content Area -->
            <main class="p-6" x-data="{ newNote: {title: '', content: ''}, notes: [{id: 1, title: 'Reunión importante', content: 'Reunión con el cliente el viernes a las 10:00 AM', date: '10 Jun 2024', color: 'blue'}, {id: 2, title: 'Pago de facturas', content: 'Recordar pagar las facturas de servicios antes del día 15', date: '12 Jun 2024', color: 'red'}, {id: 3, title: 'Ideas para proyecto', content: 'Investigar nuevas tecnologías para implementar en el próximo proyecto', date: '15 Jun 2024', color: 'green'}] }">
                <div class="max-w-7xl mx-auto">
                    <!-- Page Title -->
                    <div class="mb-10 text-center">
                        <h1 class="text-4xl font-bold text-gray-800 mb-4">Mis Notas</h1>
                        <p class="text-xl text-gray-600">Crea y organiza tus notas y recordatorios personales</p>
                    </div>

                    <!-- Notes Management Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Add New Note Form -->
                        <div class="lg:col-span-1">
                            <div class="bg-white rounded-xl shadow-md p-6 sticky top-6">
                                <h2 class="text-xl font-semibold mb-4 flex items-center">
                                    <i class="fas fa-plus-circle text-blue-500 mr-2"></i> Nueva Nota
                                </h2>
                                <form @submit.prevent="notes.push({id: Date.now(), title: newNote.title, content: newNote.content, date: new Date().toLocaleDateString('es-ES', {day: '2-digit', month: 'short', year: 'numeric'}), color: ['blue', 'green', 'red', 'purple', 'yellow'][Math.floor(Math.random() * 5)]}); newNote.title = ''; newNote.content = '';" class="space-y-4">
                                    <div>
                                        <label for="note-title" class="block text-sm font-medium text-gray-700 mb-1">Título</label>
                                        <input type="text" id="note-title" x-model="newNote.title" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Título de la nota" required>
                                    </div>
                                    <div>
                                        <label for="note-content" class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                        <textarea id="note-content" x-model="newNote.content" rows="5" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Escribe tu nota aquí..." required></textarea>
                                    </div>
                                    <button type="submit" class="w-full px-4 py-2 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-medium rounded-lg hover:from-blue-600 hover:to-blue-700 focus:ring-4 focus:ring-blue-300/50 shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all duration-300 ease-out active:scale-95">
                                        <i class="fas fa-save mr-2"></i> Guardar Nota
                                    </button>
                                </form>
                            </div>
                        </div>

                        <!-- Notes List -->
                        <div class="lg:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <template x-for="note in notes" :key="note.id">
                                    <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border-t-4" :class="{
                                        'border-blue-500': note.color === 'blue',
                                        'border-green-500': note.color === 'green',
                                        'border-red-500': note.color === 'red',
                                        'border-purple-500': note.color === 'purple',
                                        'border-yellow-500': note.color === 'yellow'
                                    }">
                                        <div class="p-6">
                                            <div class="flex justify-between items-start mb-4">
                                                <h3 class="font-bold text-lg text-gray-800" x-text="note.title"></h3>
                                                <div class="flex space-x-2">
                                                    <button class="text-gray-400 hover:text-blue-500 transition-colors">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="text-gray-400 hover:text-red-500 transition-colors" @click="notes = notes.filter(n => n.id !== note.id)">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <p class="text-gray-600 mb-4" x-text="note.content"></p>
                                            <div class="flex justify-between items-center text-sm text-gray-500">
                                                <span><i class="far fa-calendar-alt mr-1"></i> <span x-text="note.date"></span></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>