<?php

namespace Elixir\DB\ObjectMapper\SQL\Relation;

use Elixir\DB\ObjectMapper\ActiveRecordInterface;

/**
 * @author Cédric Tanghe <ced.tanghe@gmail.com>
 */
class HasOne extends RelationAbstract
{
    /**
     * @param ActiveRecordInterface        $model
     * @param string|ActiveRecordInterface $target
     * @param array                        $config
     */
    public function __construct(ActiveRecordInterface $model, $target, array $config = [])
    {
        $this->type = self::HAS_ONE;
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

        if (false !== $config['pivot']) {
            $this->pivot = $config['pivot'];
        }

        foreach ($config['criterias'] as $criteria) {
            $this->addCriteria($criteria);
        }
    }

    /**
     * @param ActiveRecordInterface $target
     *
     * @return bool
     */
    public function attach(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot) {
            $result = $this->pivot->attach(
                $this->model->getConnectionManager(),
                $this->model->get($this->localKey),
                $target->get($this->foreignKey)
            );
        } else {
            $target->set($this->foreignKey, $this->localKey);
            $result = $target->save();
        }

        $this->setRelated($target, ['filled' => true]);

        return $result;
    }

    /**
     * @param ActiveRecordInterface $target
     *
     * @return bool
     */
    public function detach(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot) {
            $result = $this->pivot->detach(
                $this->model->getConnectionManager(),
                $this->model->get($this->localKey),
                $target->get($this->foreignKey)
            );
        } else {
            $target->set($this->foreignKey, $target->getIgnoreValue());
            $result = $target->save();
        }

        $this->related = null;

        return $result;
    }

    /**
     * @param ActiveRecordInterface $target
     *
     * @return bool
     */
    public function detachAndDelete(ActiveRecordInterface $target)
    {
        if (null !== $this->pivot) {
            $result = $this->pivot->detach(
                $this->model->getConnectionManager(),
                $this->model->get($this->localKey),
                $target->get($this->foreignKey)
            );

            if ($result) {
                $result = $target->delete();
            }
        } else {
            $target->set($this->foreignKey, $target->getIgnoreValue());
            $result = $target->delete();
        }

        $this->related = null;

        return $result;
    }
}
