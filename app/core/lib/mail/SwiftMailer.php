<?php

// SwiftMailer caller in LiF
// See: <https://swiftmailer.symfony.com/docs/introduction.html>

namespace Lif\Core\Lib\Mail;

class SwiftMailer implements \Lif\Core\Intf\Mailer
{
    // When $params['to'] is greater than 1
    // Return false not mean all mail-sending events were failed
    public function send(array $config, array $params) : bool
    {
        if (!class_exists('Swift_SmtpTransport')
            || !class_exists('Swift_Mailer')
            || !class_exists('Swift_Message')
        ) {
            excp(
                'Install SwiftMailer first please.'
            );
        }

        $transport = (
            new \Swift_SmtpTransport(
                $config['host'],
                $config['port'],
                $config['encryption']
            )
        )
        ->setUsername($config['account'])
        ->setPassword($config['credential'])
        ;

        $mailer = new \Swift_Mailer($transport);

        $message = (new \Swift_Message($params['title']))
        ->setFrom([$config['sender_email'] => $config['sender_name']])
        ->setTo($params['to'])
        ->setBody($params['body'], ($params['mime'] ?? 'text/html'))
        ;

        $count = count($params['to']);

        return ($mailer->send($message) == $count)
        ? true
        : false
        ;
    }
}
