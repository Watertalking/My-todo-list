const editButtons = document.querySelectorAll('.edit');

editButtons.forEach(button => {
    button.addEventListener('click', function() {
        const form = button.parentElement;
        const taskText = form.querySelector('.task-text');
        const text = taskText.textContent.trim();
        const input = document.createElement('input');
        input.type = 'text';
        input.value = text;
        taskText.replaceWith(input);
        input.className = 'task-text';

        // Добавляем обработчик на Enter
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const newText = input.value.trim();
                if (newText) { // Проверяем, что не пусто
                    saveEdit(input, button.dataset.taskId, newText);
                }
            }
        });

        input.focus();

        // input.addEventListener('blur', function(){
        //     saveEdit(input, taskId);
        // });
        
    });
});


function saveEdit(input, taskId, newText) {
    // Создаем данные для отправки
    const formData = new FormData();
    formData.append('edit_task', true);
    formData.append('task_id', taskId);
    formData.append('new_text', newText);
    
    // Отправляем POST-запрос
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Парсим JSON
    .then(task => {
        // task содержит id, text, is_done, current_date
        const newSpan = document.createElement('span');
        newSpan.className = 'task-text';
        if (task.is_done) {
            newSpan.classList.add('completed');
        }
        newSpan.textContent = task.text;
        input.replaceWith(newSpan);
    })
    .catch(error => console.error('Ошибка:', error));
}
