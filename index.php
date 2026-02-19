<?php
// Сохранение текущей даты в сессии
session_start();
require_once "config.php";
require_once "functions.php";


// Установка даты по умолчанию (если в сессии ничего нет)
if (!isset($_SESSION['current_date'])) {
    $_SESSION['current_date'] = date('Y-m-d'); // храним строку!
}
// Обработка GET-параметров для смены даты
// Если в GET передан конкретный параметр date в формате Y-m-d
if (isset($_GET['date']) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['date'])) {
    $_SESSION['current_date'] = $_GET['date'];
}

$currentDate = $_SESSION['current_date'];

// Получаем все задачи
$tasks = getTasks(TODO_FILE);
// Массив для хранения ошибок
$errors = [];

// Обработка Post обработка кнопки удалить
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    if (isset($_POST['delete'])){
        $id = $_POST['delete'];

        foreach($tasks as $key => $task){
            if ($task['id'] == $id){
                unset($tasks[$key]);
                break;
            }
        }
        saveTasks(TODO_FILE, $tasks, $currentDate);

    }

    if (isset($_POST['done'])){
        $id = $_POST['done'];

        foreach($tasks as $key => $task){
            if ($task['id'] == $id){
                $tasks[$key]['is_done'] = !$tasks[$key]['is_done'] ;
                break;
            }
        }
        saveTasks(TODO_FILE, $tasks, $currentDate);
    }

    if (isset($_POST['add_task']) && !empty($_POST['text_task'])) {
        $textTask = $_POST['text_task'];

        $newTask = [
            'id' => time(),
            'current_date' => $currentDate,
            'text' => $textTask,
            'is_done' => false
        ];
        $tasks[] = $newTask;
        saveTasks(TODO_FILE, $tasks, $currentDate);
    }elseif (isset($_POST['add_task'])) {
        $errors[] = 'Поле не может быть пустым';
    }

}

$filteredTasks = filterTasksByDate($tasks, $currentDate);

require_once "header.php";
?>


    <!-- Шапка с датой -->
<div class="header">
    <h2>📋 Todo-list <?= date('d-m-Y', strtotime($currentDate)) ?> </h2>
    <div class="date-nav">
        <a href="?date=<?= (new DateTime($currentDate))->modify('-1 day')->format('Y-m-d') ?>">← Вчера</a>
        <a href="?date=<?= date('Y-m-d') ?>">📅 Сегодня</a>
        <a href="?date=<?= (new DateTime($currentDate))->modify('+1 day')->format('Y-m-d') ?>">Завтра →</a>
    </div>
</div>

<!-- Основной контент -->
<div class="content">
    <!-- Форма добавления задачи -->
    <div class="add-task">
        <h3>➕ Новая задача</h3>
        <form action="" method="POST">
            <input type="text" name="text_task" placeholder="Например, купить молоко" required>
            <button type="submit" name="add_task">Добавить</button>
        </form>
    </div>

    <!-- Список задач -->
    <div class="tasks-list">
        <h3>📌 Задачи на <?= date('d.m.Y', strtotime($currentDate)) ?></h3>
        
        <?php if (empty($filteredTasks)): ?>
            <div class="empty-list">
                ✨ На этот день задач нет. Отдыхаем? ✨
            </div>
        <?php else: ?>
            <?php foreach ($filteredTasks as $task): ?>
                <div class="task-item">
                    <form action="" method="POST">
                        <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                        <button type="submit" name="done" value="<?= $task['id'] ?>">
                            <?= $task['is_done'] ? '✓ Выполнено' : '○ Выполнить' ?>
                        </button>
                        <span class="task-text <?= $task['is_done'] ? 'completed' : '' ?>">
                            <?= htmlspecialchars($task['text']) ?>
                        </span>
                        <button class="edit" type="button" name="edit" title="Редактировать" data-task-id="<?= $task['id'] ?>">✏️</button>
                        <button type="submit" name="delete" value="<?= $task['id'] ?>" onclick="return confirm('Удалить задачу?')">
                            ✕ Удалить
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
    
<?php require_once "footer.php"; ?>