export function updateStatus(selectElement) {
    const taskId = selectElement.dataset.taskId;
    const status = selectElement.value;

    fetch(`/api/tasks/${taskId}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ status }),
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
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = data.message;
        msgDiv.className = 'mb-4 text-green-600 font-semibold';
        setTimeout(() => msgDiv.textContent = '', 3000);

        // tasksMap の該当タスクのステータスを更新
        const task = window.tasksMap.get(Number(taskId));
        if (task) {
            task.status = status;
            window.tasksMap.set(Number(taskId), task);// 明示的に再セット（オプション）
        }
    })
    .catch(err => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = err.message;
        msgDiv.className = 'mb-4 text-red-600 font-semibold';
    });
}
