import sys
import time
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.chrome.options import Options

# Recibe la descripción como argumento
if len(sys.argv) < 2:
    print('ERROR: No se recibió descripción')
    sys.exit(1)

descripcion = sys.argv[1]

chrome_options = Options()
chrome_options.add_argument('--headless')
chrome_options.add_argument('--no-sandbox')
chrome_options.add_argument('--disable-dev-shm-usage')
chrome_options.add_argument('--disable-gpu')
chrome_options.add_argument('--window-size=1920,1080')

try:
    driver = webdriver.Chrome(options=chrome_options)
except Exception as e:
    print(f'ERROR: No se pudo iniciar ChromeDriver. Detalle: {str(e)}')
    sys.exit(2)

try:
    driver.get('https://cdn.botpress.cloud/webchat/v2.3/shareable.html?configUrl=https://files.bpcontent.cloud/2025/04/15/16/20250415164756-4SAS73EX.json')
    time.sleep(10)  # Espera más tiempo para que cargue el chat
    try:
        textarea = driver.find_element(By.CLASS_NAME, 'bpComposerInput')
    except Exception as e:
        print(f'ERROR: No se encontró el área de texto para enviar el mensaje. Detalle: {str(e)}')
        sys.exit(3)
    textarea.send_keys(descripcion)
    textarea.send_keys('\n')
    time.sleep(8)  # Espera más tiempo para que llegue la respuesta
    responses = driver.find_elements(By.CLASS_NAME, 'bpMessageBlocksTextText')
    print("Mensajes encontrados:")
    for i, resp in enumerate(responses):
        print(f"[{i}] {resp.text}")
    if not responses:
        print('ERROR: No se encontró ninguna respuesta del bot.')
        sys.exit(4)
    # Mostrar todos los mensajes del bot a partir del índice 2
    respuestas_finales = [resp.text.strip() for i, resp in enumerate(responses) if i >= 2 and resp.text.strip()]
    if respuestas_finales:
        print("Respuesta IA:", "\n".join(respuestas_finales))
    else:
        print('ERROR: No se encontró respuesta relevante del bot.')
except Exception as e:
    print(f'ERROR: {str(e)}')
finally:
    driver.quit()
