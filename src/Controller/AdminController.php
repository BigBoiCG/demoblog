<?php

namespace App\Controller;

use App\Entity\Article;
use App\Form\ArticleType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Constraints\Date;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/admin/articles', name: 'admin_articles')]
    public function gestion(ArticleRepository $repo, EntityManagerInterface $manager): Response
    {
        $colonnes = $manager->getClassMetadata(Article::class)->getFieldNames();
        $articles = $repo->findAll();
        return $this->render('admin/gestionArticle.html.twig', [
            "colonnes" => $colonnes,
            "articles" => $articles
        ]);
    }

    #[Route('/admin/article/edit/{id}', name: 'admin_article_edit')]
    #[Route('/admin/article/new', name: 'admin_new_article')]
    public function formArticle(Request $request, EntityManagerInterface $manager, Article $article = null): Response
    {
        if($article == null)
        {
        $article = new Article;
        }
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $article->setCreatedAt(new \Datetime);
            $manager->persist($article);
            $manager->flush();
            $this->addFlash('success', 'Article ajouté');
            return $this->redirectToRoute('admin_articles');
        }

        return $this->render('admin/formArticle.html.twig', [
            "formArticle" => $form,
            'editMode' => $article->getId()!=null
        ]);
    }

    #[Route('/admin/article/delete/{id}', name: 'admin_delete_article')]
    public function deleteArticle(Article $article, EntityManagerInterface $manager): Response
    {
        $manager->remove($article);
        $manager->flush();
        $this->addFlash('success', "Suppression effectuée");
        return $this->redirectToRoute('admin_articles');
    }
}
