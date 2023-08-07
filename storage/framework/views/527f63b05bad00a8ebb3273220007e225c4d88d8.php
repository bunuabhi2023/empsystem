

<?php $__env->startSection('content'); ?>
    <div class="container">
        <h2>Employee ID Card Templates</h2>
        <a href="<?php echo e(route('templates.id-cards.create')); ?>" class="btn btn-primary">Create New Template</a>
        <?php if(session('success')): ?>
            <div class="alert alert-success mt-3">
                <?php echo e(session('success')); ?>

            </div>
        <?php endif; ?>

        <style>
            /* Your custom CSS styling for the ID card template */

            .container {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100vh;
                background-color: #e6ebe0;
            }

            .card {
                border: 1px solid #ccc;
                border-radius: 10px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                padding: 20px;
                max-width: 300px;
                margin: 0 auto;
            }

            .card-title {
                font-size: 20px;
                font-weight: bold;
                text-align: center;
                margin-bottom: 20px;
            }

            .card-preview {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 10px;
                margin-bottom: 20px;
            }

            .card-preview img {
                max-width: 100%;
                height: auto;
            }

            .card-buttons {
                display: flex;
                justify-content: space-between;
            }

            .card-buttons button {
                flex: 1;
            }

            .alert {
                margin-top: 20px;
            }
        </style>

        <div class="row mt-4">
            <?php $__currentLoopData = $templates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $template): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo e($template->name); ?></h5>
                            <div class="card-preview">
                                <?php echo html_entity_decode($template->template_html); ?>

                            </div>
                            <div class="mt-3">
                                <a href="<?php echo e(route('templates.id-cards.edit', $template->id)); ?>" class="btn btn-primary">Edit</a>
                                <form action="<?php echo e(route('templates.id-cards.delete', $template->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this template?')">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.main', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\app\resources\views/templates/id-cards/index.blade.php ENDPATH**/ ?>