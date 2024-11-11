# ProductService

Documentation du ProductService

## Prérequis
- Composer 2.7 or above [\<link\>](https://getcomposer.org/doc/00-intro.md)
- Node [\<link\>](https://nodejs.org/en/download/)
- PHP 8.2 or above [\<link\>](https://www.php.net/downloads)
- Docker 27 or above [\<link\>](https://docs.docker.com/get-docker/)

## Installation

1. Clone le repository
```bash
git clone https://github.com/EFREI-Microservices/ProductService.git
```
2. Installer les dépendances
```bash
composer install
```

3. Lancer la base de données
```bash
docker-compose up -d
```

4. Générer les données de test
```bash
npm run truncate-database
```

5. Lancer le serveur
```bash
npm run start
```

## Endpoints

L'API est accessible à l'adresse `http://localhost:8010/`  

Liste des endpoints :

#### [GET] `/api/products`
Retourne la liste de tous les produits

#### [GET] `/api/products/{id}`
Retourne le produit correspondant à l'id
```json
{
    "id": int,
    "name": string,
    "description": string,
    "price": int,
    "available": bool
}
```

#### [POST] `/api/products`
Créer un nouveau produit.  
Autorisations d'admin requises (token JWT)
```json
{
    "name": string,
    "description": string,
    "price": int,
    "available": bool
}
```

#### [PATCH] `/api/products/{id}`
Mettre à jour un produit. Chaque champ est optionnel.  
Autorisations d'admin requises (token JWT)
```json
{
    "name": string,
    "description": string,
    "price": int,
    "available": bool
}
```

#### [DELETE] `/api/products/{id}`
Supprimer un produit.  
Autorisations d'admin requises (token JWT)

