<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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

            <!-- Chat Interface -->
            <main class="p-6" x-data="{ message: '', messages: [] }">
                <div class="max-w-6xl mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
                    <!-- Chat Messages Container -->
                    <div class="h-[calc(100vh-12rem)] flex flex-col">
                        <div class="flex-1 overflow-y-auto p-4 space-y-4" id="chat-messages">
                            <!-- Welcome Message -->
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                        <i class="fas fa-robot text-white"></i>
                                    </div>
                                </div>
                                <div class="ml-3 bg-blue-50 rounded-lg py-3 px-4 max-w-3xl">
                                    <p class="text-gray-800">¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?</p>
                                </div>
                            </div>

                            <!-- Message Templates -->
                            <template x-for="(msg, index) in messages" :key="index">
                                <div class="flex items-start" :class="{'justify-end': msg.type === 'user'}">
                                    <template x-if="msg.type === 'bot'">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                <i class="fas fa-robot text-white"></i>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="mx-3" :class="{
                                        'bg-blue-50': msg.type === 'bot',
                                        'bg-green-50': msg.type === 'user'
                                    }" class="rounded-lg py-3 px-4 max-w-3xl">
                                        <p class="text-gray-800" x-text="msg.text"></p>
                                    </div>
                                    <template x-if="msg.type === 'user'">
                                        <div class="flex-shrink-0">
                                            <div class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input -->
                        <div class="border-t border-gray-200 p-4 bg-gray-50">
                            <form @submit.prevent="messages.push({type: 'user', text: message}); messages.push({type: 'bot', text: 'Gracias por tu mensaje. Te responderé en breve.'}); message = ''" class="flex space-x-4">
                                <div class="flex-1">
                                    <input 
                                        type="text" 
                                        x-model="message"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors duration-200"
                                        placeholder="Escribe tu mensaje aquí..."
                                    >
                                </div>
                                <button 
                                    type="submit"
                                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                                >
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Enviar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>