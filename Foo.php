<?php

/**
 * This file is auto-generated.
 */

namespace Skeleton\Domain\Entity;

/**
 * Foo entity
 *
 * @Entity
 * @Table(name="foo")
 *
 * @package Skeleton\Domain\Entity
 */
class Foo
{
    /**
     * Foo ID
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     * @var int $id
     */
    private $id;

    /**
     * Foo bar
     *
     * @Column(type="string", length=255)
     * @var string $bar
     */
    private $bar;

    /**
     * Foo baz
     *
     * @Column(type="datetime")
     * @var \DateTime $baz
     */
    private $baz;


    /**
     * Returns foo ID
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


    /**
     * Returns foo bar
     *
     * @return string
     */
    public function getBar(): string
    {
        return $this->bar;
    }


    /**
     * Sets foo bar
     *
     * @param string $bar
     * @return Foo
     */
    public function setBar(string $bar): Foo
    {
        $this->bar = $bar;
        return $this;
    }


    /**
     * Returns foo baz
     *
     * @return \DateTime
     */
    public function getBaz(): \DateTime
    {
        return $this->baz;
    }


    /**
     * Sets foo baz
     *
     * @param \DateTime $baz
     * @return Foo
     */
    public function setBaz(\DateTime $baz): Foo
    {
        $this->baz = $baz;
        return $this;
    }
}
