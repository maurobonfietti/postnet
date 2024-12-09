<?php

require_once __DIR__ . '/../Services/CardService.php';
require_once __DIR__ . '/../Traits/JsonRequestTrait.php';

class CardController {
    use JsonRequestTrait;
    private $cardService;

    public function __construct() {
        $this->cardService = new CardService();
    }

    public function register() {
        $input = $this->getJsonInput();
        $response = $this->cardService->registerCard($input);
        http_response_code(201);
        return $response;
    }
}
