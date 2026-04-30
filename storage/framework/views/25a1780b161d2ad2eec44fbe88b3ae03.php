<?php $__env->startSection('content'); ?>
<div class="row g-4 mb-4">
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">รถพร้อมใช้งาน</div>
            <div class="display-6 fw-semibold"><?php echo e(number_format($vehicleCount)); ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">พนักงานขับพร้อมงาน</div>
            <div class="display-6 fw-semibold"><?php echo e(number_format($driverCount)); ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">ฟาร์ม</div>
            <div class="display-6 fw-semibold"><?php echo e(number_format($farmCount)); ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">คู่สัญญาใช้งาน</div>
            <div class="display-6 fw-semibold"><?php echo e(number_format($vendorCount)); ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">เที่ยววันนี้</div>
            <div class="display-6 fw-semibold"><?php echo e(number_format($todayJobsCount)); ?></div>
        </div>
    </div>
    <div class="col-md-4 col-xl-2">
        <div class="card stat-card p-3">
            <div class="text-muted small">ค่าน้ำมันเดือนนี้</div>
            <div class="h4 fw-semibold mb-0"><?php echo e(number_format($monthlyOilCost, 2)); ?></div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center">
        <span class="fw-semibold">เที่ยวขนส่งล่าสุด</span>
        <a href="<?php echo e(route('transport-jobs.index')); ?>" class="btn btn-sm btn-primary">ดูทั้งหมด</a>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>วันที่</th>
                        <th>เลขที่เอกสาร</th>
                        <th>รถ</th>
                        <th>พนักงานขับ</th>
                        <th>ฟาร์ม</th>
                        <th>คู่สัญญา</th>
                        <th class="text-end">ค่าน้ำมัน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentJobs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $job): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($job->transport_date?->format('d/m/Y')); ?></td>
                            <td><?php echo e($job->document_no); ?></td>
                            <td><?php echo e($job->vehicle?->registration_number); ?></td>
                            <td><?php echo e($job->driver?->full_name); ?></td>
                            <td><?php echo e($job->farm?->farm_name); ?></td>
                            <td><?php echo e($job->vendor?->vendor_name); ?></td>
                            <td class="text-end"><?php echo e(number_format($job->total_oil_cost, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">ยังไม่มีข้อมูลเที่ยวขนส่ง</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\transport_cfarm\resources\views/dashboard/index.blade.php ENDPATH**/ ?>