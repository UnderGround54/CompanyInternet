<?php


namespace App\Service;

use Symfony\Component\Serializer\SerializerInterface;

class SerializerService
{
    public function __construct(
        private readonly SerializerInterface $serializer
    ){}

    /**
     * serilized data
     * @param $data
     * @param string $group
     * @return array
     */
    public function serializeData($data, string $group): array
    {
        $jsonData = $this->serializer->serialize($data, 'json', ['groups' => $group]);

        return json_decode($jsonData, true);
    }

    /**
     * @param $data
     * @param string $type
     * @param string|null $group
     * @return mixed|object|string
     */
    public function deserializeData($data, string $type, string $group = null): mixed
    {
        return $this->serializer->deserialize($data, $type, 'json', ['groups' => $group]);
    }
}
