<?php

namespace App\Policies;

use App\Models\Proposal;
use App\Models\Tender;
use App\Models\User;

class ProposalPolicy
{
    /**
     * Создание предложения поставщиком для конкретного тендера.
     */
    public function create(User $user, Tender $tender): bool
    {
        if (! $user->isSupplier()) {
            return false;
        }

        if ($tender->is_finished) {
            return false;
        }

        return ! $tender->valid_until->isPast();
    }

    /**
     * Поставщик может редактировать только своё предложение,
     * только пока тендер открыт и предложение в статусе draft.
     */
    public function update(User $user, Proposal $proposal): bool
    {
        if (! $user->isSupplier()) {
            return false;
        }

        if ($proposal->user_id !== $user->id) {
            return false;
        }

        $tender = $proposal->tender;

        if ($tender === null) {
            return false;
        }

        if ($tender->is_finished || $tender->valid_until->isPast()) {
            return false;
        }

        return in_array($proposal->status, ['draft', 'withdrawn'], true);
    }

    /**
     * Заказчик может принять предложение по своему тендеру, пока тендер не завершен.
     */
    public function approve(User $user, Proposal $proposal): bool
    {
        $tender = $proposal->tender;
        if ($tender === null) {
            return false;
        }
        if ($tender->is_finished) {
            return false;
        }
        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->id === $tender->customer_id;
    }

    /**
     * Заказчик может отклонить предложение по своему тендеру, пока тендер не завершен.
     */
    public function reject(User $user, Proposal $proposal): bool
    {
        return $this->approve($user, $proposal);
    }

    /**
     * Поставщик может отозвать только своё отправленное предложение,
     * пока тендер открыт и срок не истёк.
     */
    public function withdraw(User $user, Proposal $proposal): bool
    {
        if (! $user->isSupplier()) {
            return false;
        }

        if ($proposal->user_id !== $user->id) {
            return false;
        }

        $tender = $proposal->tender;
        if ($tender === null) {
            return false;
        }

        if ($tender->is_finished || $tender->valid_until->isPast()) {
            return false;
        }

        return $proposal->status === 'submitted';
    }

    /**
     * Поставщик может удалить свой черновик предложения, пока тендер открыт и срок не истёк.
     */
    public function discard(User $user, Proposal $proposal): bool
    {
        if (! $user->isSupplier()) {
            return false;
        }

        if ($proposal->user_id !== $user->id) {
            return false;
        }

        $tender = $proposal->tender;
        if ($tender === null) {
            return false;
        }

        if ($tender->is_finished || $tender->valid_until->isPast()) {
            return false;
        }

        return $proposal->status === 'draft';
    }
}
