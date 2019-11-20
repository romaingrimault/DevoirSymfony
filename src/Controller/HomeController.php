<?php

namespace App\Controller;

use App\Entity\Produit;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {

        $repository=$this->getDoctrine()->getRepository(Produit::class);


        $categories=$repository->findAll();



        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }
    /**
     * @Route("/ajouter",name="produit_ajouter")
     */

    public function ajouter(Request $request){

        $produit=new Produit();


        $formulaire=$this->createForm(ProduitType::class,$produit);


        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted()&& $formulaire->isValid()){
            $data=$formulaire->get('EAN')->getData();
            $nbCharactere=strlen($data);
           if($nbCharactere==13 || $nbCharactere==14){
                if(ctype_digit($data)){
                    /*$total=0;
                    for ($i=0;$i<$nbCharactere--;$i++){
                        if($i%2==0){
                            $total=$total+$data[$i]*3;
                        }
                        else{
                            $total=$total+$data[$i]*1;
                        }
                    }
                    $totalsup=round($total,-1);
                    var_dump($totalsup);
                    var_dump($total);
                    $nbCharactere--;

                    if($totalsup-$total==$data[$nbCharactere]) {*/

                        $em = $this->getDoctrine()->getManager();

                        $em->persist($produit);

                        $em->flush();
                        return $this->redirectToRoute("home");
                    /*}
                    else{
                        //return $this->redirectToRoute("home");
                    }*/
           }
                else{
                    return $this->redirectToRoute("home");
                }
          }
           else{
               return $this->redirectToRoute("home");
           }





        }
        return $this->render('home/formulaire.html.twig',[
            "formulaire"=>$formulaire->createView(),
            "h1"=>"Modifier le Produit",

        ]);
}
    /**
     * @Route("/produit/modifier/{id}",name="produit_modifier")
     */

    public function modifier(Request $request,$id){
        //je vais chercher l'objet à modifier

        $repository=$this->getDoctrine()->getRepository(Produit::class);
        $categorie=$repository->find($id);

        //créer le formulaire
        $formulaire=$this->createForm(ProduitType::class,$categorie);

        //récupérer les données du POST
        $formulaire->handleRequest($request);

        if($formulaire->isSubmitted()&& $formulaire->isValid()){
            //récupération de l'entity manager
            $em=$this->getDoctrine()->getManager();
            //je dis au manager de garder cet objet en BDD

            $em->persist($categorie);

            //execute l'update
            $em->flush();

            //je m'en vais
            return $this->redirectToRoute("home");

        }

        return $this->render('home/formulaire.html.twig',[
            "formulaire"=>$formulaire->createView(),
            "h1"=>"Modifier la catégorie".$categorie->getNom(),


        ]);
    }
    /**
     * @Route("/produit/supprimer/{id}",name="produit_supprimer")
     */
    public function supprimer($id){
        $repository=$this->getDoctrine()->getRepository(Produit::class);
        $produit=$repository->find($id);
        $em=$this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        return $this->redirectToRoute("home");}


}
