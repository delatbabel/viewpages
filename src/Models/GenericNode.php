<?php

namespace Delatbabel\ViewPages\Models;

use Baum\Node;

/**
 * ProductCall Model
 */
class GenericNode extends Node
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($node) {

            // Baum triggers a parent move, which puts the item last in the list,
            // even if the old and new parents are the same
            if ($node->isParentIdSame()) {
                $node->stopBaumParentMove();
            }
        });
    }

    /**
     * Check for dirty parent ID.
     *
     * Returns true if the parent_id value in the database is different to the current
     * value in the model's dirty attributes
     *
     * @return bool
     */
    protected function isParentIdSame()
    {
        $dirty             = $this->getDirty();
        $oldNavItem        = self::where('id', '=', $this->id)->first();
        $oldParent         = $oldNavItem->parent;
        $oldParentId       = empty($oldParent) ? null : $oldParent->id;
        $isParentColumnSet = isset($dirty[$this->getParentColumnName()]);
        if ($isParentColumnSet) {
            $isNewParentSameAsOld = ($dirty[$this->getParentColumnName()] == $oldParentId);
        } else {
            $isNewParentSameAsOld = false;
        }
        return $isParentColumnSet && $isNewParentSameAsOld;
    }

    /**
     * Reset parent ID.
     *
     * Removes the parent_id field from the model's attributes and sets $moveToNewParentId
     * static property on the parent Baum\Node model class to false to prevent Baum from
     * triggering a move. This can be required because Baum triggers a parent move, which
     * puts the item last in the list, even if the old and new parents are the same.
     *
     * @return void
     */
    protected function stopBaumParentMove()
    {
        unset($this->{$this->getParentColumnName()});
        static::$moveToNewParentId = false;
    }
}
