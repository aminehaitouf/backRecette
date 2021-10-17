<?php

namespace App\Controller;

use App\Entity\Recette;
use App\Form\Recette1Type;
use App\Entity\Ingredients;

use App\Form\IngredientsType;
use App\Repository\RecetteRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\IngredientsRepository;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/acceuil")
 */
class AcceuilController extends AbstractController
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/", name="acceuil_index", methods={"GET"})
     */
    public function index(RecetteRepository $recetteRepository): Response
    {
        $recettes=$recetteRepository->findAll();
        


        return $this->render('default/listeRecettes.html.twig', [
            'recettes' => $recettes,
        ]);
    }

    /**
     * @Route("/api/listeRecettes", name="listeRecettes", methods={"GET"})
     */
    public function listeRecettes(RecetteRepository $recetteRepository): Response
    {
        $recettes=$recetteRepository->findAll();
        $encoders = [new JsonEncoder()];
        $normalizers= [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers,$encoders);
        $jsonContent = $serializer->serialize($recettes,'json',[
            'circular_reference_handler' => function($object){
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response ->headers->set('Content-Type', 'application/json');
         return $response;


        
    }

    /**
     * @Route("/new", name="acceuil_new", methods={"GET","POST"})
     */
    public function new(Request $request,IngredientsRepository $ingredientsRepository): Response
    {
        $ingredients=$ingredientsRepository->findAll();
        $ingredients = new Ingredients();
        $formingredient = $this->createForm(IngredientsType::class, $ingredients);
        $formingredient->handleRequest($request);

        if ($formingredient->isSubmitted() && $formingredient->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredients);
            $entityManager->flush();
            // return $this->redirectToRoute('ajoutOptionad', ['id' => $Ads]);

        }
        $recette = new Recette();
        $form = $this->createForm(Recette1Type::class, $recette);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($recette);
            $entityManager->flush();

            return $this->redirectToRoute('acceuil_index', [], Response::HTTP_SEE_OTHER);
        }

        
        return $this->renderForm('default/ajoutSignalement.html.twig', [
            'recette' => $recette,
            'form' => $form,
             'formingredient'=>$formingredient,
        ]);
    }
     /**
     * @Route("/api/newrecette", name="newrecette", methods={"GET","POST"})
     */
    public function newrecette(Request $request,IngredientsRepository $ingredientsRepository): Response
    {
        
            // $donnees = json_decode($request->getContent());
            // if($donnees){

            //     $recette = new Recette();
            //     $recette->setTitre($donnees->titre);
            //     $recette->setSousTitre($donnees->sousTitre);
            //    // dd($donnees);
                
            //     foreach ($donnees->ingredients as $a){
            //         $recette->addIngredient($a);
            //     }

            //     $entityManager = $this->getDoctrine()->getManager();
            // $entityManager->persist($recette);
            // $entityManager->flush();
            // // return new JsonResponse($donnees);
            // }

       
        return new JsonResponse($this->em->createQuery("select ingredients from App\Entity\Ingredients ingredients")->getArrayResult());
       
    }


    /**
     * @Route("/{id}", name="acceuil_show", methods={"GET"})
     */
    public function show(Recette $recette): Response
    {
        $encoders = [new JsonEncoder()];
        $normalizers= [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers,$encoders);
        $jsonContent = $serializer->serialize($recette,'json',[
            'circular_reference_handler' => function($object){
                return $object->getId();
            }
        ]);

        $response = new Response($jsonContent);
        $response ->headers->set('Content-Type', 'application/json');
        return $response;
        // return $this->render('acceuil/show.html.twig', [
        //     'recette' => $recette,
        // ]);
    }

    /**
     * @Route("/{id}/edit", name="acceuil_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Recette $recette): Response
    {
        $ingredients = new Ingredients();
        $formingredient = $this->createForm(IngredientsType::class, $ingredients);
        $formingredient->handleRequest($request);

        if ($formingredient->isSubmitted() && $formingredient->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ingredients);
            $entityManager->flush();
            // return $this->redirectToRoute('ajoutOptionad', ['id' => $Ads]);

        }
        $form = $this->createForm(Recette1Type::class, $recette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('acceuil_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('default/modifierRecettes.html.twig', [
            'recette' => $recette,
            'form' => $form,
            'formingredient'=>$formingredient,
        ]);
    }

    /**
     * @Route("/{id}/delete", name="acceuil_delete", methods={"POST","GET"})
     */
    public function delete(Request $request, Recette $recette): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();

         return $this->redirectToRoute('acceuil_index', [], Response::HTTP_SEE_OTHER);
    }
    /**
     * @Route("/api/delete/{id}", name="deleteRecette", methods={"DELETE"})
     */
    public function deleteRecette(Request $request, Recette $recette): Response
    {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($recette);
            $entityManager->flush();
            return new Response('OK');

        // return $this->redirectToRoute('acceuil_index', [], Response::HTTP_SEE_OTHER);
    }
}
