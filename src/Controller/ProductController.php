<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProductController extends AbstractController
{/**
     * Liste des produits
     * @Route("/produit/liste")
     * @param ProductRepository $Repository
     * @return Response
     */
    public function index(): Response
    {
        // Récupération du Repository des produits
        $repository = $this->getDoctrine()
            ->getRepository(Product::class);
        // Récupération de tous les produits publiés
        $products = $repository->findBy([
            'isPublished' => true
        ]);
        // Renvoi des produits à la vue
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * Affiche et traite le formulaire d'ajout d'un produit
     * @Route("/produit/creation", methods={"GET", "POST"})
     * @param Request $requestHTTP
     * @return Response
     */
    public function create(Request $requestHTTP): Response
    {
        //Récupération d'une catégorie
        $category = $this->getDoctrine()
            ->getRepository(Category::class)
            ->find(1)
            ;
        // Récupération des POSTS
        dump($requestHTTP->request);
        //Création et remplissage du produit
        $product = new Product();
            $product->setName('Ventilateur')
                ->setDescription('pour faire du froid')
                ->setImageName('ventilateur.jpg')
                ->setIsPublished(true)
                ->setPrice(15.99)
                ->setCategory($category)
                ;


        $manager = $this->getDoctrine()->getManager();
        $manager -> persist($product) ;
        $manager ->flush();


        //On sauvegarde le en BDD grâce au manager




        return $this->render('product/create.html.twig');
    }



    /**
     * Affiche le détail d'un produit
     * @Route("/produit/{slug<[a-z0-9\-]+>}", methods={"GET", "POST"})
     * @param string $slug
     * @return Response
     */
    public function show(string $slug): Response
    {
        // Récupération du repository
        $repository = $this->getDoctrine()->getRepository(Product::class);
        // Récupération du produit lié au slug de l'URL
        $product = $repository->findOneBy([
            'slug' => $slug,
            'isPublished' => true
        ]);
        // Si on a pas de produit -> page 404
        if (!$product) {
            throw $this->createNotFoundException('Produit non-trouvé !');
        }
        // Renvoi du produit à la vue
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
