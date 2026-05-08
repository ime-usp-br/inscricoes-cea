<?php

if (!class_exists('nusoap_client')) {
    class nusoap_client {
        public static $mockStatus = 'E'; // Default to Emitido
        public $fault = false;
        public $request = '';
        public $response = '';

        public function __construct($url, $wsdl) {}

        public function getError() { return false; }

        public function setHeaders($headers) {}

        public function call($method, $params) {
            if ($method === 'obterBoleto') {
                return ['boletoPDF' => base64_encode('fake_pdf_content')];
            }

            // Return structure matching BankSlip expectations
            return [
                'return' => true,
                'identificacao' => ['codigoIDBoleto' => '123'],
                'situacao' => [
                    'dataVencimentoBoleto' => now()->format('d/m/Y'),
                    'dataEfetivaPagamento' => null,
                    'valorEfetivamentePago' => 0,
                    'statusBoletoBancario' => self::$mockStatus
                ]
            ];
        }

        public function getDebug() { return ''; }
    }
}
