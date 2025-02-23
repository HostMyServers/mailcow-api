<?php

namespace HostMyServers\MailBoxes;

use HostMyServers\MailCowAPI;

class MailBoxes
{
    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    /**
     * Default values for mailbox creation
     */
    private const DEFAULT_VALUES = [
        'active' => '1',
        'force_pw_update' => '1',
        'quota' => '10240',
        'tls_enforce_in' => '1',
        'tls_enforce_out' => '1',
        'sogo_access' => '1'
    ];

    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }

    /**
     * Get all mailboxes
     * 
     * @return array|string
     */
    public function getAll()
    {
        return $this->MailCowAPI->get('get/mailbox/all');
    }

    /**
     * Get a specific mailbox
     * 
     * @param string $mailbox
     * @return array|string
     */
    public function get(string $mailbox)
    {
        return $this->MailCowAPI->get('get/mailbox/' . $mailbox);
    }

    /**
     * Create a new mailbox
     * 
     * @param string $localPart Local part of address (before @)
     * @param string $domain Domain name
     * @param string $fullName User's full name
     * @param string $password Password
     * @param array $options Additional options
     * @return array|string
     */
    public function create(
        string $localPart,
        string $domain,
        string $fullName,
        string $password,
        array $options = []
    ) {
        $payload = array_merge([
            'local_part' => $localPart,
            'domain' => $domain,
            'name' => $fullName,
            'password' => $password,
            'password2' => $password
        ], self::DEFAULT_VALUES, $options);

        return $this->MailCowAPI->post('add/mailbox', $payload);
    }

    /**
     * Update an existing mailbox
     * 
     * @param string $mailAddress Complete email address
     * @param array $attributes Attributes to update
     * @return array|string
     */
    public function update(string $mailAddress, array $attributes)
    {
        $payload = [
            'items' => [$mailAddress],
            'attr' => array_merge([
                'sender_acl' => ['default'],
                'sogo_access' => '1'
            ], $attributes)
        ];

        return $this->MailCowAPI->post('edit/mailbox', $payload);
    }

    /**
     * Update mailbox spam score
     * 
     * @param string $email Email address
     * @param string $score New spam score
     * @return array|string
     */
    public function updateSpamScore(string $email, string $score)
    {
        return $this->MailCowAPI->post('edit/spam-score', [
            'items' => [$email],
            'attr' => ['spam_score' => $score]
        ]);
    }

    /**
     * Delete one or multiple mailboxes
     * 
     * @param string|array $mailboxes Single mailbox or array of mailboxes
     * @return array|string
     */
    public function delete($mailboxes)
    {
        $mailboxes = is_array($mailboxes) ? $mailboxes : [$mailboxes];
        return $this->MailCowAPI->post('delete/mailbox', $mailboxes);
    }

    /**
     * Enable or disable a mailbox
     * 
     * @param string $mailAddress Email address
     * @param bool $active Status to set
     * @return array|string
     */
    public function setActive(string $mailAddress, bool $active)
    {
        return $this->update($mailAddress, [
            'active' => $active ? '1' : '0'
        ]);
    }

    //TODO ADD MAILBOXES ACL
    //TODO ADD MAILBOXES PUSHOVER

    //TODO ADD Quarantine Notifications

}
