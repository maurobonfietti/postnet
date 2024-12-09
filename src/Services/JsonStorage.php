<?php

require_once __DIR__ . '/../Models/Client.php';
require_once __DIR__ . '/../Models/Card.php';

class JsonStorage {
    private $cards = [];
    private $filePath;

    public function __construct() {
        $this->filePath = __DIR__ . '/../../data/cards.json';
        $this->loadCards();
    }

    private function loadCards(): void {
        if (!file_exists($this->filePath)) {
            return;
        }
        
        $jsonContent = file_get_contents($this->filePath);
        if (!$jsonContent) {
            throw new \RuntimeException("Error reading JSON file: {$this->filePath}");
        }
        
        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Invalid JSON format: " . json_last_error_msg());
        }
        
        foreach ($data as $cardData) {
            $client = new Client(
                $cardData['client']['dni'],
                $cardData['client']['name'],
                $cardData['client']['surname']
            );
            $card = new Card(
                $cardData['bankName'],
                $cardData['cardNumber'],
                $cardData['limit'],
                $client
            );
            $this->cards[$card->getCardNumber()] = $card;
        }
    }

    private function saveToFile(): void {
        $data = [];
        foreach ($this->cards as $card) {
            $data[] = [
                'bankName' => $card->getBankName(),
                'cardNumber' => $card->getCardNumber(),
                'limit' => $card->getLimit(),
                'client' => [
                    'dni' => $card->getClient()->getDni(),
                    'name' => $card->getClient()->getName(),
                    'surname' => $card->getClient()->getSurname()
                ]
            ];
        }
        if (file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT)) === false) {
            throw new \RuntimeException("Error writing to file: {$this->filePath}");
        }
    }

    public function saveCard(Card $card): void {
        $this->cards[$card->getCardNumber()] = $card;
        $this->saveToFile();
    }

    public function getCard(string $cardNumber): Card {
        if (!isset($this->cards[$cardNumber])) {
            throw new \Exception("Card not found");
        }
        return $this->cards[$cardNumber];
    }

    public function cardExists(string $cardNumber): bool {
        return isset($this->cards[$cardNumber]);
    }

    public function clientExists(string $dni): bool {
        foreach ($this->cards as $card) {
            if ($card->getClient()->getDni() === $dni) {
                return true;
            }
        }
        return false;
    }
} 