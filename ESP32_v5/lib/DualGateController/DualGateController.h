#ifndef DUALGATECONTROLLER_H
#define DUALGATECONTROLLER_H

#include <Arduino.h>
#include <ESP32Servo.h>

class DualGateController {
private:
    Servo gateServo1;
    Servo gateServo2;
    int servoPin1;
    int servoPin2;

    int currentAngle1;
    int currentAngle2;

public:
    // Constructor: initializes both servo pins
    DualGateController(int pin1, int pin2);

    // Set angle for servo 1
    void setAngle1(int angle);

    // Set angle for servo 2
    void setAngle2(int angle);

    // Get individual angles
    int getAngle1() const;
    int getAngle2() const;
};

#endif
