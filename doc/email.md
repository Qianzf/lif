LiF use [SwiftMailer](https://github.com/swiftmailer/swiftmailer) as default email sender.

- First config a SMTP server: _app/conf/mail.php_

``` php
return [
    'default' => 'swiftmailer',

    'senders' => [
        'swiftmailer' => [
            'driver'  => 'swift-mailer',    // static
            'host'    => 'smtp.example.com',
            'port'    => 465,
            'account' => 'user@example.com',
            'credential'   => 'access_credential',
            'sender_name'  => 'no-reply',
            'sender_email' => 'user@example.com',
            'encryption'   => 'ssl',
        ],
    ],
];

```

- Then use `email()` to send an email.

``` php
email([
    'to'    => [
        'user1@example.com' => 'dispaly_name1',
        'user2@example.com' => 'dispaly_name2',
        'user2@example.com',
        ],
    'title' => 'Email Subject',
    'body'  => 'Email context',
]);
```

If all emails sending success, `email()` will return `true`, otherwise `false`;

To increase success rate, it's recommended that using backend queue to send email one by one.
