<?php

declare(strict_types=1);

/*
 * Copyright Humbly Arrogant Ltd 2020-2022, all rights reserved.
 */

namespace Parthenon\RuleEngine\Entity;

class RuleExecutionLog
{
    protected $id;
    protected $entityName;
    protected $fieldName;
    protected $entityId;
    protected array $value;
    protected $createdAt;

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getEntityName()
    {
        return $this->entityName;
    }

    /**
     * @param mixed $entityName
     */
    public function setEntityName($entityName): self
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFieldName()
    {
        return $this->fieldName;
    }

    /**
     * @param mixed $fieldName
     */
    public function setFieldName($fieldName): self
    {
        $this->fieldName = $fieldName;

        return $this;
    }

    public function getValue()
    {
        if (isset($this->value['data'])) {
            return $this->value['data'];
        }

        return null;
    }

    public function setValue($value): self
    {
        $this->value = ['data' => $value];

        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param mixed $createdAt
     */
    public function setCreatedAt($createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * @param mixed $entityId
     */
    public function setEntityId($entityId): self
    {
        $this->entityId = $entityId;

        return $this;
    }
}
