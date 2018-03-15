<?php

namespace AppBundle\Controller\Professional;

use AppBundle\Entity\UserFile;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

/**
 * @Route("/")
 */
class UserFileController Extends Controller
{
    /**
     * @Route("/", name="professional_user_files")
     * @Method({"GET"})
     *
     * @return Response
     */
    public function listAction(): Response
    {
        $userFiles = $this->getDoctrine()
            ->getRepository(UserFile::class)
            ->findByUser($this->getUser());

        return $this->render('professional/user-file/list.html.twig', [
            'userFiles' => $userFiles
        ]);
    }

    /**
     * @Route("/user-files/{userFile}/download", name="professional_user_file_download", requirements={"userFile": "\d+"})
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

        if ($userFile->getUser() !== $this->getUser()) {
            throw $this->createNotFoundException('File Not Found.');
        }

        $filePath = sprintf(
            '%s/../uploads/user-files/%s/%s',
            $this->get('kernel')->getRootDir(),
            $this->getUser()->getId(),
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
