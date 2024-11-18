<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractTokenAuthenticatedController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient
    ) {}

    final public function isAdmin(string $token): bool
    {
        try {

        $response = $this->httpClient->request('GET', 'http://localhost:8009/api/auth/check-token', [
            'headers' => [
                'Authorization' => "Bearer {$token}"
            ]
        ]);

            $data = $response->toArray();

            return $data['role'] === 'admin';
        } catch (ClientExceptionInterface|DecodingExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface $error) {
            return false;
        }
    }

    final public function extractTokenFromRequest(Request $request): string
    {
        $token = $request->headers->get('Authorization');
        return str_replace('Bearer ', '', $token);
    }
}
