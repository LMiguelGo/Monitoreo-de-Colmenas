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

    // Set Bot Token
    void setBotToken(const String& token);

    // Set Chat ID
    void setChatId(const String& id);

    // Method to send a message to the chat
    bool sendMessage(const String& message);

    // Method to get new messages with offset
    String getNextMessage(int &lastUpdateId);
};

#endif
