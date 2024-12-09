<?php

require_once __DIR__ . '/../Services/PaymentService.php';
require_once __DIR__ . '/../Traits/JsonRequestTrait.php';

class PaymentController {
    use JsonRequestTrait;
    private $paymentService;

    public function __construct() {
        $this->paymentService = new PaymentService();
    }

    public function doPayment() {
        $input = $this->getJsonInput();
        $response = $this->paymentService->doPayment($input);
        http_response_code(200);
        return $response;
    }
}
