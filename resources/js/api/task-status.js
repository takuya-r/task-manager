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
    .then(res => res.json())
    .then(data => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = data.message || 'ステータスが更新されました';
        msgDiv.className = 'mb-4 text-green-600 font-semibold';
        setTimeout(() => msgDiv.textContent = '', 3000);
    })
    .catch(err => {
        const msgDiv = document.getElementById('status-message');
        msgDiv.textContent = 'ステータス更新に失敗しました';
        msgDiv.className = 'mb-4 text-red-600 font-semibold';
    });
}
