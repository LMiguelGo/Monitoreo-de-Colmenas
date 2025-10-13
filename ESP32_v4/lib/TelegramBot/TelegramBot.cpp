#include "TelegramBot.h"

TelegramBot::TelegramBot(const String& token, const String& chat_id)
    : botToken(token), chatId(chat_id) {}

// ==========================
// Setters
// ==========================
void TelegramBot::setBotToken(const String& token) {
    botToken = token;
}

void TelegramBot::setChatId(const String& id) {
    chatId = id;
}

// ==========================
// Enviar mensaje
// ==========================
bool TelegramBot::sendMessage(const String& message) {
    if (WiFi.status() != WL_CONNECTED) return false;

    WiFiClientSecure client;
    client.setInsecure();

    HTTPClient https;
    String url = apiHost + "/bot" + botToken + "/sendMessage";
    String payload = "chat_id=" + chatId + "&text=" + urlencode(message);

    https.begin(client, url);
    https.addHeader("Content-Type", "application/x-www-form-urlencoded");

    int httpCode = https.POST(payload);
    https.end();

    return httpCode == 200;
}

// ==========================
// Obtener mensajes nuevos (con offset)
// ==========================
String TelegramBot::getNextMessage(int &lastUpdateId) {
    if (WiFi.status() != WL_CONNECTED) return "";

    WiFiClientSecure client;
    client.setInsecure();

    HTTPClient https;
    String url = apiHost + "/bot" + botToken + "/getUpdates?offset=" + String(lastUpdateId + 1);

    https.begin(client, url);
    int httpCode = https.GET();

    if (httpCode != 200) {
        https.end();
        return "";
    }

    String response = https.getString();
    https.end();

    int updateIdIndex = response.indexOf("\"update_id\":");
    if (updateIdIndex == -1) return "";

    int idStart = updateIdIndex + 12;
    int idEnd = response.indexOf(",", idStart);
    int updateId = response.substring(idStart, idEnd).toInt();

    if (updateId <= lastUpdateId) return "";

    lastUpdateId = updateId;

    int textIndex = response.indexOf("\"text\":\"");
    if (textIndex == -1) return "";

    int start = textIndex + 8;
    int end = response.indexOf("\"", start);
    String text = response.substring(start, end);

    return text;
}

// ==========================
// urlencode
// ==========================
String TelegramBot::urlencode(const String& str) {
    String encoded = "";
    char c;
    char code0;
    char code1;

    for (int i = 0; i < str.length(); i++) {
        c = str.charAt(i);
        if (isalnum(c)) {
            encoded += c;
        } else {
            code0 = (c >> 4) & 0xF;
            code1 = c & 0xF;
            encoded += '%';
            encoded += char(code0 > 9 ? code0 - 10 + 'A' : code0 + '0');
            encoded += char(code1 > 9 ? code1 - 10 + 'A' : code1 + '0');
        }
    }
    return encoded;
}
