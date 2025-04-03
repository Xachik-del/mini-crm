<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = Task::query()->where('user_id', $user->id);

        if ($request->has('status')) {
            $query->where('status', $request->query('status'));
        }

        $tasks = $query->orderBy('created_at', 'desc')->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse
     */
    public function store(TaskStoreRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $data['user_id'] = $user->id;

        if (!isset($data['status'])) {
            $data['status'] = 'new';
        }

        $task = Task::query()->create($data);

        return response()->json($task, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function show(Request $request, Task $task): JsonResponse
    {

        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Задача не найдена'], 404);
        }
        return response()->json($task);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param TaskUpdateRequest $request
     * @param Task $task
     * @return JsonResponse
     */
    public function update(TaskUpdateRequest $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $data = $request->validated();

        unset($data['user_id']);
        unset($data['id']);

        $task->update($data);

        return response()->json($task);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param Task $task
     * @return JsonResponse
     */
    public function destroy(Request $request, Task $task): JsonResponse
    {
        if ($task->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $task->delete();
        return response()->json(null, 204);
    }

}
