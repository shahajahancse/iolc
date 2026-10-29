<?php if(!empty($todos)): ?>
    <?php foreach($todos as $todo): ?>
        <div class="todo-item <?php echo $todo->status == 1 ? 'completed' : ''; ?>">
            <input type="checkbox" class="checkbox-custom" 
                <?php echo $todo->status == 1 ? 'checked' : ''; ?> 
                onclick="toggleTask(<?php echo $todo->id; ?>, <?php echo $todo->status; ?>)">
            
            <span class="todo-text" onclick="toggleTask(<?php echo $todo->id; ?>, <?php echo $todo->status; ?>)">
                <?php echo htmlspecialchars($todo->title); ?>
            </span>
            
            <div class="todo-actions">
                <button class="btn btn-xs btn-outline-primary" onclick="editTask(<?php echo $todo->id; ?>)" title="Edit Task">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-xs btn-outline-danger" onclick="deleteTask(<?php echo $todo->id; ?>)" title="Delete Task">
                    <i class="fa fa-trash"></i>
                </button>
            </div>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <div class="text-center p-4 text-muted">
        <i class="fa fa-smile-o fa-2x m-b-1"></i><br>
        All caught up! No tasks pending.
    </div>
<?php endif; ?>
