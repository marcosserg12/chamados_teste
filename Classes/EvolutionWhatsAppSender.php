<?php

class EvolutionWhatsAppSender
{
    private $baseUrl;
    private $apiKey;
    private $instanceName;

    public function __construct(string $baseUrl, string $apiKey, string $instanceName)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;
        $this->instanceName = $instanceName;
    }

    /**
     * Envia uma mensagem via Evolution API
     *
     * @param string $toNumber Número em formato internacional (ex: 556198000000)
     * @param string $message Texto da mensagem
     * @return array Retorno com status, messageId, e outros dados
     * @throws Exception Em caso de erro de envio ou resposta inválida
     */
    public function sendMessage(string $toNumber, string $message): array
    {
        if (!preg_match('/^\d{11,13}$/', $toNumber)) {
            throw new Exception("Número inválido: {$toNumber}");
        }

        $payload = [
            'number' => $toNumber,
            'text' => $message,
        ];

        $url = "{$this->baseUrl}/message/sendText/{$this->instanceName}";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $this->apiKey,
            ],
            CURLOPT_POSTFIELDS => json_encode($payload),
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new Exception("Erro cURL: $curlError");
        }

        if (!in_array($httpCode, [200, 201])) {
            throw new Exception("Erro HTTP $httpCode. Resposta: $response");
        }

        $data = json_decode($response, true);

        if (!is_array($data) || !isset($data['key']['id'])) {
            throw new Exception("Resposta inválida da Evolution API: $response");
        }

        return [
            'success' => true,
            'messageId' => $data['key']['id'] ?? null,
            'to' => $data['key']['remoteJid'] ?? null,
            'message' => $data['message']['conversation'] ?? null,
            'timestamp' => $data['messageTimestamp'] ?? null,
            'raw' => $data,
        ];
    }
}
