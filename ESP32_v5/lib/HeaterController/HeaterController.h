#ifndef HEATERCONTROLLER_H
#define HEATERCONTROLLER_H

#include <Arduino.h>

class HeaterController {
private:
    int heaterPin;
    bool isOn;

public:
    // Constructor: initializes the heater control pin
    HeaterController(int pin);

    // Turns the heater ON
    void turnOn();

    // Turns the heater OFF
    void turnOff();

    // Returns true if the heater is ON, false otherwise
    bool getState() const;
};

#endif
