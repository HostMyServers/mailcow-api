<?php

namespace HostMyServers\Domains;

use HostMyServers\MailCowAPI;

class Domains
{
    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    /**
     * Default values for domain creation
     */
    private const DEFAULT_VALUES = [
        'active' => '1',
        'rl_value' => '10',
        'rl_frame' => 's',
        'backupmx' => '0',
        'relay_all_recipients' => '0',
        'restart_sogo' => '1'
    ];

    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }

    /**
     * Get all domains
     * 
     * @return array|string
     */
    public function getAll()
    {
        return $this->MailCowAPI->get('get/domain/all');
    }

    /**
     * Get a specific domain
     * 
     * @param string $domain
     * @return array|string
     */
    public function get(string $domain)
    {
        return $this->MailCowAPI->get('get/domain/' . $domain);
    }

    /**
     * Create a new domain
     * 
     * @param string $domain Domain name
     * @param string $description Domain description
     * @param int $aliases Number of allowed aliases
     * @param int $mailboxes Number of allowed mailboxes
     * @param int $defquota Default quota (in MB)
     * @param int $maxquota Maximum quota (in MB)
     * @param int $quota Total domain quota (in MB)
     * @param array $options Additional options
     * @return array|string
     */
    public function create(
        string $domain,
        string $description,
        int $aliases = 0,
        int $mailboxes = 0,
        int $defquota = 1000,
        int $maxquota = 1000,
        int $quota = 1000,
        array $options = []
    ) {
        $payload = array_merge([
            'domain' => $domain,
            'description' => $description,
            'aliases' => $aliases,
            'mailboxes' => $mailboxes,
            'defquota' => $defquota,
            'maxquota' => $maxquota,
            'quota' => $quota
        ], self::DEFAULT_VALUES, $options);

        return $this->MailCowAPI->post('add/domain', $payload);
    }

    /**
     * Update an existing domain
     * 
     * @param string $domain Domain name
     * @param array $attributes Attributes to update
     * @return array|string
     */
    public function update(string $domain, array $attributes)
    {
        $payload = [
            'items' => ['domain' => $domain],
            'attr' => array_merge([
                'active' => '1',
                'rl_value' => '10',
                'rl_frame' => 's',
                'backupmx' => '0',
                'relay_all_recipients' => '0',
                'restart_sogo' => '1'
            ], $attributes)
        ];

        return $this->MailCowAPI->post('edit/domain', $payload);
    }

    /**
     * Delete one or multiple domains
     * 
     * @param string|array $domains Single domain or array of domains
     * @return array|string
     */
    public function delete($domains)
    {
        $domains = is_array($domains) ? $domains : [$domains];
        return $this->MailCowAPI->post('delete/domain', $domains);
    }

    /**
     * Enable or disable a domain
     * 
     * @param string $domain Domain name
     * @param bool $active Status to set
     * @return array|string
     */
    public function setActive(string $domain, bool $active)
    {
        return $this->update($domain, [
            'active' => $active ? '1' : '0'
        ]);
    }
}
