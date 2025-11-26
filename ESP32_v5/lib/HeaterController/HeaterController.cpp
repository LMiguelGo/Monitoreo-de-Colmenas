#include "HeaterController.h"

HeaterController::HeaterController(int pin) : heaterPin(pin), isOn(false) {
    pinMode(heaterPin, OUTPUT);
    digitalWrite(heaterPin, LOW); // Ensure heater starts OFF
}

void HeaterController::turnOn() {
    digitalWrite(heaterPin, HIGH);
    isOn = true;
}

void HeaterController::turnOff() {
    digitalWrite(heaterPin, LOW);
    isOn = false;
}

bool HeaterController::getState() const {
    return isOn;
}
