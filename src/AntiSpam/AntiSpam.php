<?php

namespace Vexura\AntiSpam;

use Vexura\MailCowAPI;

class AntiSpam
{
    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    /**
     * @param MailCowAPI $MailCowAPI
     */
    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }

    /**
     * Get whitelist policy for a domain
     * 
     * @param string $domain
     * @return array|string
     */
    public function getWhitelistPolicy(string $domain)
    {
        return $this->MailCowAPI->get('get/policy_wl_domain/' . $domain);
    }

    /**
     * Get blacklist policy for a domain
     * 
     * @param string $domain
     * @return array|string
     */
    public function getBlacklistPolicy(string $domain)
    {
        return $this->MailCowAPI->get('get/policy_bl_domain/' . $domain);
    }

    /**
     * Add a new policy
     * 
     * @param string $domain Domain name
     * @param string $objectList List type (whitelist/blacklist)
     * @param string $objectFrom Source email or domain
     * @return array|string
     */
    public function addPolicy(string $domain, string $objectList, string $objectFrom)
    {
        return $this->MailCowAPI->post('add/domain-policy', [
            'domain' => $domain,
            'object_list' => $objectList,
            'object_from' => $objectFrom
        ]);
    }

    /**
     * Delete one or multiple policies
     * 
     * @param array $policyIds List of policy IDs to delete
     * @return array|string
     */
    public function deletePolicy(array $policyIds)
    {
        return $this->MailCowAPI->post('delete/domain-policy', $policyIds);
    }
}
