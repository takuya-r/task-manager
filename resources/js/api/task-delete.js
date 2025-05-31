export function deleteTask(taskId) {
    fetch(`/api/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(res => {
        if (!res.ok) {
            throw new Error('削除に失敗しました');
        }
        return res.json();
    })
    .then(data => {
        // タスクDOM要素の削除（ID: task-{taskId}）
        const el = document.getElementById(`task-${taskId}`);
        if (el) el.remove();

        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = data.message || 'タスクを削除しました';
        msgDiv.className = 'mb-4 text-green-600 font-semibold';
        setTimeout(() => msgDiv.textContent = '', 3000);
    })
    .catch(err => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = '削除に失敗しました';
        msgDiv.className = 'mb-4 text-red-600 font-semibold';
    });
}
