<?php

namespace App\Controller;

use DateTime;
use JsonException;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\UserPermissionService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/')]
final class ProductController extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly UserPermissionService $userPermissionService,
    ) {}

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

    /**
     * @throws JsonException
     */
    #[Route('products', name: 'product_create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        if (!$this->userPermissionService->isAdmin()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
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

    #[Route('products/{id}', name: 'product_show', methods: ['GET'])]
    public function show(?Product $product): JsonResponse
    {
        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $data = [
            'id' => $product->getId(),
            'name' => $product->getName(),
            'description' => $product->getDescription(),
            'price' => $product->getPrice(),
            'available' => $product->getAvailable(),
            'createdAt' => $product->getCreatedAt()->format('Y-m-d H:i:s')
        ];

        return new JsonResponse($data);
    }

    /**
     * @throws JsonException
     */
    #[Route('products/{id}', name: 'product_update', methods: ['PATCH'])]
    public function update(?Product $product, Request $request): JsonResponse
    {
        if (!$this->userPermissionService->isAdmin()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
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

    #[Route('products/{id}', name: 'product_delete', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        if (!$this->userPermissionService->isAdmin()) {
            return new JsonResponse(['error' => 'Access denied'], Response::HTTP_FORBIDDEN);
        }

        $product = $entityManager->getRepository(Product::class)->find($id);

        if (!$product) {
            return new JsonResponse(['error' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($product);
        $entityManager->flush();

        return new JsonResponse(['status' => 'Product deleted!']);
    }
}
