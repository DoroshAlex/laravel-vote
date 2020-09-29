<?php

/*
 * This file is part of the jcc/laravel-vote.
 *
 * (c) jcc <changejian@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace DoroshAlex\LaravelVote;

trait Vote
{
    protected $voteRelation = __CLASS__;

    /**
     * Up vote a item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $item
     *
     * @return boolean
     */
    public function upVote($item)
    {
        $this->cancelVote($item);

        return $this->vote($item);
    }

    /**
     * Down vote a item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $item
     *
     * @return boolean
     */
    public function downVote($item)
    {
        $this->cancelVote($item);

        return $this->vote($item, -1);
    }

    /**
     * Vote a item or items.
     *
     * @param  int|array|\Illuminate\Database\Eloquent\Model $item
     * @param  string $type
     * @return boolean
     */
    public function vote($item, $type = 1)
    {
        $items = array_fill_keys((array) $this->checkVoteItem($item), ['type' => $type]);

        return $this->votedItems()->sync($items, false);
    }

    /**
     * Cancel vote a item or items.
     *
     * @param int|array|\Illuminate\Database\Eloquent\Model $item
     *
     * @return int
     */
    public function cancelVote($item)
    {
        $item = $this->checkVoteItem($item);

        return $this->votedItems()->detach((array)$item);
    }

    /**
     * Determine whether the type of 1 item exist
     *
     * @param $item
     *
     * @return boolean
     */
    public function hasUpVoted($item)
    {
        return $this->hasVoted($item, 1);
    }

    /**
     * Determine whether the type of -1 item exist
     *
     * @param $item
     *
     * @return boolean
     */
    public function hasDownVoted($item)
    {
        return $this->hasVoted($item, -1);
    }

    /**
     * Check if user has voted item.
     *
     * @param $item
     * @param string $type
     *
     * @return bool
     */
    public function hasVoted($item, $type = null)
    {
        $item = $this->checkVoteItem($item);

        $votedItems = $this->votedItems();

        if(!is_null($type)) $votedItems->wherePivot('type', $type);

        return $votedItems->get()->contains($item);
    }

    /**
     * Return the user what has items.
     *
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function votedItems($class = null)
    {
        if (!empty($class)) {
            $this->setVoteRelation($class);
        }

        return $this->morphedByMany($this->voteRelation, 'votable', $this->vote_table ?: 'votes')->withTimestamps();
    }

    /**
     * Determine whether $item is an instantiated object of \Illuminate\Database\Eloquent\Model
     *
     * @param $item
     *
     * @return int
     */
    protected function checkVoteItem($item)
    {
        if ($item instanceof \Illuminate\Database\Eloquent\Model) {
            $this->setVoteRelation(get_class($item));
            return $item->id;
        };

        return $item;
    }

    /**
     * Set the vote relation class.
     *
     * @param string $class
     */
    protected function setVoteRelation($class)
    {
        return $this->voteRelation = $class;
    }
}
