const editButtons = document.querySelectorAll('.edit');

editButtons.forEach(button => {
    button.addEventListener('click', function() {
        // console.log(button.dataset.taskId);
        const form = button.parentElement;
        const taskText = form.querySelector('.task-text');
        const text = taskText.textContent.trim();
        const input = document.createElement('input');
        input.type = 'text';
        input.value = text;
        taskText.replaceWith(input);
        
    });
});
