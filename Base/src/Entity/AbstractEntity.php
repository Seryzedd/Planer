<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Abstract User
 */
#[ORM\MappedSuperclass]
abstract class AbstractEntity
{
    /**
     * Id
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', length: 255)]
    protected int $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }
}