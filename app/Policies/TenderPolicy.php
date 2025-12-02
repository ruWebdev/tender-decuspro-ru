<?php

namespace App\Policies;

use App\Models\Tender;
use App\Models\User;

class TenderPolicy
{
    /**
     * Проверка права на создание тендера.
     */
    public function create(User $user): bool
    {
        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->isCustomer();
    }

    /**
     * Проверка права на сохранение тендера.
     */
    public function store(User $user): bool
    {
        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->isCustomer();
    }

    /**
     * Проверка права на обновление тендера.
     */
    public function update(User $user, Tender $tender): bool
    {
        if ($tender->is_finished) {
            return false;
        }

        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->id === $tender->customer_id;
    }

    /**
     * Проверка права на завершение тендера и выбор победителя.
     */
    public function finish(User $user, Tender $tender): bool
    {
        if ($tender->is_finished) {
            return false;
        }

        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->id === $tender->customer_id;
    }

    /**
     * Проверка права на просмотр тендера.
     */
    public function view(User $user, Tender $tender): bool
    {
        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->id === $tender->customer_id;
    }

    /**
     * Поставщик может участвовать в тендере, если срок не истёк.
     */
    public function participate(User $user, Tender $tender): bool
    {
        return $user->isSupplier();
    }

    /**
     * Заказчик может просматривать предложения только по своим тендерам.
     */
    public function viewProposals(User $user, Tender $tender): bool
    {
        if ($user->isAdmin() || $user->isModerator()) {
            return true;
        }

        return $user->id === $tender->customer_id;
    }
}
