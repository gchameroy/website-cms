<?php

namespace AppBundle\Service;

use AppBundle\Entity\User;
use Swift_Mailer;
use Swift_Message;
use Twig_Environment;

class EmailProvider
{
    /** @var array */
    private $config;

    /** @var Swift_Mailer*/
    private $mailer;

    /** @var Twig_Environment */
    private $twig;

    public function __construct(array $config ,Swift_Mailer $mailer, Twig_Environment $twig)
    {
        $this->config = $config;
        $this->mailer = $mailer;
        $this->twig = $twig;
    }

    public function sendAdminNewOrder(): void
    {
        $message = (new Swift_Message($this->config['company']['name'] . ' - Nouvelle commande'))
            ->setFrom($this->config['company']['email'])
            ->setTo($this->config['company']['email'])
            ->setBody(
                $this->twig->render('admin/emails/new-order.html.twig'),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function sendClientOrderConfirmation(User $user): void
    {
        $message = (new Swift_Message($this->config['company']['name'] . ' - Confirmation de commande'))
            ->setFrom($this->config['company']['email'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('front/emails/confirm-command.html.twig'),
                'text/html'
            );
        $this->mailer->send($message);
    }

    public function sendClientOrderStatus(User $user): void
    {
        $message = (new Swift_Message($this->config['company']['name'] . ' - Confirmation envoie de commande'))
            ->setFrom($this->config['company']['email'])
            ->setTo($user->getEmail())
            ->setBody(
                $this->twig->render('front/emails/confirm-send.html.twig'),
                'text/html'
            );
        $this->mailer->send($message);
    }
}