#include "DualGateController.h"

DualGateController::DualGateController(int pin1, int pin2)
    : servoPin1(pin1), servoPin2(pin2), currentAngle1(0), currentAngle2(0) 
{
    gateServo1.attach(servoPin1);
    gateServo2.attach(servoPin2);

    gateServo1.write(0);
    gateServo2.write(0);
}

void DualGateController::setAngle1(int angle) {
    angle = constrain(angle, 0, 180);
    gateServo1.write(angle);
    currentAngle1 = angle;
}

void DualGateController::setAngle2(int angle) {
    angle = constrain(angle, 0, 180);
    gateServo2.write(angle);
    currentAngle2 = angle;
}

int DualGateController::getAngle1() const {
    return currentAngle1;
}

int DualGateController::getAngle2() const {
    return currentAngle2;
}
