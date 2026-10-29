<?php
/* To-Do List View */
?>
<div class="row">
    <div class="col-md-8 col-md-offset-2">
        <div class="box box-block bg-white">
            <div class="box-header with-border">
                <h3 class="box-title"> <i class="fa fa-check-square-o"></i> My To-Do List </h3>
                <div class="box-tools pull-right">
                    <span class="label label-info" id="total_tasks">0 Tasks</span>
                    <span class="label label-success" id="completed_tasks">0 Completed</span>
                    <span class="label label-warning" id="pending_tasks">0 Pending</span>
                </div>
            </div>
            <div class="box-body">
                <div class="input-group m-b-2">
                    <input type="text" id="new_task_title" class="form-control input-lg" placeholder="What needs to be done?" autocomplete="off">
                    <span class="input-group-btn">
                        <button class="btn btn-primary btn-lg" type="button" id="add_task_btn">
                            <i class="fa fa-plus"></i> Add Task
                        </button>
                    </span>
                </div>
                
                <div id="todo_list_container">
                    <?php $this->load->view('admin/todo/todo_items', array('todos' => $todos)); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Task Modal -->
<div class="modal fade" id="editTaskModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title"><i class="fa fa-edit"></i> Edit Task</h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit_task_id">
                <div class="form-group">
                    <label>Task Title</label>
                    <input type="text" id="edit_task_title" class="form-control" placeholder="Enter task title">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="save_task_btn">
                    <i class="fa fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Get base URL helper
function getBaseURL() {
    return '<?php echo base_url(); ?>';
}

// Get site URL helper
function getSiteURL(path) {
    return '<?php echo site_url(""); ?>' + path;
}

// Update task statistics
function updateTaskStats() {
    const todoItems = document.querySelectorAll('.todo-item');
    const total = todoItems.length;
    const completed = document.querySelectorAll('.todo-item.completed').length;
    const pending = total - completed;
    
    document.getElementById('total_tasks').textContent = total + ' Task' + (total !== 1 ? 's' : '');
    document.getElementById('completed_tasks').textContent = completed + ' Completed';
    document.getElementById('pending_tasks').textContent = pending + ' Pending';
}

// Add Task - Enter key support
document.getElementById('new_task_title').addEventListener('keypress', function(e) {
    if(e.which === 13 || e.keyCode === 13) {
        addTask();
    }
});

// Add Task - Button click
document.getElementById('add_task_btn').addEventListener('click', addTask);

function addTask() {
    const titleInput = document.getElementById('new_task_title');
    const title = titleInput.value.trim();
    
    if(title === '') {
        toastr.warning('Please enter a task title');
        return;
    }
    
    // Disable button to prevent double submission
    const btn = document.getElementById('add_task_btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', getSiteURL('admin/todo/add_task'), true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById('todo_list_container').innerHTML = xhr.responseText;
            titleInput.value = '';
            toastr.success('Task added successfully!');
            updateTaskStats();
        } else {
            toastr.error('Failed to add task');
        }
        
        // Re-enable button
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-plus"></i> Add Task';
    };
    
    xhr.onerror = function() {
        toastr.error('Network error occurred');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-plus"></i> Add Task';
    };
    
    xhr.send('title=' + encodeURIComponent(title));
}

function toggleTask(id, currentStatus) {
    const newStatus = currentStatus == 1 ? 0 : 1;
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', getSiteURL('admin/todo/update_status'), true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById('todo_list_container').innerHTML = xhr.responseText;
            updateTaskStats();
        } else {
            toastr.error('Failed to update task');
        }
    };
    
    xhr.send('id=' + id + '&status=' + newStatus);
}

function editTask(id) {
    const xhr = new XMLHttpRequest();
    xhr.open('POST', getSiteURL('admin/todo/get_task'), true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                if(response.success) {
                    document.getElementById('edit_task_id').value = response.task.id;
                    document.getElementById('edit_task_title').value = response.task.title;
                    $('#editTaskModal').modal('show');
                } else {
                    toastr.error(response.message || 'Failed to load task');
                }
            } catch(e) {
                toastr.error('Invalid response from server');
            }
        }
    };
    
    xhr.send('id=' + id);
}

// Save edited task
document.getElementById('save_task_btn').addEventListener('click', function() {
    const id = document.getElementById('edit_task_id').value;
    const title = document.getElementById('edit_task_title').value.trim();
    
    if(title === '') {
        toastr.warning('Please enter a task title');
        return;
    }
    
    const btn = this;
    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', getSiteURL('admin/todo/update_task'), true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById('todo_list_container').innerHTML = xhr.responseText;
            $('#editTaskModal').modal('hide');
            toastr.success('Task updated successfully!');
            updateTaskStats();
        } else {
            toastr.error('Failed to update task');
        }
        
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-save"></i> Save Changes';
    };
    
    xhr.send('id=' + id + '&title=' + encodeURIComponent(title));
});

function deleteTask(id) {
    if(!confirm('Are you sure you want to delete this task?')) return;
    
    const xhr = new XMLHttpRequest();
    xhr.open('POST', getSiteURL('admin/todo/delete_task'), true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    
    xhr.onload = function() {
        if(xhr.status === 200) {
            document.getElementById('todo_list_container').innerHTML = xhr.responseText;
            toastr.warning('Task deleted successfully');
            updateTaskStats();
        } else {
            toastr.error('Failed to delete task');
        }
    };
    
    xhr.send('id=' + id);
}

// Initialize stats on page load
document.addEventListener('DOMContentLoaded', function() {
    updateTaskStats();
});
</script>

<style>
    .todo-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        align-items: center;
        transition: all 0.3s ease;
        background: #fff;
    }
    
    .todo-item:hover {
        background: #f9f9f9;
        transform: translateX(5px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    
    .todo-item.completed {
        background: #f0f9ff;
    }
    
    .todo-item.completed .todo-text {
        text-decoration: line-through;
        color: #888;
    }
    
    .todo-text {
        flex-grow: 1;
        font-size: 16px;
        margin-left: 15px;
        cursor: pointer;
        user-select: none;
    }
    
    .todo-actions {
        margin-left: 10px;
        display: flex;
        gap: 5px;
    }
    
    .checkbox-custom {
        width: 20px;
        height: 20px;
        cursor: pointer;
        accent-color: #0275d8;
    }
    
    .box-tools {
        display: flex;
        gap: 5px;
    }
    
    .label {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    #new_task_title:focus {
        border-color: #0275d8;
        box-shadow: 0 0 5px rgba(2, 117, 216, 0.3);
    }
    
    .btn-xs {
        padding: 3px 8px;
        font-size: 12px;
    }
    
    .m-b-2 {
        margin-bottom: 20px;
    }
    
    .text-center {
        text-align: center;
    }
    
    .p-4 {
        padding: 40px;
    }
    
    .text-muted {
        color: #999;
    }
    
    .m-b-1 {
        margin-bottom: 10px;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .todo-item {
        animation: slideIn 0.3s ease;
    }
</style>
