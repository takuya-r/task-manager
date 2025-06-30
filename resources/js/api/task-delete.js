export function deleteTask(taskId) {
    fetch(`/api/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        },
        credentials: 'same-origin'
    })
    .then(async res => {
        const data = await res.json(); // 常にJSONパース
        if (!res.ok) {
            throw data; // data.message を catch 側で使うためそのまま投げる
        }
        return data;
    })
    .then(data => {
        // タスクDOM要素の削除（ID: task-{taskId}）
        const el = document.getElementById(`task-${taskId}`);
        if (el) el.remove();

        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = data.message;
        msgDiv.className = 'mb-4 text-green-600 font-semibold';
        setTimeout(() => msgDiv.textContent = '', 3000);
    })
    .catch(err => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = err.message;
        msgDiv.className = 'mb-4 text-red-600 font-semibold';
    });
}
