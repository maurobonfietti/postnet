<?php

trait JsonRequestTrait {
    private function getJsonInput(): array {
        $rawInput = file_get_contents('php://input');
        $input = json_decode($rawInput, true);
        
        if (is_null($input)) {
            throw new Exception('Invalid JSON payload');
        }
        
        return $input;
    }
}
