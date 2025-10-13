#ifndef TELEGRAM_BOT_H
#define TELEGRAM_BOT_H

#include <WiFiClientSecure.h>
#include <HTTPClient.h>

class TelegramBot {
private:
    String botToken;
    String chatId;
    String apiHost = "https://api.telegram.org";

    // Función privada para codificar texto (urlencode)
    String urlencode(const String& str);

public:
    TelegramBot(const String& token, const String& chat_id);

    // Setters
    void setBotToken(const String& token);
    void setChatId(const String& id);

    // Funciones principales
    bool sendMessage(const String& message);
    String getNextMessage(int &lastUpdateId);
};

#endif
