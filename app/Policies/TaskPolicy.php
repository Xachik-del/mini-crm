<?php

namespace App\Policies;

use App\Models\Task;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaskPolicy
{
    use HandlesAuthorization;


    public function viewAny(User $user)
    {
        //
    }


    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }


    public function create(User $user)
    {
        //
    }


    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }


    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }


    public function restore(User $user, Task $task)
    {
        //
    }


    public function forceDelete(User $user, Task $task)
    {
        //
    }
}
