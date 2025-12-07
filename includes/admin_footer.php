</div> 

<footer class="text-center py-3 mt-4" style="background-color: #1B2A41; color: #92B4F4;">
    <small>&copy; <?= date('Y') ?> Bulinaw Beach Resort Admin Portal. All rights reserved.</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('employeeTable');
    const responseDiv = document.getElementById('generalResponse');

    function showResponse(message, isError) {
        responseDiv.textContent = message;
        responseDiv.style.display = 'block';
        responseDiv.className = 'response-message'; 
        
        if (isError) {
            responseDiv.classList.add('alert', 'alert-danger');
        } else {
            responseDiv.classList.add('alert', 'alert-success');
        }
        
        setTimeout(() => {
            responseDiv.style.display = 'none';
        }, 5000);
    }

    if (table) {
        table.addEventListener('click', async function(e) {
            const deleteBtn = e.target.closest('.delete-employee-btn');
            if (!deleteBtn) return;
            
            const employeeId = deleteBtn.dataset.id;
            const employeeRow = deleteBtn.closest('tr');
            const employeeNameElement = employeeRow ? employeeRow.querySelector('td:nth-child(2)') : null;
            const employeeName = employeeNameElement ? employeeNameElement.textContent : 'this employee';
            
            if (!confirm(`Are you sure you want to permanently fire ${employeeName} (ID: ${employeeId})? This action cannot be undone and will delete all related employee data.`)) {
                return;
            }
            
            deleteBtn.disabled = true; 
            deleteBtn.textContent = 'Deleting...';

            const formData = new FormData();
            formData.append('employee_id', employeeId);

            try {
                const res = await fetch('../handlers/delete_employee.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await res.json();

                if (result.success) {
                    showResponse(result.success, false);
                    if (employeeRow) {
                        employeeRow.style.backgroundColor = '#f44336aa'; 
                        employeeRow.style.transition = 'opacity 1s ease';
                        setTimeout(() => employeeRow.style.opacity = 0, 500);
                        setTimeout(() => employeeRow.remove(), 1500);
                    }
                } else {
                    showResponse(result.error || 'Deletion failed due to unknown server error.', true);
                    deleteBtn.disabled = false;
                    deleteBtn.textContent = 'Fire/Delete';
                }

            } catch (error) {
                console.error('Network or parsing error:', error);
                showResponse('A network error occurred. Check the handler path.', true);
                deleteBtn.disabled = false;
                deleteBtn.textContent = 'Fire/Delete';
            }
        });
    }
});
</script>
</body>
</html>
