<?php
/**
 * Created by PhpStorm.
 * User: RobertLee
 * Date: 2020/4/22
 * Time: 17:32:28.
 */

namespace App\Entity;

class ChessEntity
{
    /**
     * @var bool
     */
    private $alive;

    private $next = false;

    private $neighbor = 0;
    /**
     * @var array
     */
    private $location;

    public function __construct(array $location, bool $alive)
    {
        $this->location = $location;
        $this->alive = $alive;
    }

    public function __toString()
    {
        if ($this->alive) {
//            return (string) $this->neighbor;
            return '■';
        } else {
            return '　';
//            return '□';
        }
    }


    public function isAlive(): bool
    {
        return $this->alive;
    }

    public function getLocation(): array
    {
        return $this->location;
    }

    public function setNext(bool $next): void
    {
        $this->next = $next;
    }

    public function toNext()
    {
        $this->alive = $this->next;
        $this->next = false;
    }

    public function setNeighbor(int $neighbor): void
    {
        $this->neighbor = $neighbor;
    }

    public function getNeighbor(): int
    {
        return $this->neighbor;
    }
}
