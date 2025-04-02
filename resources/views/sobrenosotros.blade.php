<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - Clínica Ricardo Palma</title>
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
    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
        @keyframes pulse {
            0%, 100% { opacity: 0.8; }
            50% { opacity: 0.4; }
        }
        @keyframes gradient-x {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-gradient-x {
            animation: gradient-x 15s ease infinite;
            background-size: 200% 200%;
        }
        .text-shadow {
            text-shadow: 0 4px 8px rgba(0, 0, 0, 0.12);
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <x-sidebar />

        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Header -->
            <x-header />

            <!-- Main Content Area -->
            <main class="p-6 md:p-8 lg:p-10">
                <!-- Hero Section -->
                <section class="relative rounded-3xl overflow-hidden mb-16">
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-500/90 to-blue-700/90 mix-blend-multiply"></div>
                    <div class="relative z-10 px-6 py-16 md:py-24 md:px-12 max-w-5xl mx-auto text-center">
                        <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-white mb-6 tracking-tight text-shadow">
                            Bienvenidos a la Clínica Ricardo Palma
                        </h1>
                        <p class="text-xl md:text-2xl text-blue-50 max-w-3xl mx-auto leading-relaxed">
                            Más de 30 años brindando atención médica de excelencia con tecnología de vanguardia y un equipo médico altamente calificado.
                        </p>
                        <div class="mt-10 flex flex-wrap justify-center gap-4">
                            <a href="#historia" class="px-8 py-3 bg-white text-blue-700 rounded-full font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:bg-blue-50 hover:-translate-y-1">
                                Nuestra Historia
                            </a>
                            <a href="#especialidades" class="px-8 py-3 bg-blue-800 text-white rounded-full font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:bg-blue-900 hover:-translate-y-1 border border-blue-400/30">
                                Especialidades Médicas
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Historia Section -->
                <section id="historia" class="mb-20 scroll-mt-24">
                    <div class="max-w-7xl mx-auto">
                        <div class="flex flex-col md:flex-row items-center gap-12">
                            <div class="md:w-1/2">
                                <div class="relative">
                                    <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-2xl blur-xl animate-pulse-slow"></div>
                                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                                        <img src="{{ asset('images/hospital.jpg') }}" alt="Clínica Ricardo Palma" class="w-full h-auto object-cover">
                                    </div>
                                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full opacity-20 animate-float"></div>
                                </div>
                            </div>
                            <div class="md:w-1/2">
                                <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-6">
                                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-600">Nuestra Historia</span>
                                </h2>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    Fundada en 1990, la Clínica Ricardo Palma nació con la visión de transformar la atención médica en nuestra comunidad. Lo que comenzó como un pequeño consultorio con un equipo de cinco médicos dedicados, ha crecido hasta convertirse en una de las instituciones médicas más respetadas de la región.
                                </p>
                                <p class="text-gray-600 mb-6 leading-relaxed">
                                    A lo largo de más de tres décadas, hemos evolucionado constantemente, incorporando tecnología de vanguardia y ampliando nuestras instalaciones para ofrecer un servicio integral que responda a las necesidades de salud de nuestros pacientes.
                                </p>
                                <div class="flex items-center space-x-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>+100 Especialistas</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-hospital text-cyan-500 mr-2"></i>
                                        <span>30+ Años de Servicio</span>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-cyan-500 mr-2"></i>
                                        <span>+50,000 Pacientes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Misión y Visión Section -->
                <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl mb-20 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-blue-600"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-full -mb-32 -mr-32"></div>
                    
                    <div class="max-w-7xl mx-auto px-6">
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-600">Misión y Visión</span>
                            </h2>
                            <p class="text-gray-600 max-w-3xl mx-auto">Los valores que guían nuestro trabajo diario y nuestra visión para el futuro de la atención médica.</p>
                        </div>
                        
                        <div class="flex flex-col md:flex-row gap-10 items-center">
                            <div class="md:w-2/5">
                                <img src="{{ asset('images/mission-vision.svg') }}" alt="Misión y Visión" class="w-full h-auto animate-float">
                            </div>
                            
                            <div class="md:w-3/5 space-y-10">
                                <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl group-hover:scale-110 transition-transform duration-500"></div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                        <span class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center text-white mr-3">
                                            <i class="fas fa-bullseye"></i>
                                        </span>
                                        Nuestra Misión
                                    </h3>
                                    <p class="text-gray-600 leading-relaxed relative z-10">
                                        Proporcionar atención médica integral de la más alta calidad, centrada en el paciente, combinando la excelencia clínica con un trato humano y compasivo. Nos comprometemos a mejorar la salud y el bienestar de nuestra comunidad a través de servicios médicos accesibles, innovadores y personalizados.
                                    </p>
                                </div>
                                
                                <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl group-hover:scale-110 transition-transform duration-500"></div>
                                    <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                                        <span class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center text-white mr-3">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                        Nuestra Visión
                                    </h3>
                                    <p class="text-gray-600 leading-relaxed relative z-10">
                                        Ser reconocidos como la institución médica líder en excelencia clínica e innovación, estableciendo nuevos estándares en la atención médica personalizada. Aspiramos a expandir nuestro impacto positivo en la salud pública, formando profesionales de élite y contribuyendo al avance de la medicina a través de la investigación y la implementación de tecnologías de vanguardia.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Especialidades Section -->
                <section id="especialidades" class="mb-20 scroll-mt-24">
                    <div class="max-w-7xl mx-auto">
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-600">Nuestras Especialidades</span>
                            </h2>
                            <p class="text-gray-600 max-w-3xl mx-auto">Contamos con un amplio rango de especialidades médicas para brindar atención integral a todos nuestros pacientes.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                            <!-- Cardiología -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-heartbeat text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Cardiología</h3>
                                <p class="text-gray-600">Diagnóstico y tratamiento de enfermedades cardiovasculares con tecnología de última generación y especialistas de renombre.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dr. Carlos Mendoza - Director</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Neurología -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-brain text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Neurología</h3>
                                <p class="text-gray-600">Evaluación, diagnóstico y tratamiento de trastornos del sistema nervioso central y periférico con enfoque multidisciplinario.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dra. Laura Sánchez - Directora</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Pediatría -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-child text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Pediatría</h3>
                                <p class="text-gray-600">Cuidado integral de la salud de niños y adolescentes, desde recién nacidos hasta los 18 años, con un enfoque preventivo y terapéutico.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dr. Miguel Ángel Torres - Director</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Traumatología -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-bone text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Traumatología</h3>
                                <p class="text-gray-600">Especialistas en el diagnóstico, tratamiento, rehabilitación y prevención de lesiones del sistema musculoesquelético.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dr. Roberto Guzmán - Director</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Ginecología -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-venus text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Ginecología</h3>
                                <p class="text-gray-600">Atención especializada en salud femenina, abarcando desde la adolescencia hasta la etapa post-menopáusica con enfoque preventivo.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dra. Patricia Vega - Directora</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Oftalmología -->
                            <div class="bg-white p-6 rounded-2xl shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 hover:border-blue-100 group">
                                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-eye text-2xl text-cyan-500"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800 mb-3">Oftalmología</h3>
                                <p class="text-gray-600">Diagnóstico y tratamiento de enfermedades oculares con tecnología de vanguardia para preservar y mejorar la salud visual.</p>
                                <div class="mt-6 pt-6 border-t border-gray-100">
                                    <div class="flex items-center text-sm text-gray-500">
                                        <i class="fas fa-user-md text-cyan-500 mr-2"></i>
                                        <span>Dr. Javier Morales - Director</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-12 text-center">
                            <a href="#" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-cyan-500 to-blue-600 text-white rounded-full font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:from-cyan-600 hover:to-blue-700 hover:-translate-y-1">
                                <i class="fas fa-list-ul mr-2"></i>
                                Ver todas las especialidades
                            </a>
                        </div>
                    </div>
                </section>

                <!-- Equipo Médico Section -->
                <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl mb-20 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-blue-600"></div>
                    
                    <div class="max-w-7xl mx-auto px-6">
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-600">Nuestro Equipo Médico</span>
                            </h2>
                            <p class="text-gray-600 max-w-3xl mx-auto">Profesionales altamente calificados comprometidos con brindar la mejor atención médica.</p>
                        </div>
                        
                        <div class="flex flex-col md:flex-row gap-10 items-center">
                            <div class="md:w-2/5">
                                <img src="{{ asset('images/doctor-team.svg') }}" alt="Equipo Médico" class="w-full h-auto animate-float">
                            </div>
                            
                            <div class="md:w-3/5">
                                <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl"></div>
                                    
                                    <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                                        <span class="w-10 h-10 rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 flex items-center justify-center text-white mr-3">
                                            <i class="fas fa-user-md"></i>
                                        </span>
                                        Excelencia Profesional
                                    </h3>
                                    
                                    <p class="text-gray-600 leading-relaxed mb-6 relative z-10">
                                        Nuestro equipo está formado por más de 100 médicos especialistas con amplia experiencia clínica y formación académica en las instituciones más prestigiosas del país y el extranjero. Muchos de nuestros profesionales son referentes en sus campos y participan activamente en investigación y docencia.
                                    </p>
                                    
                                    <p class="text-gray-600 leading-relaxed mb-8 relative z-10">
                                        Todos nuestros médicos comparten el compromiso de ofrecer una atención personalizada y humana, poniendo al paciente en el centro de todas nuestras decisiones y procesos.
                                    </p>
                                    
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mr-3">
                                                <i class="fas fa-graduation-cap text-cyan-500"></i>
                                            </div>
                                            <span class="text-gray-600 text-sm">Formación continua</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mr-3">
                                                <i class="fas fa-certificate text-cyan-500"></i>
                                            </div>
                                            <span class="text-gray-600 text-sm">Certificaciones internacionales</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mr-3">
                                                <i class="fas fa-flask text-cyan-500"></i>
                                            </div>
                                            <span class="text-gray-600 text-sm">Investigación activa</span>
                                        </div>
                                        <div class="flex items-center">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center mr-3">
                                                <i class="fas fa-hands-helping text-cyan-500"></i>
                                            </div>
                                            <span class="text-gray-600 text-sm">Enfoque humanizado</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="md:w-1/2 order-1 md:order-2">
                                <div class="relative">
                                    <div class="absolute -inset-1 bg-gradient-to-r from-cyan-500/20 to-blue-500/20 rounded-2xl blur-xl animate-pulse-slow"></div>
                                    <div class="relative rounded-2xl overflow-hidden shadow-2xl">
                                        <img src="{{ asset('images/medical-tech.svg') }}" alt="Tecnología Médica Avanzada" class="w-full h-auto object-cover animate-float">
                                    </div>
                                    <div class="absolute -bottom-6 -right-6 w-24 h-24 bg-gradient-to-br from-cyan-400 to-blue-500 rounded-full opacity-20 animate-float"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Testimonios Section -->
                <section class="py-16 bg-gradient-to-br from-gray-50 to-blue-50 rounded-3xl mb-20 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-cyan-400 to-blue-600"></div>
                    <div class="absolute bottom-0 right-0 w-64 h-64 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-full -mb-32 -mr-32"></div>
                    
                    <div class="max-w-7xl mx-auto px-6">
                        <div class="text-center mb-16">
                            <h2 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">
                                <span class="bg-clip-text text-transparent bg-gradient-to-r from-cyan-500 to-blue-600">Lo Que Dicen Nuestros Pacientes</span>
                            </h2>
                            <p class="text-gray-600 max-w-3xl mx-auto">Testimonios de quienes han confiado en nosotros para el cuidado de su salud.</p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                            <!-- Testimonio 1 -->
                            <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl group-hover:scale-110 transition-transform duration-500"></div>
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center">
                                            <i class="fas fa-user text-xl text-cyan-500"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-800">María González</h4>
                                        <p class="text-sm text-gray-500">Paciente de Cardiología</p>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="flex text-amber-400 mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="text-gray-600 italic relative z-10">
                                    "La atención que recibí en la Clínica Ricardo Palma superó todas mis expectativas. El equipo médico no solo demostró un alto nivel de profesionalismo, sino también una calidez humana que me hizo sentir segura y acompañada durante todo mi tratamiento."</p>
                            </div>
                            
                            <!-- Testimonio 2 -->
                            <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl group-hover:scale-110 transition-transform duration-500"></div>
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center">
                                            <i class="fas fa-user text-xl text-cyan-500"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-800">Carlos Mendoza</h4>
                                        <p class="text-sm text-gray-500">Paciente de Traumatología</p>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="flex text-amber-400 mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                                <p class="text-gray-600 italic relative z-10">
                                    "Después de mi cirugía de rodilla, el equipo de rehabilitación fue fundamental para mi recuperación. Su dedicación y la tecnología de vanguardia que utilizan marcaron la diferencia. Hoy puedo volver a practicar deportes gracias a ellos."</p>
                            </div>
                            
                            <!-- Testimonio 3 -->
                            <div class="bg-white p-8 rounded-2xl shadow-xl border border-blue-100 relative overflow-hidden group hover:shadow-2xl transition-all duration-500">
                                <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-cyan-400/10 to-blue-500/10 rounded-bl-3xl group-hover:scale-110 transition-transform duration-500"></div>
                                <div class="flex items-center mb-6">
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 rounded-full bg-gradient-to-br from-cyan-400/20 to-blue-500/20 flex items-center justify-center">
                                            <i class="fas fa-user text-xl text-cyan-500"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <h4 class="text-lg font-semibold text-gray-800">Ana Martínez</h4>
                                        <p class="text-sm text-gray-500">Paciente de Ginecología</p>
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <div class="flex text-amber-400 mb-2">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star-half-alt"></i>
                                    </div>
                                </div>
                                <p class="text-gray-600 italic relative z-10">
                                    "Agradezco enormemente el trato humano y profesional que recibí durante mi embarazo. Los controles prenatales con tecnología de última generación y el acompañamiento constante del equipo médico me dieron la tranquilidad que necesitaba en ese momento tan especial."</p>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>