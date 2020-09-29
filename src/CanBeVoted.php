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

trait CanBeVoted
{
    /**
     * Check if user is voted by given user.
     *
     * @param $user
     *
     * @return bool
     */
    public function isVotedBy($user)
    {
        return $this->voters->contains($user);
    }

    /**
     * Return the total vote score
     *
     * @return int
     */
    public function countTotalVotes()
    {
        return $this->countVotesSum();
    }

    /**
     * Count the number of up votes.
     *
     * @return int
     */
    public function countUpVoters()
    {
        return $this->countVoters(1);
    }

    /**
     * Count the number of down votes.
     *
     * @return int
     */
    public function countDownVoters()
    {
        return $this->countVoters(-1);
    }

    /**
     * Count the number of voters.
     *
     * @param  string $type
     *
     * @return int
     */
    public function countVoters($type = null)
    {
        $voters = $this->voters();

        if(!is_null($type)) $voters->wherePivot('type', $type);

        return $voters->count();
    }

    /**
     * Count the number of voters.
     *
     * @param  string $type
     *
     * @return int
     */
    public function countVotesSum($type = null)
    {
        return (int) DB::table($this->getVoteTable())
            ->select(DB::raw('SUM(type) as votes_sum'))
            ->where('votable_id', $this->id)
            ->where('votable_type', get_class($this))
            ->first()->votes_sum;
    }

    /**
     * Return voters.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function voters()
    {
        $property = property_exists($this, 'vote') ? $this->vote : __CLASS__;

        return $this->morphToMany($property, 'votable', $this->vote_table ?: 'votes');
    }

    protected function getVoteTable()
    {
        return $this->vote_table ?: 'votes';
    }
}
