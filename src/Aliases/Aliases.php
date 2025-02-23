<?php

namespace HostMyServers\Aliases;

use HostMyServers\MailCowAPI;

class Aliases
{
    /**
     * @var MailCowAPI
     */
    private $MailCowAPI;

    /**
     * Default values for aliases
     */
    private const DEFAULT_VALUES = [
        'active' => '1'
    ];

    public function __construct(MailCowAPI $MailCowAPI)
    {
        $this->MailCowAPI = $MailCowAPI;
    }

    /**
     * Get all aliases
     * 
     * @return array|string
     */
    public function getAll()
    {
        return $this->MailCowAPI->get('get/alias/all');
    }

    /**
     * Get a specific alias
     * 
     * @param string $aliasId
     * @return array|string
     */
    public function get(string $aliasId)
    {
        return $this->MailCowAPI->get('get/alias/' . $aliasId);
    }

    /**
     * Create a new alias
     * 
     * @param string $address Alias address
     * @param string $destination Destination address
     * @param array $options Additional options
     * @return array|string
     */
    public function create(string $address, string $destination, array $options = [])
    {
        $payload = array_merge([
            'address' => $address,
            'goto' => $destination
        ], self::DEFAULT_VALUES, $options);

        return $this->MailCowAPI->post('add/alias', $payload);
    }

    /**
     * Update an existing alias
     * 
     * @param string $aliasId Alias ID
     * @param array $attributes Attributes to update
     * @return array|string
     */
    public function update(string $aliasId, array $attributes)
    {
        $payload = [
            'items' => [$aliasId],
            'attr' => array_merge(self::DEFAULT_VALUES, $attributes)
        ];

        return $this->MailCowAPI->post('edit/alias', $payload);
    }

    /**
     * Update alias address and destination
     * 
     * @param string $aliasId Alias ID
     * @param string $address New address
     * @param string $destination New destination
     * @param string|null $privateComment Private comment
     * @param string|null $publicComment Public comment
     * @return array|string
     */
    public function updateAddressAndDestination(
        string $aliasId,
        string $address,
        string $destination,
        ?string $privateComment = null,
        ?string $publicComment = null
    ) {
        return $this->update($aliasId, [
            'address' => $address,
            'goto' => $destination,
            'private_comment' => $privateComment,
            'public_comment' => $publicComment
        ]);
    }

    /**
     * Delete one or multiple aliases
     * 
     * @param string|array $aliasIds Single alias ID or array of alias IDs
     * @return array|string
     */
    public function delete($aliasIds)
    {
        $aliasIds = is_array($aliasIds) ? $aliasIds : [$aliasIds];
        return $this->MailCowAPI->post('delete/alias', $aliasIds);
    }

    /**
     * Enable or disable an alias
     * 
     * @param string $aliasId Alias ID
     * @param bool $active Status to set
     * @return array|string
     */
    public function setActive(string $aliasId, bool $active)
    {
        return $this->update($aliasId, [
            'active' => $active ? '1' : '0'
        ]);
    }
}
