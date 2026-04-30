<?php $__env->startSection('content'); ?>
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="form-label">ค้นหา</label>
                <input type="text" name="keyword" value="<?php echo e(request('keyword')); ?>" class="form-control" placeholder="ทะเบียนรถ, ยี่ห้อ, รุ่น, รหัสคนขับ, ชื่อคนขับ">
            </div>
            <div class="col-auto">
                <button class="btn btn-primary">ค้นหา</button>
                <a href="<?php echo e(route('vehicles.index')); ?>" class="btn btn-outline-secondary">ล้าง</a>
            </div>
            <div class="col text-end">
                <a href="<?php echo e(route('vehicles.create')); ?>" class="btn btn-success">เพิ่มข้อมูลรถ</a>
            </div>
        </form>
    </div>
</div>
<div class="card">
    <div class="card-body table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>ทะเบียนรถ</th>
                    <th>ยี่ห้อ</th>
                    <th>รุ่น</th>
                    <th>พนักงานขับประจำรถ</th>
                    <th class="text-end">ความจุ</th>
                    <th>สถานะ</th>
                    <th class="text-end">จัดการ</th>
                </tr>
            </thead>
            <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr>
                    <td><?php echo e($vehicle->registration_number); ?></td>
                    <td><?php echo e($vehicle->brand); ?></td>
                    <td><?php echo e($vehicle->model ?: '-'); ?></td>
                    <td>
                        <?php if($vehicle->primaryDriver): ?>
                            <div><?php echo e($vehicle->primaryDriver->full_name); ?></div>
                            <small class="text-muted"><?php echo e($vehicle->primaryDriver->employee_code); ?></small>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end"><?php echo e(number_format($vehicle->capacity_kg ?? 0, 2)); ?></td>
                    <td><?php echo e($vehicle->status); ?></td>
                    <td class="text-end">
                        <a href="<?php echo e(route('vehicles.edit', $vehicle)); ?>" class="btn btn-sm btn-warning">แก้ไข</a>
                        <form method="POST" action="<?php echo e(route('vehicles.destroy', $vehicle)); ?>" class="d-inline" onsubmit="return confirm('ยืนยันการลบข้อมูลรถ?')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button class="btn btn-sm btn-outline-danger">ลบ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="7" class="text-center text-muted">ยังไม่มีข้อมูลรถ</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
        <?php echo e($vehicles->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\transport_cfarm\resources\views/vehicles/index.blade.php ENDPATH**/ ?>