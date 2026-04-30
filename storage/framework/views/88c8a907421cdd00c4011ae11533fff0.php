<?php $__env->startSection('content'); ?>
<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo e(route('transport-jobs.store')); ?>" class="d-grid gap-4">
            <?php echo csrf_field(); ?>
            <?php echo $__env->make('transport-jobs._form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <div>
                <button class="btn btn-primary">บันทึกเที่ยวขนส่ง</button>
                <a href="<?php echo e(route('transport-jobs.index')); ?>" class="btn btn-outline-secondary">กลับ</a>
            </div>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\transport_cfarm\resources\views/transport-jobs/create.blade.php ENDPATH**/ ?>