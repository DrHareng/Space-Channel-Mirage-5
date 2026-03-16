<?php

namespace App\Controller;

use App\Entity\Photo;
use App\Entity\Comment;
use App\Form\PhotoType;
use App\Form\CommentType;
use App\Repository\PhotoRepository;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\HttpFoundation\JsonResponse;

#[Route('/gallery')]
final class PhotoController extends AbstractController
{
    #[Route('/', name: 'app_gallery')]
    public function index(PhotoRepository $photoRepository, TagRepository $tagRepository, Request $request): Response
    {
        $tagFilter = $request->query->get('tag');
        $tags = $tagRepository->findAll();
        
        $photos = $tagFilter 
            ? $photoRepository->findByTag($tagFilter)
            : $photoRepository->findBy([], ['createdAt' => 'DESC']);

        return $this->render('gallery/index.html.twig', [
            'photos' => $photos,
            'tags' => $tags,
            'selectedTag' => $tagFilter,
        ]);
    }

    #[Route('/upload', name: 'app_photo_upload', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function upload(Request $request, EntityManagerInterface $entityManager): Response
    {
        $photo = new Photo();
        $form = $this->createForm(PhotoType::class, $photo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photo->setAuthor($this->getUser());
            $entityManager->persist($photo);
            $entityManager->flush();

            $this->addFlash('success', 'photo.uploaded_successfully');
            return $this->redirectToRoute('app_gallery');
        }

        return $this->render('gallery/upload.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/photo/{id}', name: 'app_photo_show', methods: ['GET', 'POST'])]
    public function show(Photo $photo, Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setAuthor($this->getUser());
            $comment->setPhoto($photo);
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('success', 'comment.added_successfully');
            return $this->redirectToRoute('app_photo_show', ['id' => $photo->getId()]);
        }

        return $this->render('gallery/show.html.twig', [
            'photo' => $photo,
            'form' => $form,
        ]);
    }

    #[Route('/photo/{id}/like', name: 'app_photo_like', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function like(Photo $photo, EntityManagerInterface $entityManager): JsonResponse
    {
        $user = $this->getUser();
        
        if ($photo->isLikedBy($user)) {
            $photo->removeLikedBy($user);
            $liked = false;
        } else {
            $photo->addLikedBy($user);
            $liked = true;
        }

        $entityManager->flush();

        return new JsonResponse([
            'liked' => $liked,
            'likesCount' => $photo->getLikesCount(),
        ]);
    }

    #[Route('/my', name: 'app_my_gallery')]
    #[IsGranted('ROLE_USER')]
    public function myGallery(PhotoRepository $photoRepository): Response
    {
        $photos = $photoRepository->findBy(['author' => $this->getUser()], ['createdAt' => 'DESC']);

        return $this->render('gallery/my_gallery.html.twig', [
            'photos' => $photos,
        ]);
    }

    #[Route('/photo/{id}/delete', name: 'app_photo_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Photo $photo, EntityManagerInterface $entityManager): Response
    {
        if ($photo->getAuthor() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException('You can only delete your own photos.');
        }

        $entityManager->remove($photo);
        $entityManager->flush();

        $this->addFlash('success', 'photo.deleted_successfully');
        return $this->redirectToRoute('app_my_gallery');
    }
}
