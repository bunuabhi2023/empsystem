

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h2>Edit Employee ID Card Template</h2>
        <form action="<?php echo e(route('templates.id-cards.update', $template->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>
            <div class="form-group">
                <label for="name">Template Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?php echo e($template->name); ?>" required>
            </div>
            <div class="form-group">
                <label for="template_html">Template HTML</label>
                <textarea name="template_html" id="template_html" class="form-control" required><?php echo e($template->template_html); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Update Template</button>
        </form>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\app\resources\views/templates/id-cards/edit.blade.php ENDPATH**/ ?>