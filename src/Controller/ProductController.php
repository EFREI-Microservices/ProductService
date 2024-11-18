<?php

namespace App\Controller;

use DateTime;
use JsonException;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/api/')]
final class ProductController extends AbstractTokenAuthenticatedController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
    ) {
        parent::__construct($this->httpClient);
    }

    #[Route('products', name: 'product_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $products = $this->productRepository->findAll();
        $data = [];

        foreach ($products as $product) {
            $data[] = [
                'id' => $product->getId(),
                'name' => $product->getName(),
                'description' => $product->getDescription(),
                'price' => $product->getPrice(),
                'available' => $product->getAvailable(),
                'createdAt' => $product->getCreatedAt()->format('Y-m-d H:i:s')
            ];
        }

        return new JsonResponse($data);
    }

    #[Route('products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(?Product $product): JsonResponse
    {
        $data = [
            'id' => $product?->getId(),
            'name' => $product?->getName(),
            'description' => $product?->getDescription(),
            'price' => $product?->getPrice(),
            'available' => $product?->getAvailable(),
            'createdAt' => $product?->getCreatedAt()->format('Y-m-d H:i:s')
        ];

        return new JsonResponse($data);
    }

    /**
     * @throws JsonException
     */
    #[Route('products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->isAdmin($this->extractTokenFromRequest($request))) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        if (empty($data['name']) || empty($data['description']) || empty($data['price']) || !isset($data['available'])) {
            return new JsonResponse(['error' => 'Invalid data'], Response::HTTP_BAD_REQUEST);
        }

        $product = (new Product())
            ->setName($data['name'])
            ->setDescription($data['description'])
            ->setPrice($data['price'])
            ->setAvailable($data['available'])
            ->setCreatedAt(new DateTime());

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product created!'], Response::HTTP_CREATED);
    }

    /**
     * @throws JsonException
     */
    #[Route('products/{id}', name: 'product_update', methods: ['PATCH'])]
    public function update(?Product $product, Request $request): JsonResponse
    {
        if (!$this->isAdmin($this->extractTokenFromRequest($request))) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $product->setName($data['name'] ?? $product->getName())
            ->setDescription($data['description'] ?? $product->getDescription())
            ->setPrice($data['price'] ?? $product->getPrice())
            ->setAvailable($data['available'] ?? $product->getAvailable());

        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product updated!']);
    }

    /**
     * @throws TransportExceptionInterface
     */
    #[Route('products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id, Request $request): JsonResponse
    {
        if (!$this->isAdmin($this->extractTokenFromRequest($request))) {
            return new JsonResponse(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $product = $this->entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted!']);
    }
}
