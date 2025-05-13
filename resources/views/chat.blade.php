<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta Virtual - Clínica Ricardo Palma</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            })
        })
    </script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('chatApp', () => ({
                message: '',
                messages: [],
                isLoading: false,
                
                sendMessage() {
                    if (!this.message.trim()) return;
                    
                    // Agregar mensaje del usuario
                    this.messages.push({type: 'user', text: this.message});
                    
                    // Guardar el mensaje para enviarlo
                    const questionText = this.message;
                    
                    // Limpiar el campo de entrada
                    this.message = '';
                    
                    // Mostrar indicador de carga
                    this.isLoading = true;
                    
                    // Hacer scroll hacia abajo
                    this.scrollToBottom();
                    
                    // Enviar la consulta al servidor
                    $.ajax({
                        url: '{{ route("chat.query") }}',
                        type: 'POST',
                        data: {
                            message: questionText,
                            _token: '{{ csrf_token() }}'
                        },
                        success: (response) => {
                            // Ocultar indicador de carga
                            this.isLoading = false;
                            
                            // Depurar la respuesta recibida
                            console.log('Respuesta completa del servidor:', response);
                            
                            // Agregar respuesta del chatbot
                            if (response.success) {
                                console.log('Agregando respuesta exitosa:', response.message);
                                let formattedResponse = response.message;
                                
                                // Verificar si hay caracteres malformados y reemplazarlos
                                formattedResponse = formattedResponse
                                    .replace(/[\u00A0-\u9999<>]/g, function(i) {
                                        return '&#'+i.charCodeAt(0)+';';
                                    })
                                    .replace(/\n/g, '<br>')
                                    .replace(/(-{2,})/g, '<span style="display:inline-block;width:100%;">$1</span>');
                                this.messages.push({type: 'bot', text: formattedResponse, html: true});
                                
                                // Forzar actualización de la vista
                                this.$nextTick(() => {
                                    console.log('Mensajes actualizados:', this.messages);
                                    // Verificar que el elemento del DOM se haya actualizado
                                    const messageElements = document.querySelectorAll('#chat-messages > div');
                                    console.log('Elementos de mensaje en el DOM:', messageElements.length);
                                });
                            } else {
                                console.log('Error en la respuesta:', response.message, response.debug_info);
                                this.messages.push({type: 'bot', text: 'Lo siento, ocurrió un error al procesar tu consulta. Por favor, intenta de nuevo.'});
                            }
                            
                            // Hacer scroll hacia abajo
                            this.scrollToBottom();
                        },
                        error: (xhr) => {
                            this.messages.push({
                                type: 'bot', 
                                text: `Error técnico: ${xhr.responseJSON?.message || 'Consulte los logs'}`
                            });
                            console.error('Detalles completos:', xhr.responseJSON?.debug_info);
                        }
                    });
                },
                
                scrollToBottom() {
                    this.$nextTick(() => {
                        this.$refs.chatContainer.scrollTop = this.$refs.chatContainer.scrollHeight;
                    });
                }
            }))
        })
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        @keyframes pulse-medical {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        .animate-pulse-medical {
            animation: pulse-medical 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-blue-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 flex flex-col h-screen">
            <!-- Header -->
            <x-header />

            <!-- Chat Interface -->
            <main class="flex-1 p-6 bg-gradient-to-br from-blue-50 via-blue-100/30 to-cyan-100/30" x-data="chatApp">
                <div class="h-full max-w-7xl mx-auto bg-white rounded-2xl shadow-2xl overflow-hidden border border-cyan-100">
                    <!-- Chat Messages Container -->
                    <div class="h-full flex flex-col">
                        <div class="flex-1 overflow-y-auto p-8 space-y-8" id="chat-messages" x-ref="chatContainer">
                            <!-- Header Banner -->
                            <div class="mb-8 bg-gradient-to-r from-cyan-600 to-teal-600 rounded-xl p-4 shadow-lg">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-14 w-14 rounded-full bg-white flex items-center justify-center shadow-lg">
                                            <i class="fas fa-stethoscope text-teal-600 text-2xl"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <h2 class="text-white text-xl font-bold">Consulta Médica Virtual</h2>
                                        <p class="text-cyan-100">Atención médica personalizada desde cualquier lugar</p>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Welcome Message -->
                            <div class="flex items-start space-x-4 animate-fade-in">
                                <div class="flex-shrink-0">
                                    <div class="h-12 w-12 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                        <i class="fas fa-user-md text-white text-lg"></i>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="bg-gradient-to-br from-cyan-50 to-teal-50 rounded-2xl py-3 px-5 shadow-sm max-w-3xl transform hover:-translate-y-0.5 transition-transform duration-200 border border-teal-100">
                                        <p class="text-gray-800 font-medium">¡Hola! Soy el Dr. Asistente Virtual de la Clínica Ricardo Palma. ¿En qué puedo ayudarle hoy? Puede consultar sobre síntomas, agendar una cita o solicitar información sobre nuestros servicios.</p>
                                    </div>
                                    <span class="text-xs text-gray-500 ml-2 mt-1 inline-block">Dr. Asistente Virtual</span>
                                </div>
                            </div>

                            <!-- Message Templates -->
                            <template x-for="(msg, index) in messages" :key="index">
                                <div class="flex items-start space-x-4" :class="{'justify-end space-x-reverse': msg.type === 'user'}">
                                    <template x-if="msg.type === 'bot'">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                                <i class="fas fa-user-md text-white text-lg"></i>
                                            </div>
                                        </div>
                                    </template>
                                    <div class="flex-1" :class="{'text-right': msg.type === 'user'}">
                                        <div class="inline-block rounded-2xl py-3 px-5 shadow-sm max-w-3xl transform hover:-translate-y-0.5 transition-transform duration-200"
                                             :class="{
                                                'bg-gradient-to-br from-cyan-50 to-teal-50 border border-teal-100': msg.type === 'bot',
                                                'bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-100': msg.type === 'user'
                                             }">
                                            <p class="text-gray-800" x-html="msg.text"></p>
                                        </div>
                                        <span class="text-xs text-gray-500 mx-2 mt-1 inline-block" x-text="msg.type === 'bot' ? 'Dr. Asistente Virtual' : 'Paciente'"></span>
                                    </div>
                                    <template x-if="msg.type === 'user'">
                                        <div class="flex-shrink-0">
                                            <div class="h-12 w-12 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200">
                                                <i class="fas fa-hospital-user text-white text-lg"></i>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>

                        <!-- Chat Input -->
                        <div class="border-t border-cyan-100 p-6 bg-gradient-to-b from-white to-cyan-50">
                            <div class="mb-4 px-4 py-2 bg-blue-50 border-l-4 border-blue-500 rounded-lg">
                                <p class="text-sm text-blue-800"><i class="fas fa-info-circle mr-2"></i> Esta consulta virtual es solo informativa. Para emergencias médicas, por favor llame al 106 o acuda a urgencias.</p>
                            </div>
                            <div x-show="isLoading" class="mb-4 px-4 py-2 bg-yellow-50 border-l-4 border-yellow-500 rounded-lg animate-pulse-medical">
                                <p class="text-sm text-yellow-800"><i class="fas fa-spinner fa-spin mr-2"></i> El Dr. Asistente Virtual está analizando su consulta. Por favor espere un momento...</p>
                            </div>
                            <form @submit.prevent="sendMessage()" class="flex space-x-6">
                                <div class="flex-1 relative">
                                    <input 
                                        type="text" 
                                        x-model="message"
                                        class="w-full px-8 py-4 bg-white border border-cyan-200 rounded-xl focus:ring-4 focus:ring-teal-500/20 focus:border-teal-500 transition-all duration-300 shadow-sm placeholder-gray-400"
                                        placeholder="Describa sus síntomas o consulta médica aquí..."
                                    >
                                    <div class="absolute left-3 top-4 text-teal-500">
                                        <i class="fas fa-notes-medical text-lg"></i>
                                    </div>
                                </div>
                                <button 
                                    type="submit"
                                    class="px-10 py-4 bg-gradient-to-r from-teal-600 to-cyan-600 text-white rounded-xl hover:from-teal-700 hover:to-cyan-700 focus:outline-none focus:ring-4 focus:ring-teal-500/30 transition-all duration-300 shadow-lg hover:shadow-xl disabled:opacity-50 disabled:cursor-not-allowed transform hover:-translate-y-0.5"
                                    :disabled="!message.trim()"
                                >
                                    <i class="fas fa-paper-plane mr-2"></i>
                                    Enviar Consulta
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