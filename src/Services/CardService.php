<?php

require_once __DIR__ . '/../Services/JsonStorage.php';

class CardService {
    private $repository;

    public function __construct(JsonStorage $repository = null) {
        $this->repository = $repository ?? new JsonStorage();
    }

    public function registerCard(array $data): array {
        $card = Card::fromArray($data);

        if ($this->repository->cardExists($card->getCardNumber())) {
            throw new \Exception("Card with number {$card->getCardNumber()} already exists");
        }

        if ($this->repository->clientExists($card->getClient()->getDni())) {
            throw new \Exception("Client with DNI {$card->getClient()->getDni()} already exists");
        }

        $this->repository->saveCard($card);

        return ["status" => "success", "message" => "Card registered successfully"];
    }
} 