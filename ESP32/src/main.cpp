#include <Arduino.h>
#include <WiFi.h>
#include "TelegramBot.h"
#include "DHT.h"

// =============================
// Configuración WiFi
// =============================
const char* ssid     = "MiguelG";
const char* password = "luis1234567";

// =============================
// Servidor PHP/MySQL
// =============================
const char* host = "172.20.6.88";   // cambia por la IP/host del servidor
const int   port = 80;                 // Puerto (normalmente 80)
int ID_TARJ = 1;                       // Identificador de la tarjeta

// =============================
// Telegram
// =============================
String botToken = "8332275316:AAGDv3cssnrlU_lBHmOOKLhGmIDkY_UrhQw";
String chatId   = "5152788448";
TelegramBot bot(botToken, chatId);

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
#define IR2_PIN 18   // Segundo sensor IR

volatile bool ir1Triggered = false;
volatile bool ir2Triggered = false;
volatile unsigned long lastIr1Time = 0;
volatile unsigned long lastIr2Time = 0;

volatile int dir1Count = 0; // IR1 → IR2
volatile int dir2Count = 0; // IR2 → IR1

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
// Lectura periódica del DHT11
// =============================
float lastTemp = 0, lastHum = 0;

void readDHT() {
  float temp = dht.readTemperature();
  float hum = dht.readHumidity();

  if (isnan(temp) || isnan(hum)) {
    Serial.println("⚠️ Error al leer del DHT11");
    return;
  }

  lastTemp = temp;
  lastHum = hum;

  Serial.printf("🌡️ Temp: %.1f °C | 💧 Hum: %.1f %%\n", temp, hum);

  if (temp < tempMin) bot.sendMessage("⚠️ ALERTA: Temperatura BAJA (" + String(temp) + " °C)");
  else if (temp > tempMax) bot.sendMessage("⚠️ ALERTA: Temperatura ALTA (" + String(temp) + " °C)");

  if (hum < humMin) bot.sendMessage("⚠️ ALERTA: Humedad BAJA (" + String(hum) + " %)");
  else if (hum > humMax) bot.sendMessage("⚠️ ALERTA: Humedad ALTA (" + String(hum) + " %)");
}

// =============================
// Monitoreo de frecuencia IR
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

    if (freqDir1 < freqMin) bot.sendMessage("⚠️ ALERTA: Frecuencia BAJA Dir1 (" + String(freqDir1) + ")");
    else if (freqDir1 > freqMax) bot.sendMessage("⚠️ ALERTA: Frecuencia ALTA Dir1 (" + String(freqDir1) + ")");

    if (freqDir2 < freqMin) bot.sendMessage("⚠️ ALERTA: Frecuencia BAJA Dir2 (" + String(freqDir2) + ")");
    else if (freqDir2 > freqMax) bot.sendMessage("⚠️ ALERTA: Frecuencia ALTA Dir2 (" + String(freqDir2) + ")");
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
      // Una línea vacía marca el fin de los headers HTTP
      if (line == "\r" || line.length() == 0) {
        headersEnded = true;
      }
    } else {
      // Ya estamos en el body → acumular datos
      body += line;
    }
  }

  body.trim();  // eliminar espacios y saltos de línea extra
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

  String url = "/programas_php/proceso_eventos/programa5.php"; // PHP debe devolver solo CSV
  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  // Obtener solo el body limpio
  String payload = getHttpResponse(client);

  // Parsear CSV → esperado: tempMin,tempMax,humMin,humMax,freqMin,freqMax
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
    Serial.println("⚠️ Error: formato de datos recibido no es válido.");
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

  String url = "/programas_php/proceso_eventos/programa1.php?temperatura=" + String(lastTemp) +
               "&humedad=" + String(lastHum) +
               "&dir1=" + String(lastFreqDir1) +
               "&dir2=" + String(lastFreqDir2) +
               "&ID_TARJ=" + String(ID_TARJ);

  client.print(String("GET ") + url + " HTTP/1.1\r\n" +
               "Host: " + host + "\r\n" +
               "Connection: close\r\n\r\n");

  Serial.println(String("Datos enviados a BD: ") + 
                "Temp=" + String(lastTemp) +
                ", Hum=" + String(lastHum) +
                ", Dir1=" + String(lastFreqDir1) +
                ", Dir2=" + String(lastFreqDir2));
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

  bot.sendMessage("ESP32 conectado. Monitoreo iniciado.");

  updateThresholdsFromDB(); // lee umbrales al inicio
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

  if (millis() - lastDbSync >= dbInterval) {
    lastDbSync = millis();
    saveDataToDB();
    updateThresholdsFromDB(); // refresca umbrales periódicamente
  }
}
