<?php

namespace Vexura\Dkim;

use Vexura\MailCowAPI;

class Dkim
{
    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    /**
     * Default key size for DKIM
     */
    private const DEFAULT_KEY_SIZE = 1024;

    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }

    /**
     * Get DKIM information for a domain
     * 
     * @param string $domain
     * @return array|string
     */
    public function getDkim(string $domain)
    {
        return $this->MailCowAPI->get('get/dkim/' . $domain);
    }

    /**
     * Generate a new DKIM signature for a domain
     * 
     * @param string $domain Domain name
     * @param string $dkimSelector DKIM selector
     * @param int $keySize Key size (1024/2048)
     * @return array|string
     */
    public function generate(string $domain, string $dkimSelector, int $keySize = self::DEFAULT_KEY_SIZE)
    {
        return $this->MailCowAPI->post('add/dkim', [
            'domains' => $domain,
            'dkim_selector' => $dkimSelector,
            'key_size' => $keySize
        ]);
    }

    /**
     * Delete DKIM signature for a domain
     * 
     * @param string $domain
     * @return array|string
     */
    public function delete(string $domain)
    {
        return $this->MailCowAPI->post('delete/dkim', $domain);
    }
}
