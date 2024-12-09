<?php

require_once __DIR__ . '/../Services/JsonStorage.php';

class PaymentService {
    private $repository;

    public function __construct(JsonStorage $repository = null) {
        $this->repository = $repository ?? new JsonStorage();
    }

    public function doPayment(array $data): array {
        $this->validatePaymentData($data);
        
        $cardNumber = $data['cardNumber'];
        $amount = floatval($data['amount']);
        $installments = intval($data['installments']);
        
        $this->validateAmount($amount);
        $this->validateInstallments($installments);
        
        $card = $this->repository->getCard($cardNumber);
        $totalAmount = $this->calculateTotalAmount($amount, $installments);
        
        if (!$card->hasEnoughLimit($totalAmount)) {
            throw new \Exception(sprintf(
                "Insufficient limit. Current limit: %.2f, Required amount: %.2f",
                $card->getLimit(),
                $totalAmount
            ));
        }

        $card->reduceLimit($totalAmount);
        $this->repository->saveCard($card);

        return [
            "status" => "success",
            "message" => "Payment processed successfully",
            "ticket" => $this->generateTicket($card, $totalAmount, $installments),
        ];
    }

    private function validatePaymentData(array $data): void {
        $requiredFields = ['cardNumber', 'amount', 'installments'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                throw new \Exception("Missing required field: $field");
            }
        }
    }

    private function validateAmount(float $amount): void {
        if ($amount <= 0) {
            throw new \Exception("Amount must be greater than zero");
        }
    }

    private function validateInstallments(int $installments): void {
        if ($installments < 1 || $installments > 6) {
            throw new \Exception("Number of installments must be between 1 and 6");
        }
    }

    private function calculateTotalAmount(float $amount, int $installments): float {
        if ($installments <= 1) {
            return round($amount, 2);
        }
        return round($amount * (1 + ($installments - 1) * 0.03), 2);
    }

    private function generateTicket(Card $card, float $totalAmount, int $installments): array {
        return [
            "client" => $card->getClient()->getFullName(),
            "totalAmount" => round($totalAmount, 2),
            "installments" => $installments,
            "installmentAmount" => round($totalAmount / $installments, 2)
        ];
    }
} 