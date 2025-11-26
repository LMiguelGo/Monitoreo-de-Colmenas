#include "ServerConnector.h"

// Constructor
ServerConnector::ServerConnector(const char* host, int port, int idTarjeta, TelegramBot* bot): 
    host(host), 
    port(port), 
    idTarjeta(idTarjeta), 
    bot(bot),
    tempMin(15.0), 
    tempMax(30.0), 
    humMin(30.0), 
    humMax(70.0), 
    freqMin(5), 
    freqMax(20) {}


// Connect to WiFi
void ServerConnector::connectWiFi(const char* ssid, const char* password) {
    WiFi.begin(ssid, password);
    Serial.printf("Conectando a WiFi (%s)...", ssid);
    while (WiFi.status() != WL_CONNECTED) {
        delay(500);
        Serial.print(".");
    }
    Serial.println("\nWiFi conectado");
    Serial.print("IP asignada: ");
    Serial.println(WiFi.localIP());
}

// Update thresholds from the database
void ServerConnector::updateThresholdsFromDB() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("Error en actualización de umbrales: No hay conexión WiFi.");
        return;
    }

    HTTPClient http;
    String url = "http://" + String(host) + ":" + String(port) +
                 "/bee_monitor/proceso_eventos/actualizar_umbrales.php?colmena_id=" + String(idTarjeta);

    http.begin(url);
    int httpCode = http.GET();

    if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        StaticJsonDocument<256> doc;
        DeserializationError error = deserializeJson(doc, payload);

        if (error) {
            Serial.println("Error al parsear JSON de umbrales:");
            Serial.println(error.c_str());
            Serial.println(payload);
        } else if (doc["status"] == "success") {
            tempMin = doc["temp_min"];
            tempMax = doc["temp_max"];
            humMin  = doc["hum_min"];
            humMax  = doc["hum_max"];
            freqMin = doc["activ_min"];
            freqMax = doc["activ_max"];

            Serial.println("Umbrales actualizados desde el servidor:");
            Serial.printf("   Temp: %.1f - %.1f °C\n", tempMin, tempMax);
            Serial.printf("   Hum: %.1f - %.1f %%\n", humMin, humMax);
            Serial.printf("   Act: %d - %d Hz\n", freqMin, freqMax);
        } else {
            Serial.println("Error en respuesta del servidor (sin status=success):");
            serializeJsonPretty(doc, Serial);
            Serial.println();
        }
    } else {
        Serial.printf("Error HTTP (%d) al obtener umbrales.\n", httpCode);
    }

    http.end();
}

// Save sensor data to the database
void ServerConnector::saveDataToDB(float temperatura, float humedad, int freqIn, int freqOut) {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("Error al guardar datos: No hay conexión WiFi.");
        return;
    }

    HTTPClient http;
    String url = "http://" + String(host) + ":" + String(port) +
                 "/bee_monitor/proceso_eventos/guardar_datos_sensores.php?temperatura=" + String(temperatura) +
                 "&humedad=" + String(humedad) +
                 "&actividad_in=" + String(freqIn) +
                 "&actividad_out=" + String(freqOut) +
                 "&ID_TARJ=" + String(idTarjeta);

    http.begin(url);
    int httpCode = http.GET();

    if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        StaticJsonDocument<128> doc;

        if (deserializeJson(doc, payload) == DeserializationError::Ok) {
            if (doc["status"] == "success") {
                Serial.println("Datos guardados correctamente en la BD:");
                Serial.println("   Temp: " + String(temperatura) + " °C");
                Serial.println("   Hum: " + String(humedad) + " %");
                Serial.println("   Act In: " + String(freqIn) + " Hz");
                Serial.println("   Act Out: " + String(freqOut) + " Hz");
            } else {
                Serial.println("Servidor respondió con error:");
                serializeJsonPretty(doc, Serial);
                Serial.println();
            }
        } else {
            Serial.println("Respuesta no JSON al guardar datos:");
            Serial.println(payload);
        }
    } else {
        Serial.printf("Error HTTP (%d) al guardar datos.\n", httpCode);
    }

    http.end();
}

// Update control and actuators from the database
void ServerConnector::updateControlAndActuatorsFromDB() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("Error en actualización de control: No hay conexión WiFi.");
        return;
    }

    HTTPClient http;
    String url = "http://" + String(host) + ":" + String(port) +
                 "/bee_monitor/proceso_eventos/obtener_control_colmena.php?colmena_id=" + String(idTarjeta);

    http.begin(url);
    int httpCode = http.GET();

    if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        StaticJsonDocument<512> doc;
        DeserializationError error = deserializeJson(doc, payload);

        if (error) {
            Serial.println("Error al parsear JSON de control:");
            Serial.println(error.c_str());
            Serial.println(payload);
        } else if (doc["status"] == "success") {
            String modo = doc["control"]["modo"].as<String>();
            automaticControl = (modo == "automatico");

            JsonArray acts = doc["actuadores"];
            for (JsonObject act : acts) {
                String nombre = act["nombre"].as<String>();
                float estado = act["estado"].as<float>();

                if (nombre == "compuertas") gateAngle = estado;
                else if (nombre == "calefactor") heater = (estado > 0);
            }

            Serial.println("Control actualizado desde el servidor:");
            Serial.printf("   Modo automático: %s\n", automaticControl ? "Sí" : "No");
            Serial.printf("   Ángulo compuertas: %.2f°\n", gateAngle);
            Serial.printf("   Calefactor: %s\n", heater ? "Encendido" : "Apagado");
        } else {
            Serial.println("Error en respuesta del servidor (sin status=success):");
            serializeJsonPretty(doc, Serial);
            Serial.println();
        }
    } else {
        Serial.printf("Error HTTP (%d) al obtener control.\n", httpCode);
    }

    http.end();
}

// Update Telegram credentials from the server
void ServerConnector::updateTelegramCredentials() {
    if (WiFi.status() != WL_CONNECTED) {
        Serial.println("Error al obtener credenciales Telegram: No hay conexión WiFi.");
        return;
    }

    HTTPClient http;
    String url = "http://" + String(host) + ":" + String(port) +
                 "/bee_monitor/proceso_eventos/obtener_credenciales_tg.php?colmena_id=" + String(idTarjeta);

    http.begin(url);
    int httpCode = http.GET();

    if (httpCode == HTTP_CODE_OK) {
        String payload = http.getString();
        StaticJsonDocument<256> doc;

        DeserializationError error = deserializeJson(doc, payload);
        if (error) {
            Serial.println("Error al parsear JSON de credenciales Telegram:");
            Serial.println(error.c_str());
            Serial.println(payload);
        } else if (doc["status"] == "success") {
            String chatId = doc["chat_id"].as<String>();
            String botToken = doc["bot_token"].as<String>();

            bot->setChatId(chatId);
            bot->setBotToken(botToken);

            Serial.println("Credenciales Telegram actualizadas:");
            Serial.println("   Chat ID: " + chatId);
            Serial.println("   Bot Token: " + botToken);
        } else {
            Serial.println("Error en la respuesta del servidor al obtener credenciales:");
            serializeJsonPretty(doc, Serial);
            Serial.println();
        }
    } else {
        Serial.printf("Error HTTP (%d) al obtener credenciales Telegram.\n", httpCode);
    }

    http.end();
}
