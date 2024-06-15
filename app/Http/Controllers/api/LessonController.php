<?php

namespace App\Http\Controllers\Api;

use App\Models\Lesson;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class LessonController extends Controller
{   
    //функция проверкти роли
    private function checkRole()
    {
        if (auth()->user()->role !== 'teacher') {
            abort(403, 'Доступ запрещен');
        }
    }


    public function index(Request $request)
    {
    // Создаем запрос к базе данных
    $query = Lesson::query();

    // Проверяем, есть ли фильтр по преподавателю
    if ($request->has('teacher')) {
        $query->where('teacher', $request->input('teacher'));
    }

    // Проверяем, есть ли фильтр по дате
    if ($request->has('date')) {
        $query->where('date', $request->input('date'));
    }
    if ($request->has('date')) {
        $query->where('date', $request->input('date'));
    }
    $query->orderBy('time', 'asc'); // Сортировка по полю time по возрастанию
    
    // Получаем отфильтрованный список уроков
    $lessons = $query->get();

    return response()->json([
        'Список отфильтрованных уроков' => $lessons,
    ], 200, [], JSON_UNESCAPED_UNICODE);
    }   

    // Создание нового урока
    public function store(Request $request)
    {    
    
        $this->checkRole();
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|string|max:255',
            'time' => 'required',
            'teacher' => 'required|string|max:255'
        ]);

        $lesson = Lesson::create($validatedData);
        return response()->json([
            'Урок создан' => $lesson,
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    // Получение одного урока
    public function show($id)
    {
        $lesson = Lesson::findOrFail($id);
        return response()->json([
            "Получение урока с id $id" => $lesson,
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    // Обновление урока
    public function update(Request $request, $id)
    {   
        $this->checkRole();
        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'date' => 'date',
            'time' => 'nullable'
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->update($validatedData);
        return response()->json([
            "Обновление урока с id $id" => $lesson,
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }

    // Удаление урока
    public function destroy($id)
    {   
        $this->checkRole();
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();
        return response()->json([
            "Урок с id $id удалён",
        ], 201, [], JSON_UNESCAPED_UNICODE);
    }
}
