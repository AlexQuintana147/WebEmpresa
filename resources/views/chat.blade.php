<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatIA</title>
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
        <div class="flex-1 flex flex-col h-screen">
            <!-- Header -->
            <x-header />

            <!-- Chat Interface -->
            <main class="flex-1 p-6 bg-gradient-to-br from-gray-50 via-gray-100 to-gray-200" x-data="{ message: '', messages: [], scrollToBottom() { this.$nextTick(() => { this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight; }); } }">
                <div class="h-full max-w-7xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
                    <!-- Chat Messages Container -->
                    <div class="h-full flex flex-col">
                        <div class="flex-1 overflow-y-auto p-8 space-y-8" id="chat-messages" x-ref="chatContainer">
                            <!-- Welcome Message -->
                            <div class="flex items-start space-x-4 animate-fade-in">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                        <i class="fas fa-robot text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl py-3 px-5 shadow-sm max-w-3xl transform hover:-translate-y-0.5 transition-transform duration-200">
                                        <p class="text-gray-800 font-medium">¡Hola! Soy tu asistente virtual. ¿En qué puedo ayudarte hoy?</p>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2 mt-1 inline-block">Asistente Virtual</span>
                                </div>
                            </div>

                            <!-- Message Templates -->
                            <template x-for="(msg, index) in messages" :key="index">
                                <div class="flex items-start space-x-4" :class="{'justify-end space-x-reverse': msg.type === 'user'}">
                                    <template x-if="msg.type === 'bot'">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                                <i class="fas fa-robot text-white text-lg"></i>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="flex-1" :class="{'text-right': msg.type === 'user'}">
                                        <div class="inline-block rounded-2xl py-3 px-5 shadow-sm max-w-3xl transform hover:-translate-y-0.5 transition-transform duration-200"
                                             :class="{
                                                'bg-gradient-to-br from-blue-50 to-blue-100': msg.type === 'bot',
                                                'bg-gradient-to-br from-green-50 to-green-100': msg.type === 'user'
                                             }">
                                            <p class="text-gray-800" x-text="msg.text"></p>
                                        </div>
                                        <span class="text-xs text-gray-500 mx-2 mt-1 inline-block" x-text="msg.type === 'bot' ? 'Asistente Virtual' : 'Tú'"></span>
                                    </div>
                                    <template x-if="msg.type === 'user'">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-green-500 to-green-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                                <i class="fas fa-user text-white text-lg"></i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input -->
                        <div class="border-t border-gray-200 p-6 bg-gradient-to-b from-white to-gray-50">
                            <form @submit.prevent="if(message.trim()) { messages.push({type: 'user', text: message}); messages.push({type: 'bot', text: 'Gracias por tu mensaje. Te responderé en breve.'}); scrollToBottom(); message = ''; }" class="flex space-x-6">
                                <div class="flex-1 relative">
                                    <input 
                                        type="text" 
                                        x-model="message"
                                        class="w-full px-8 py-4 bg-gray-50 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/20 focus:border-blue-500 transition-all duration-300 shadow-sm placeholder-gray-400"
                                        placeholder="Escribe tu mensaje aquí..."
                                    >
                                    <div class="absolute right-4 top-4 text-gray-400">
                                        <i class="fas fa-keyboard text-lg"></i>
                                    </div>
                                </div>
                                <button 
                                    type="submit"
                                    class="px-10 py-4 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-500/30 transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                                    :disabled="!message.trim()"
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

    <style>
        @keyframes fade-in {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fade-in 0.5s ease-out;
        }
    </style>
</body>
</html>