<?php

namespace Vexura\Dkim;

use Vexura\MailCowAPI;

class Dkim
{

    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }


    /**
     * @return array|string
     */
    public function getDkim(string $domain)
    {
        return $this->MailCowAPI->get('get/dkim/' . $domain);
    }

    /**
     * @return array|string
     */
    public function generateDkim(string $domain, string $dkim_selector, int $key_size = 1024)
    {
        return $this->MailCowAPI->post('add/dkim', [
            "domains" => $domain,
            "dkim_selector" => $dkim_selector,
            "key_size" => $key_size
        ]);
    }

    /**
     * @return array|string
     */
    public function deleteDkim(string $domain)
    {
        return $this->MailCowAPI->post('delete/dkim', $domain);
    }

}
