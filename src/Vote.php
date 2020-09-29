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

use Illuminate\Support\Facades\DB;

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
        $vote = $this->getVoteItem($item);

        if($vote) {
            if($vote->type != $type) {
                return DB::table($this->getVoteTable())
                    ->where('user_id', $this->id)
                    ->where('votable_id', $item->id)
                    ->where('votable_type', get_class($item))
                    ->update(['type' => $type, 'updated_at' => now()]);
            }
        } else {
            return DB::table($this->getVoteTable())
                ->insert([
                    'user_id' => $this->id,
                    'votable_id' => $item->id,
                    'votable_type' => get_class($item),
                    'type' => $type,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
        }
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
        $vote = $this->getVoteItem($item);

        if($vote) {
            return DB::table($this->getVoteTable())
                ->where('user_id', $vote->user_id)
                ->where('votable_id', $vote->votable_id)
                ->where('votable_type', $vote->votable_type)
                ->delete();
        }

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
        $vote = $this->getVoteItem($item);

        if($vote) {
            return $type ? $vote->type === $type : true;
        }

        return false;
    }

    /**
     * getVoteItem
     *
     * @param mixed $item
     * @access public
     * @return mixed
     */
    public function getVoteItem($item)
    {
        return DB::table($this->getVoteTable())
            ->where('user_id', $this->id)
            ->where('votable_id', $item->id)
            ->where('votable_type', get_class($item))
            ->limit(1)
            ->first();
    }

    public function getVote($item)
    {
        $vote = $this->getVoteItem($item);

        return $vote ? $vote->type : null;
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

        return $this->morphedByMany($this->voteRelation, 'votable', $this->getVoteTable())->withTimestamps();
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

    protected function getVoteTable()
    {
        return $this->vote_table ?: 'votes';
    }
}
