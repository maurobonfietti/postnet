<?php

class Client {
    private $dni;
    private $name;
    private $surname;

    public function __construct(string $dni, string $name, string $surname) {
        $this->validateDni($dni);
        $this->validateName($name);
        $this->validateSurname($surname);

        $this->dni = $dni;
        $this->name = $name;
        $this->surname = $surname;
    }

    private function validateDni(string $dni): void {
        if (!ctype_digit($dni)) {
            throw new Exception("DNI must contain only digits");
        }
        if (strlen($dni) != 8) {
            throw new Exception("DNI must be 8 digits");
        }
    }

    private function validateName(string $name): void {
        if (empty(trim($name))) {
            throw new Exception("Name cannot be empty");
        }
        if (strlen($name) > 50) {
            throw new Exception("Name is too long");
        }
    }

    private function validateSurname(string $surname): void {
        if (empty(trim($surname))) {
            throw new Exception("Surname cannot be empty");
        }
        if (strlen($surname) > 50) {
            throw new Exception("Surname is too long");
        }
    }

    public function getDni(): string {
        return $this->dni;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getSurname(): string {
        return $this->surname;
    }

    public function getFullName(): string {
        return $this->name . ' ' . $this->surname;
    }
} 