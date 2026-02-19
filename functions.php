<?php
function dump($data) {
    echo "<pre>";
    var_dump($data);
    echo "</pre>";

}

function dd($data){
    dump($data);
    die;
}

/**
 * Получить все задачи из файла
 */
function getTasks($file) {
    if (!file_exists($file)) {
        return [];
    }
    $data = file_get_contents($file);
    return json_decode($data, true) ?? [];
}

/**
 * Сохранить задачи в файл
 */
function saveTasks($file, $tasks, $currentDate) {
    $json = json_encode($tasks, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    file_put_contents($file, $json);
    header('Location: ?date=' . $currentDate);
    exit;
}

/**
 * Найти задачу по ID
 */
function findTaskById($tasks, $id) {
    foreach ($tasks as $key => $task) {
        if ($task['id'] == $id) {
            return ['task' => $task, 'key' => $key];
        }
    }
    return null;
}

/**
 * Отфильтровать задачи по дате
 */
function filterTasksByDate($tasks, $date) {
    return array_filter($tasks, function($task) use ($date) {
        return $task['current_date'] === $date;
    });
}