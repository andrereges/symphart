<?php
namespace App\Controller;

use App\Entity\Article;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class ArticleController extends Controller
{

    /**
     * @Route("/", name="articles")
     * @Method({"GET"})
     */
    public function index()
    {
       // return new Response('<html><body>Hello</body></html>');
       $articles = $this->getDoctrine()->getRepository(Article::class)->findAll();
       return $this->render('articles/index.html.twig', array('articles' => $articles));
    }

    /**
     * @Route("/article/new", name="create_article")
     * Method({"GET", "POST"})
     */
    public function create(Request $request)
    {
        $article = new Article();

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class,
            [
                'attr' => ['class' => 'form-control']
            ])
            ->add('body', TextareaType::class,
            [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class,
            [
                'label' => 'Create',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/edit/{id}", name="edit_article")
     * Method({"GET", "POST"})
     */
    public function edit(Request $request, $id)
    {
        $article = new Article();

        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $form = $this->createFormBuilder($article)
            ->add('title', TextType::class,
            [
                'attr' => ['class' => 'form-control']
            ])
            ->add('body', TextareaType::class,
            [
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('save', SubmitType::class,
            [
                'label' => 'Update',
                'attr' => ['class' => 'btn btn-primary mt-3']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            return $this->redirectToRoute('articles');
        }

        return $this->render('articles/edit.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/article/{id}", name="show_article")
     */
    public function show($id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        return $this->render('articles/show.html.twig', array('article' => $article));
    }

    /**
     * @Route("/article/delete/{id}", name="delete_article")
     * @Method({"DELETE"})
     */
    public function delete(Request $request, $id)
    {
        $article = $this->getDoctrine()->getRepository(Article::class)->find($id);

        $em = $this->getDoctrine()->getManager();
        $em->remove($article);
        $em->flush();

        $response = new Response();
        $response->send();
    }

    // /**
    //  * @Route("/article/save")
    //  */
    // public function save()
    // {
    //     $em = $this->getDoctrine()->getManager();

    //     $article = new Article();

    //     $article->setTitle('Article two');
    //     $article->setBody('This is the body for article two');

    //    $em->persist($article);
    //    $em->flush();

    //     return new Response('Saved an article with the id of '.$article->getId());
    // }
}