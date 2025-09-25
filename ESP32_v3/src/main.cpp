#include <Arduino.h>
#include <WiFi.h>
#include "TelegramBot.h"
#include "DHT.h"

// =============================
// Configuración WiFi
// =============================
const char* ssid     = "Jose";      // SSID de la red
const char* password = "Viani1992"; // Contraseña de la red

// =============================
// Servidor PHP/MySQL
// =============================
int ID_TARJ = 1;                    // IDENTIFICADOR DE LA TARJETA (id de colmena en la BD)
const char* host = "192.168.80.27"; // IP del host del servidor (local o remoto)
const int   port = 80;              // Puerto del servidor

// =============================
// Telegram
// =============================
String telegramBotToken = "";     // Se actualizará desde la BD
String telegramChatId   = "";     // Se actualizará desde la BD
TelegramBot bot(telegramBotToken, telegramChatId);

// =============================
// Sensor DHT11
// =============================
#define DHTPIN 4
#define DHTTYPE DHT11
DHT dht(DHTPIN, DHTTYPE);

// =============================
// Sensores IR
// =============================
#define IR1_PIN 5
#define IR2_PIN 18

volatile bool ir1Triggered = false;
volatile bool ir2Triggered = false;
volatile unsigned long lastIr1Time = 0;
volatile unsigned long lastIr2Time = 0;

volatile int dir1Count = 0; // IR1 → IR2
volatile int dir2Count = 0; // IR2 → IR1

// =============================
// LEDs
// =============================
#define LED_TEMP 2
#define LED_HUM  15
#define LED_FREQ 21

// =============================
// Control de tiempos
// =============================
unsigned long lastCheckIR = 0;
const unsigned long checkIntervalIR = 60000; // 60s

unsigned long lastDhtRead = 0;
const unsigned long dhtInterval = 2000; // 2s

unsigned long lastDbSync = 0;
const unsigned long dbInterval = 10000; // cada 10s guardar datos

// =============================
// Umbrales (se actualizarán desde la BD)
// =============================
float tempMin = 15.0, tempMax = 30.0; // °C
float humMin = 30.0, humMax = 70.0;   // %
int freqMin = 5, freqMax = 20;        // eventos/minuto

// =============================
// ISR: detección de pulsos
// =============================
void IRAM_ATTR handleIR1() {
  unsigned long now = millis();
  if (now - lastIr1Time > 50) {
    ir1Triggered = true;
    lastIr1Time = now;
  }
}

void IRAM_ATTR handleIR2() {
  unsigned long now = millis();
  if (now - lastIr2Time > 50) {
    ir2Triggered = true;
    lastIr2Time = now;
  }
}

// =============================
// Lógica de conteo de direcciones
// =============================
void processDirection() {
  if (ir1Triggered && ir2Triggered && lastIr1Time < lastIr2Time) {
    dir1Count++;
    ir1Triggered = false;
    ir2Triggered = false;
  }
  else if (ir1Triggered && ir2Triggered && lastIr2Time < lastIr1Time) {
    dir2Count++;
    ir1Triggered = false;
    ir2Triggered = false;
  }
}

// =============================
// Control de alertas agrupadas
// =============================
unsigned long lastAlertTime = 0;
const unsigned long alertInterval = 60000; // 1 minuto
String pendingAlerts = ""; // Mensajes acumulados

// =============================
// Lectura periódica del DHT11 con alertas agrupadas
// =============================
float lastTemp = 0, lastHum = 0;

void readDHT() {
  float temp = dht.readTemperature();
  float hum = dht.readHumidity();

  if (isnan(temp) || isnan(hum)) {
    Serial.println(" Error al leer del DHT11");
    return;
  }

  lastTemp = temp;
  lastHum = hum;

  Serial.printf("Temp: %.1f °C | Hum: %.1f %%\n", temp, hum);

  // --- ALERTAS agrupadas ---
  if (temp < tempMin) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Temperatura BAJA (" + String(temp) + " °C)\n";
  else if (temp > tempMax) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Temperatura ALTA (" + String(temp) + " °C)\n";

  if (hum < humMin) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Humedad BAJA (" + String(hum) + " %)\n";
  else if (hum > humMax) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Humedad ALTA (" + String(hum) + " %)\n";

  // --- CONTROL DE LEDS ---
  digitalWrite(LED_TEMP, (temp < tempMin || temp > tempMax) ? HIGH : LOW);
  digitalWrite(LED_HUM,  (hum < humMin  || hum > humMax)   ? HIGH : LOW);
}

// =============================
// Monitoreo de frecuencia IR con alertas agrupadas
// =============================
int lastFreqDir1 = 0, lastFreqDir2 = 0;

void checkIR() {
  processDirection();

  if (millis() - lastCheckIR >= checkIntervalIR) {
    lastCheckIR = millis();

    int freqDir1 = dir1Count;
    int freqDir2 = dir2Count;
    dir1Count = 0;
    dir2Count = 0;

    lastFreqDir1 = freqDir1;
    lastFreqDir2 = freqDir2;

    Serial.println("Dir1: " + String(freqDir1) + " eventos/min");
    Serial.println("Dir2: " + String(freqDir2) + " eventos/min");

    // --- ALERTAS agrupadas ---
    if (freqDir1 < freqMin) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Frecuencia BAJA Dir1 (" + String(freqDir1) + ")\n";
    else if (freqDir1 > freqMax) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Frecuencia ALTA Dir1 (" + String(freqDir1) + ")\n";

    if (freqDir2 < freqMin) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Frecuencia BAJA Dir2 (" + String(freqDir2) + ")\n";
    else if (freqDir2 > freqMax) pendingAlerts += "⚠ Colmena " + String(ID_TARJ) + ": Frecuencia ALTA Dir2 (" + String(freqDir2) + ")\n";

    // --- CONTROL DE LED ---
    if (freqDir1 < freqMin || freqDir1 > freqMax ||
        freqDir2 < freqMin || freqDir2 > freqMax) {
      digitalWrite(LED_FREQ, HIGH);
    } else {
      digitalWrite(LED_FREQ, LOW);
    }
  }
}

// =============================
// Envío periódico de alertas agrupadas
// =============================
void sendGroupedAlerts() {
  if (millis() - lastAlertTime >= alertInterval) {
    lastAlertTime = millis();

    if (pendingAlerts.length() > 0) {
      bot.sendMessage(pendingAlerts);
      Serial.println("🔔 Alertas enviadas:\n" + pendingAlerts);
      pendingAlerts = ""; // Resetear acumulador
    }
  }
}

// =============================
// Función auxiliar: leer solo el body HTTP (sin headers)
// =============================
String getHttpResponse(WiFiClient &client) {
  String line;
  String body = "";
  bool headersEnded = false;

  while (client.connected() || client.available()) {
    line = client.readStringUntil('\n');

    if (!headersEnded) {
      if (line == "\r" || line.length() == 0) {
        headersEnded = true;
      }
    } else {
      body += line;
    }
  }

  body.trim();
  return body;
}

// =============================
// Función: obtener umbrales desde la BD
// =============================
void updateThresholdsFromDB() {
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("Error conectando a servidor (umbrales)");
    return;
  }

  String url = "/programas_php/proceso_eventos/actualizar_umbrales.php?colmena_id=" + String(ID_TARJ);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  String payload = getHttpResponse(client);

  int idx1 = payload.indexOf(',');
  int idx2 = payload.indexOf(',', idx1+1);
  int idx3 = payload.indexOf(',', idx2+1);
  int idx4 = payload.indexOf(',', idx3+1);
  int idx5 = payload.indexOf(',', idx4+1);

  if (idx1 > 0 && idx2 > 0 && idx3 > 0 && idx4 > 0 && idx5 > 0) {
    tempMin = payload.substring(0, idx1).toFloat();
    tempMax = payload.substring(idx1+1, idx2).toFloat();
    humMin  = payload.substring(idx2+1, idx3).toFloat();
    humMax  = payload.substring(idx3+1, idx4).toFloat();
    freqMin = payload.substring(idx4+1, idx5).toInt();
    freqMax = payload.substring(idx5+1).toInt();

    Serial.printf("Nuevos umbrales - Temp: %.1f-%.1f °C, Hum: %.1f-%.1f %%, Freq: %d-%d eventos/min\n",
                  tempMin, tempMax, humMin, humMax, freqMin, freqMax);
  } else {
    Serial.println("⚠ Error: formato de datos recibido no es válido.");
    Serial.println("Respuesta recibida: " + payload);
  }

  client.stop();
}


// =============================
// Función: guardar datos en la BD
// =============================
void saveDataToDB() {
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("Error conectando a servidor (guardar)");
    return;
  }

  String url = "/programas_php/proceso_eventos/guardar_datos_sensores.php?temperatura=" + String(lastTemp) +
               "&humedad=" + String(lastHum) +
               "&actividad_in=" + String(lastFreqDir1) +
               "&actividad_out=" + String(lastFreqDir2) +
               "&ID_TARJ=" + String(ID_TARJ);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  Serial.println(String("Datos enviados a BD: ") + 
                "Temp=" + String(lastTemp) +
                ", Hum=" + String(lastHum) +
                ", Actividad Entrante=" + String(lastFreqDir1) +
                ", Actividad Saliente=" + String(lastFreqDir2));
  client.stop();
}

// ===========================================================
// Función para obtener credenciales de telegram desde la BD
// ===========================================================
void updateTelegramCredentials() {
  WiFiClient client;
  if (!client.connect(host, port)) {
    Serial.println("Error conectando a servidor (Telegram)");
    return;
  }

  // Aquí ajusta la URL al script PHP que hicimos antes
  String url = "/programas_php/proceso_eventos/obtener_credenciales_tg.php?colmena_id=1"; 
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  String payload = getHttpResponse(client);

  // Buscar la coma separadora
  int idx = payload.indexOf(',');

  if (idx > 0) {
    String chatId  = payload.substring(0, idx);
    String botToken = payload.substring(idx + 1);

    chatId.trim();
    botToken.trim();

    Serial.println("Credenciales recibidas:");
    Serial.println("Chat ID: " + chatId);
    Serial.println("Bot Token: " + botToken);

    // Actualizar el bot con las nuevas credenciales
    bot.setChatId(chatId);
    bot.setBotToken(botToken);
  } else {
    Serial.println("⚠ Error: formato de datos recibido no es válido.");
    Serial.println("Payload: " + payload);
  }

  client.stop();
}

// =============================
// Setup
// =============================
void setup() {
  Serial.begin(115200);

  WiFi.begin(ssid, password);
  Serial.print("Conectando a WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nWiFi conectado");
  Serial.print("IP: "); Serial.println(WiFi.localIP());

  dht.begin();
  pinMode(IR1_PIN, INPUT_PULLUP);
  pinMode(IR2_PIN, INPUT_PULLUP);
  attachInterrupt(digitalPinToInterrupt(IR1_PIN), handleIR1, FALLING);
  attachInterrupt(digitalPinToInterrupt(IR2_PIN), handleIR2, FALLING);

  // Configurar LEDs como salida
  pinMode(LED_TEMP, OUTPUT);
  pinMode(LED_HUM, OUTPUT);
  pinMode(LED_FREQ, OUTPUT);
  
  // Llamar a la función para obtener credenciales de Telegram
  updateTelegramCredentials();

  delay(1000); // Esperar un momento para asegurar que el bot esté listo

  bot.sendMessage("ESP32 conectado. Monitoreo iniciado.");

  updateThresholdsFromDB();
}

// =============================
// Loop principal
// =============================
void loop() {
  if (millis() - lastDhtRead >= dhtInterval) {
    lastDhtRead = millis();
    readDHT();
  }

  checkIR();
  sendGroupedAlerts(); //Ahora las alertas salen cada minuto agrupadas

  if (millis() - lastDbSync >= dbInterval) {
    lastDbSync = millis();
    saveDataToDB();
    updateThresholdsFromDB();
  }
}