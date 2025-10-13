#include "ServerConnector.h"

ServerConnector::ServerConnector(const char* host, int port, int idTarjeta, TelegramBot* bot)
: host(host), port(port), idTarjeta(idTarjeta), bot(bot),
  tempMin(15.0), tempMax(30.0), humMin(30.0), humMax(70.0), freqMin(5), freqMax(20) {}

void ServerConnector::connectWiFi(const char* ssid, const char* password) {
    Serial.printf("Conectando a WiFi %s...\n", ssid);
    WiFi.begin(ssid, password);
    while (WiFi.status() != WL_CONNECTED) {
        delay(250);
    }
    Serial.println("\nWIFI CONECTADO.");
    Serial.print("IP: ");
    Serial.println(WiFi.localIP());
}

String ServerConnector::getHttpResponse(WiFiClient &client) {
    String line, body = "";
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

void ServerConnector::updateThresholdsFromDB() {
    WiFiClient client;
    if (!client.connect(host, port)) {
        Serial.println("❌ Error conectando a servidor (umbrales)");
        return;
    }

    String url = "/programas_php/proceso_eventos/actualizar_umbrales.php?colmena_id=" + String(idTarjeta);
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "Connection: close\r\n\r\n");

    String payload = getHttpResponse(client);
    client.stop();

    int idx1 = payload.indexOf(',');
    int idx2 = payload.indexOf(',', idx1 + 1);
    int idx3 = payload.indexOf(',', idx2 + 1);
    int idx4 = payload.indexOf(',', idx3 + 1);
    int idx5 = payload.indexOf(',', idx4 + 1);

    if (idx1 > 0 && idx2 > 0 && idx3 > 0 && idx4 > 0 && idx5 > 0) {
        tempMin = payload.substring(0, idx1).toFloat();
        tempMax = payload.substring(idx1 + 1, idx2).toFloat();
        humMin  = payload.substring(idx2 + 1, idx3).toFloat();
        humMax  = payload.substring(idx3 + 1, idx4).toFloat();
        freqMin = payload.substring(idx4 + 1, idx5).toInt();
        freqMax = payload.substring(idx5 + 1).toInt();

        Serial.printf("📡 Umbrales actualizados: T(%.1f–%.1f)°C, H(%.1f–%.1f)%%, F(%d–%d)\n",
                      tempMin, tempMax, humMin, humMax, freqMin, freqMax);
    } else {
        Serial.println("⚠ Error: formato de datos inválido.");
        Serial.println("Respuesta: " + payload);
    }
}

void ServerConnector::saveDataToDB(float temperatura, float humedad, int freqIn, int freqOut) {
    WiFiClient client;
    if (!client.connect(host, port)) {
        Serial.println("❌ Error conectando a servidor (guardar)");
        return;
    }

    String url = "/programas_php/proceso_eventos/guardar_datos_sensores.php?temperatura=" + String(temperatura) +
                 "&humedad=" + String(humedad) +
                 "&actividad_in=" + String(freqIn) +
                 "&actividad_out=" + String(freqOut) +
                 "&ID_TARJ=" + String(idTarjeta);

    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "Connection: close\r\n\r\n");

    Serial.printf("💾 Datos enviados -> Temp: %.1f, Hum: %.1f, In: %d, Out: %d\n",
                  temperatura, humedad, freqIn, freqOut);

    client.stop();
}

void ServerConnector::updateTelegramCredentials() {
    WiFiClient client;
    if (!client.connect(host, port)) {
        Serial.println("❌ Error conectando a servidor (Telegram)");
        return;
    }

    String url = "/programas_php/proceso_eventos/obtener_credenciales_tg.php?colmena_id=" + String(idTarjeta);
    client.print(String("GET ") + url + " HTTP/1.1\r\n" +
                 "Host: " + host + "\r\n" +
                 "Connection: close\r\n\r\n");

    String payload = getHttpResponse(client);
    client.stop();

    int idx = payload.indexOf(',');
    if (idx > 0) {
        String chatId = payload.substring(0, idx);
        String botToken = payload.substring(idx + 1);
        chatId.trim();
        botToken.trim();

        bot->setChatId(chatId);
        bot->setBotToken(botToken);

        Serial.println("🤖 Telegram actualizado:");
        Serial.println("Chat ID: " + chatId);
        Serial.println("Bot Token: " + botToken);
    } else {
        Serial.println("⚠ Error: formato de datos inválido.");
        Serial.println("Payload: " + payload);
    }
}
