<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task; // Подключаем модель Task

class TaskController extends Controller
{
    public function createTask(Request $request)
    {
        // Валидация входных данных
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Создание задачи для авторизированного пользователя
        $task = auth()->user()->tasks()->create([
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'completed' => false, // По умолчанию задача не завершена
        ]);

        // Возвращаем созданную задачу в формате JSON
        return response()->json([
            'message' => 'Задача успешно создана.',
            'task' => $task,
        ], 201);
    }
    
    public function getUserTasks()
    {
        // Получаем авторизированного пользователя
        $user = auth()->user();

        // Получаем все задачи пользователя
        $tasks = $user->tasks;

        // Возвращаем задачи в формате JSON
        return response()->json([
            'message' => 'Список задач пользователя.',
            'tasks' => $tasks,
        ], 200);
    }

}
