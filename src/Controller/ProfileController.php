<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ProfileFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile', methods: ['GET', 'POST'])]
    public function profile(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        EntityManagerInterface $entityManager,
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();
        if ($user === null) {
            return $this->redirectToRoute('app_login');
        }

        $profileForm = $this->createForm(ProfileFormType::class, $user);
        $profileForm->handleRequest($request);

        $passwordForm = $this->createForm(ChangePasswordFormType::class);
        $passwordForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            /** @var UploadedFile|null $pictureFile */
            $pictureFile = $profileForm->get('profilePicture')->getData();

            if ($pictureFile instanceof UploadedFile) {
                $filesystem = new Filesystem();
                $projectDir = (string) $this->getParameter('kernel.project_dir');
                $relativeDir = 'uploads/avatars';
                $targetDir = $projectDir . '/public/' . $relativeDir;

                if (!$filesystem->exists($targetDir)) {
                    $filesystem->mkdir($targetDir);
                }

                $safeBase = preg_replace('/[^a-z0-9]/i', '_', (string) $user->getEmail());
                $safeBase = strtolower((string) $safeBase);

                $extension = $pictureFile->guessExtension() ?? 'bin';
                if ($extension === 'jpeg') {
                    $extension = 'jpg';
                }

                $newFilename = $safeBase . '.' . $extension;

                $oldFilename = $user->getProfilePictureFilename();
                if ($oldFilename !== null && $oldFilename !== $newFilename) {
                    $oldPath = $targetDir . '/' . $oldFilename;
                    if ($filesystem->exists($oldPath)) {
                        $filesystem->remove($oldPath);
                    }
                }

                $pictureFile->move($targetDir, $newFilename);
                $user->setProfilePictureFilename($newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Profile updated.');

            return $this->redirectToRoute('app_profile');
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            $newPassword = (string) $passwordForm->get('newPassword')->getData();
            $user->setPassword($passwordHasher->hashPassword($user, $newPassword));
            $entityManager->flush();

            $this->addFlash('success', 'Password updated.');

            return $this->redirectToRoute('app_profile');
        }

        return $this->render('profile/profile.html.twig', [
            'profileForm' => $profileForm,
            'passwordForm' => $passwordForm,
            'avatar_path' => $user->getProfilePictureFilename(),
        ]);
    }
}

