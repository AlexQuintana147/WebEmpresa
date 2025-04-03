# Añadir un bloque try-except global para capturar cualquier error al inicio del script
try:
    from selenium import webdriver
    from selenium.webdriver.common.by import By
    from selenium.webdriver.chrome.service import Service
    from selenium.webdriver.chrome.options import Options
    from selenium.webdriver.common.keys import Keys
    from selenium.webdriver.support.wait import WebDriverWait
    from selenium.webdriver.support import expected_conditions as EC
    import time
    import os
    import sys
    import platform
    import traceback
    import datetime

    # Crear un archivo de registro detallado para verificar la ejecución
    log_dir = os.path.dirname(os.path.abspath(__file__))
    log_file = os.path.join(log_dir, 'python_execution_detailed.log')

    with open(log_file, 'a') as f:
        timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        f.write(f"\n[{timestamp}] ===== INICIO DE EJECUCIÓN =====\n")
        f.write(f"[{timestamp}] Sistema: {platform.system()} {platform.release()}\n")
        f.write(f"[{timestamp}] Ruta: {os.getcwd()}\n")
        f.write(f"[{timestamp}] Argumentos: {sys.argv}\n")

    # Configurar las opciones de Chrome
    chrome_options = Options()
    # El modo headless puede causar problemas con interacciones en chatbots
    chrome_options.add_argument("--headless")  # Comentado para evitar problemas
    chrome_options.add_argument("--no-sandbox")
    chrome_options.add_argument("--disable-dev-shm-usage")
    chrome_options.add_argument("--disable-gpu")
    chrome_options.add_argument("--window-size=1920,1080")
    chrome_options.add_argument("--log-level=3")  # Minimizar logs

    # Tiempo máximo de espera para elementos (en segundos)
    WAIT_TIMEOUT = 10  # Reducido para mayor velocidad
    MAX_RETRY_ATTEMPTS = 3

    # Tiempos de espera dinámicos (en segundos)
    INITIAL_PAGE_LOAD_WAIT = 8  # Reducido para cargar más rápido
    RESPONSE_CHECK_INTERVAL = 0.3  # Intervalo más corto para verificar respuestas más frecuentemente

    # URL del chatbot - El botId actual corresponde a un chatbot médico
    CHATBOT_URL = "https://cdn.botpress.cloud/webchat/v2/shareable.html?botId=c9972624-70b3-42d9-8163-5cff1a30bd62"

    # Obtener la pregunta desde los argumentos de línea de comandos
    if len(sys.argv) > 1:
        QUESTION = sys.argv[1]
        print(f"Script iniciado con la pregunta: {QUESTION}")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Pregunta: {QUESTION}\n")
    else:
        QUESTION = "me duele la cabeza, que puedo tomar?"  # Pregunta por defecto
        print("Script iniciado con la pregunta por defecto")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Usando pregunta por defecto: {QUESTION}\n")

    # Imprimir información de diagnóstico
    print(f"Sistema operativo: {platform.system()} {platform.release()}")
    print(f"Ruta de ejecución: {os.getcwd()}")
    print(f"Argumentos recibidos: {sys.argv}")

    # Añadir registro de ejecución al inicio del script para verificar si se está ejecutando
    log_dir = os.path.dirname(os.path.abspath(__file__))
    log_file = os.path.join(log_dir, 'python_execution.log')

    with open(log_file, 'a') as f:
        timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
        f.write(f"[{timestamp}] Script ejecutado con argumentos: {sys.argv}\n")

    def wait_for_element(driver, by, selector, timeout=WAIT_TIMEOUT):
        """Espera a que un elemento esté presente y visible en la página"""
        try:
            element = WebDriverWait(driver, timeout).until(
                EC.visibility_of_element_located((by, selector))
            )
            return element
        except Exception as e:
            print(f"Error esperando elemento {selector}: {e}")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Error esperando elemento {selector}: {e}\n")
            return None

    def wait_for_chatbot_ready(driver, timeout=WAIT_TIMEOUT):
        """Espera a que el chatbot esté listo para recibir mensajes de manera optimizada"""
        try:
            # Usar una espera más corta pero con polling más frecuente
            wait = WebDriverWait(driver, timeout, poll_frequency=0.2)
            textarea = wait.until(
                EC.element_to_be_clickable((By.CSS_SELECTOR, "textarea.bpComposerInput"))
            )
            print("Chatbot listo para recibir mensajes")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Chatbot listo para recibir mensajes\n")
            return textarea
        except Exception as e:
            print(f"Error esperando a que el chatbot esté listo: {e}")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Error esperando a que el chatbot esté listo: {e}\n")
            return None
            
    def is_response_complete(driver, last_message_text):
        """Verifica si la respuesta del chatbot está completa de manera optimizada"""
        try:
            # Verificar primero si hay nuevos mensajes (más eficiente)
            responses = driver.find_elements(By.CSS_SELECTOR, "div.bpMessageBlocksBubble")
            if not responses:
                return False
                
            # Obtener el último mensaje
            current_last_message = responses[-1].text
            
            # Si el mensaje no ha cambiado o es igual a la pregunta, no está completo
            if current_last_message == last_message_text or current_last_message.strip().lower() == QUESTION.strip().lower():
                return False
                
            # Verificar si el bot sigue escribiendo (indicador de typing)
            typing_indicators = driver.find_elements(By.CSS_SELECTOR, "div.bpTyping")
            if typing_indicators and len(typing_indicators) > 0:
                return False
                
            # Si el mensaje ha cambiado y no hay indicadores de escritura, consideramos que está completo
            return True
        except Exception as e:
            print(f"Error al verificar si la respuesta está completa: {e}")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Error al verificar si la respuesta está completa: {e}\n")
            return True  # Asumir que está completa en caso de error para no bloquear el proceso

    try:
        # Configurar el driver de Chrome
        print("Iniciando el navegador Chrome...")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Iniciando el navegador Chrome...\n")
        
        # Usar el driver de Chrome instalado en el sistema
        driver = webdriver.Chrome(options=chrome_options)
        
        # Configurar un tiempo de espera implícito más corto
        driver.implicitly_wait(5)

        # Abrir la página del chatbot
        print("Abriendo la página del chatbot...")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Abriendo la página del chatbot...\n")
        driver.get(CHATBOT_URL)

        # Esperar a que la página cargue inicialmente
        print("Esperando a que la página cargue inicialmente...")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Esperando a que la página cargue inicialmente...\n")
        time.sleep(INITIAL_PAGE_LOAD_WAIT)  # Tiempo inicial de espera reducido
        
        # Esperar a que el chatbot esté listo para recibir mensajes
        print("Esperando a que el chatbot esté listo...")
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Esperando a que el chatbot esté listo...\n")
        textarea = wait_for_chatbot_ready(driver)
        
        if not textarea:
            print("No se pudo encontrar el campo de texto para enviar el mensaje.")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] No se pudo encontrar el campo de texto para enviar el mensaje.\n")
            raise Exception("No se encontró el campo de texto del chatbot")
        
        # Enviar el mensaje y esperar respuesta de forma optimizada
        try:
            # Limpiar el textarea por si acaso
            textarea.clear()
            
            # Escribir y enviar el mensaje rápidamente
            textarea.send_keys(QUESTION)
            textarea.send_keys(Keys.RETURN)
            print("Mensaje enviado.") 
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Mensaje enviado: {QUESTION}\n")
            
            # Obtener el estado inicial de los mensajes
            initial_responses = driver.find_elements(By.CSS_SELECTOR, "div.bpMessageBlocksBubble")
            initial_last_message = initial_responses[-1].text if initial_responses else ""
            
            # Esperar a que aparezca una respuesta con verificación dinámica ultra-optimizada
            print("Esperando respuesta del chatbot...")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Esperando respuesta del chatbot...\n")
            wait_start_time = time.time()
            response_received = False
            last_check_message = initial_last_message
            consecutive_same_message = 0
            max_consecutive_checks = 2  # Reducido para detectar respuestas más rápido
            last_length_change_time = time.time()
            
            while (time.time() - wait_start_time) < WAIT_TIMEOUT and not response_received:
                # Verificar si hay respuesta nueva (usando find_elements para evitar excepciones)
                responses = driver.find_elements(By.CSS_SELECTOR, "div.bpMessageBlocksBubble")
                
                if responses:
                    # Obtener la última respuesta
                    last_response = responses[-1]
                    full_response_text = last_response.text
                    
                    # Verificar si la respuesta es nueva y diferente a la pregunta
                    if (full_response_text != last_check_message and 
                        full_response_text.strip().lower() != QUESTION.strip().lower()):
                        
                        # Actualizar el tiempo de último cambio si el contenido cambió
                        if full_response_text != last_check_message:
                            last_length_change_time = time.time()
                        
                        # Verificar si la respuesta está completa por alguno de estos métodos:
                        # 1. Método tradicional (is_response_complete)
                        # 2. Estabilidad del mensaje (mismo mensaje varias veces)
                        # 3. No ha cambiado en los últimos 1.5 segundos
                        if (is_response_complete(driver, full_response_text) or
                            (full_response_text == last_check_message and consecutive_same_message >= max_consecutive_checks) or
                            (time.time() - last_length_change_time > 1.5 and len(full_response_text) > 20)):
                            
                            print("\nRespuesta del chatbot:")
                            print("-" * 50)
                            print(full_response_text)
                            print("-" * 50)
                            with open(log_file, 'a') as f:
                                f.write(f"[{timestamp}] Respuesta recibida: {full_response_text}\n")
                            response_received = True
                            break
                        else:
                            # Actualizar el contador de mensajes consecutivos iguales
                            if full_response_text == last_check_message:
                                consecutive_same_message += 1
                            else:
                                consecutive_same_message = 0
                                last_check_message = full_response_text
                
                # Esperar un breve intervalo antes de verificar nuevamente (reducido para mayor velocidad)
                time.sleep(RESPONSE_CHECK_INTERVAL)
            
            if not response_received:
                # Intentar obtener la última respuesta disponible de forma optimizada
                final_responses = driver.find_elements(By.CSS_SELECTOR, "div.bpMessageBlocksBubble")
                if final_responses and final_responses[-1].text.strip().lower() != QUESTION.strip().lower():
                    print("\nÚltima respuesta disponible del chatbot:")
                    print("-" * 50)
                    print(final_responses[-1].text)
                    print("-" * 50)
                    with open(log_file, 'a') as f:
                        f.write(f"[{timestamp}] Última respuesta disponible: {final_responses[-1].text}\n")
                    # Marcar como recibida para evitar mensajes de error innecesarios
                    response_received = True
                else:
                    print("\nRespuesta del chatbot:")
                    print("-" * 50)
                    print("Lo siento, no pude procesar tu consulta correctamente. Por favor, intenta de nuevo.")
                    print("-" * 50)
                    with open(log_file, 'a') as f:
                        f.write(f"[{timestamp}] No se pudo obtener respuesta\n")
                    
        except Exception as e:
            print(f"Error al enviar mensaje o recibir respuesta: {e}")
            traceback.print_exc()
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Error al enviar mensaje o recibir respuesta: {e}\n")
                f.write(f"[{timestamp}] {traceback.format_exc()}\n")
            # Asegurar que siempre se genere una respuesta con el formato esperado
            print("\nRespuesta del chatbot:")
            print("-" * 50)
            print("Lo siento, ocurrió un error al procesar tu consulta. Por favor, intenta de nuevo.")
            print("-" * 50)
        
        if not response_received:
            # Asegurar que siempre se genere una respuesta con el formato esperado
            print("\nRespuesta del chatbot:")
            print("-" * 50)
            print("No se pudo obtener una respuesta válida después de varios intentos. Por favor, intenta de nuevo.")
            print("-" * 50)
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] No se pudo obtener una respuesta válida después de varios intentos\n")

    except Exception as e:
        print(f"Error al interactuar con el chatbot: {e}")
        import traceback
        print(traceback.format_exc())  # Imprime el stack trace completo
        with open(log_file, 'a') as f:
            f.write(f"[{timestamp}] Error al interactuar con el chatbot: {e}\n")
            f.write(f"[{timestamp}] {traceback.format_exc()}\n")
        # Asegurar que siempre se genere una respuesta con el formato esperado
        print("\nRespuesta del chatbot:")
        print("-" * 50)
        print("Lo siento, ocurrió un error al interactuar con el chatbot. Por favor, intenta de nuevo.")
        print("-" * 50)
    finally:
        # Cerrar el navegador si está definido
        try:
            if 'driver' in locals() and driver is not None:
                print("Cerrando el navegador...")
                with open(log_file, 'a') as f:
                    f.write(f"[{timestamp}] Cerrando el navegador...\n")
                driver.quit()
        except Exception as e:
            print(f"Error al cerrar el navegador: {e}")
            with open(log_file, 'a') as f:
                f.write(f"[{timestamp}] Error al cerrar el navegador: {e}\n")

    print("Script finalizado.")
    with open(log_file, 'a') as f:
        f.write(f"[{timestamp}] ===== SCRIPT FINALIZADO =====\n")

except Exception as e:
    # Capturar cualquier error que ocurra al inicio del script
    error_message = f"ERROR CRÍTICO AL INICIAR EL SCRIPT: {str(e)}"
    print(error_message)
    
    # Intentar escribir en un archivo de error
    try:
        error_log_path = os.path.join(os.path.dirname(os.path.abspath(__file__)), 'python_critical_error.log')
        with open(error_log_path, 'a') as f:
            timestamp = datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')
            f.write(f"[{timestamp}] {error_message}\n")
            f.write(f"[{timestamp}] {traceback.format_exc()}\n")
    except:
        pass
    
    # Asegurar que siempre se genere una respuesta con el formato esperado
    print("\nRespuesta del chatbot:")
    print("-" * 50)
    print("Error crítico al iniciar el script. Por favor, contacte al administrador del sistema.")
    print("-" * 50)
