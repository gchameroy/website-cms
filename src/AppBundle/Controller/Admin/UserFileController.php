<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\UserFile;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/user-files")
 */
class UserFileController Extends Controller
{
    /**
     * @Route("/{userFile}/download", name="admin_user_file_download", requirements={"userFile": "\d+"})
     * @Method({"GET"})
     *
     * @param integer $userFile
     *
     * @return Response
     */
    public function downloadAction(int $userFile): Response
    {
        $userFile = $this->getDoctrine()
            ->getRepository(UserFile::class)
            ->find($userFile);
        $this->checkUserFile($userFile);

        $filePath = sprintf(
            '%s/../uploads/user-files/%s/%s',
            $this->get('kernel')->getRootDir(),
            $userFile->getUser()->getId(),
            $userFile->getFile()->getPath()
        );
        $this->checkFilePath($filePath);

        $response = new Response(file_get_contents($filePath));
        $disposition = $response->headers->makeDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            sprintf('%s.%s', $userFile->getFile()->getTitle(), $userFile->getFile()->getExtension())
        );
        $response->headers->set('Content-Disposition', $disposition);

        return $response;
    }

    /**
     * @Route("/delete", name="admin_user_file_delete")
     * @Method({"POST"})
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     *
     * @return Response
     */
    public function deleteAction(Request $request, EntityManagerInterface $entityManager): Response
    {
        $userFile = $this->getDoctrine()
            ->getRepository(UserFile::class)
            ->find($request->request->get('userFile'));
        $this->checkUserFile($userFile);
        $user = $userFile->getUser();

        $token = $request->request->get('token');
        if ($this->isCsrfTokenValid('user-file-delete', $token)) {
            $entityManager->remove($userFile);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_user', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @param UserFile|null $userFile
     */
    private function checkUserFile(?UserFile $userFile): void
    {
        if (!$userFile) {
            throw $this->createNotFoundException('File Not Found.');
        }
    }

    /**
     * @param string|null $filePath
     */
    private function checkFilePath(?string $filePath): void
    {
        if (!$filePath) {
            throw $this->createNotFoundException('File Not Found.');
        }
    }
}
