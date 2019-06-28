<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductType;
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
        //Récupération du formulaire
        $product = new Product();
        $formProduct = $this->createForm(ProductType::class,$product);

        //On envoi les donnees postées au formulaire
        $formProduct->handleRequest($requestHTTP);

        //On vérifie que le formulaire est soumis et valide
        if($formProduct->isSubmitted() && $formProduct->isValid()){
            $manager = $this->getDoctrine()->getManager();
            $manager -> persist($product) ;
            $manager ->flush();
        }

        /*
         On sauvegarde le produi  en BDD grâce au manager
        $manager = $this->getDoctrine()->getManager();
        $manager -> persist($product) ;
        $manager ->flush();
        */
        return $this->render('product/create.html.twig',[
            'formProduct' => $formProduct->createView()
            ]);
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
            throw $this->createNotFoundException('Produit non-trouvé ou non disponible !');
        }
        // Renvoi du produit à la vue
        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }
}
