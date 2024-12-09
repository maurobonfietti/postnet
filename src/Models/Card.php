<?php

class Card {
    private $bankName;
    private $cardNumber;
    private $limit;
    private $client;

    public function __construct(string $bankName, string $cardNumber, float $limit, Client $client) {
        $this->validateBankName($bankName);
        $this->validateCardNumber($cardNumber);
        $this->validateLimit($limit);

        $this->bankName = $bankName;
        $this->cardNumber = $cardNumber;
        $this->limit = $limit;
        $this->client = $client;
    }

    private function validateBankName(string $bankName): void {
        if (empty(trim($bankName))) {
            throw new Exception("Bank name cannot be empty");
        }
        if (strlen($bankName) > 100) {
            throw new Exception("Bank name is too long");
        }
    }

    private function validateCardNumber(string $cardNumber): void {
        if (!ctype_digit($cardNumber)) {
            throw new Exception("Card number must contain only digits");
        }
        if (!in_array(substr($cardNumber, 0, 1), ['4', '3'])) {
            throw new Exception("Only Visa (4) or AMEX (3) cards are allowed");
        }
        if (strlen($cardNumber) != 8) {
            throw new Exception("Card number must be 8 digits");
        }
    }

    private function validateLimit(float $limit): void {
        if ($limit < 0) {
            throw new Exception("Limit cannot be negative");
        }
    }

    public function getBankName(): string {
        return $this->bankName;
    }

    public function getCardNumber(): string {
        return $this->cardNumber;
    }

    public function getLimit(): float {
        return $this->limit;
    }

    public function getClient(): Client {
        return $this->client;
    }

    public function hasEnoughLimit(float $amount): bool {
        return $this->limit >= $amount;
    }

    public function reduceLimit(float $amount): void {
        if (!$this->hasEnoughLimit($amount)) {
            throw new Exception("Insufficient limit");
        }
        $this->limit -= $amount;
    }

    public static function fromArray(array $data): self {
        if (!isset($data['bankName'], $data['cardNumber'], $data['limit'], 
                  $data['client']['dni'], $data['client']['name'], $data['client']['surname'])) {
            throw new Exception("Incomplete card data");
        }

        $client = new Client(
            $data['client']['dni'],
            $data['client']['name'],
            $data['client']['surname']
        );

        return new self(
            $data['bankName'],
            $data['cardNumber'],
            floatval($data['limit']),
            $client
        );
    }
}
