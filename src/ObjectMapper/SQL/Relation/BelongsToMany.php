<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class BelongsToMany extends RelationAbstract
{
    /**
     * @param ActiveRecordInterface        $model
     * @param string|ActiveRecordInterface $target
     * @param array                        $config
     */
    public function __construct(ActiveRecordInterface $model, $target, array $config = [])
    {
        $this->type = self::BELONGS_TO_MANY;
        $this->model = $model;
        $this->target = $target;

        $config += [
            'foreign_key' => null,
            'local_key' => null,
            'pivot' => null,
            'criterias' => [],
        ];

        $this->foreignKey = $config['foreign_key'];
        $this->localKey = $config['local_key'];
        $this->pivot = null !== $config['pivot'] && false !== $config['pivot'] ? $config['pivot'] : true;

        foreach ($config['criterias'] as $criteria) {
            $this->addCriteria($criteria);
        }
    }

    /**
     * @param ActiveRecordInterface $target
     *
     * @return bool
     */
    public function associate(ActiveRecordInterface $target)
    {
        $result = $this->pivot->attach(
            $target->getConnectionManager(),
            $target->get($this->foreignKey),
            $this->model->get($this->localKey)
        );

        if (null !== $this->related) {
            if (!in_array($target, $this->related, true)) {
                $this->related[] = $target;
            }
        } else {
            $this->setRelated([$target], ['filled' => true]);
        }

        return $result;
    }

    /**
     * @param ActiveRecordInterface $target
     *
     * @return bool
     */
    public function dissociate(ActiveRecordInterface $target)
    {
        $result = $this->pivot->detach(
            $target->getConnectionManager(),
            $target->get($this->foreignKey),
            $this->model->get($this->localKey)
        );

        if (null !== $this->related) {
            $pos = array_search($target, $this->related);

            if (false !== $pos) {
                array_splice($this->related, $pos, 1);
            }
        }

        return $result;
    }
}
