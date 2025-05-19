<?php use Carbon\Carbon; ?>

<?php if (isset($component)) { $__componentOriginal1f9e5f64f242295036c059d9dc1c375c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c = $attributes; } ?>
<?php $component = App\View\Components\Layout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\Layout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['Title' => 'Customers']); ?>
    <div class="col content">

        <div class="d-flex">
            <div class="p2 flex-grow-1">
                <H2><?php echo e($customer->customer_names); ?></H2>
            </div>
            <a href="<?php echo e(route('customers.edit', $customer)); ?>" id="edit-registrant"><i
                    class="bi bi-pencil-square fs-2"></i></a>
        </div>

        <!-- Include the form to display the registrant details -->
        <?php echo $__env->make('forms.registrant', ['action' => 'display'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <hr>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">
                <button class="nav-link active" id="nav-current-tab" data-bs-toggle="tab" data-bs-target="#nav-current"
                        type="button" role="tab" aria-controls="nav-current" aria-selected="true">Current
                </button>
                <button class="nav-link" id="nav-history-tab" data-bs-toggle="tab" data-bs-target="#nav-history"
                        type="button" role="tab" aria-controls="nav-history" aria-selected="false">History
                </button>
            </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">

            <!-- Table containing the currently active needs and services -->
            <div class="tab-pane fade show active" id="nav-current" role="tabpanel" aria-labelledby="nav-current-tab"
                 tabindex="0">
                <table id="attributes-table" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $attributeService->currentAttributes($customer); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item['code']); ?></td>
                            <td><?php echo e($item['description']); ?></td>
                            <td><?php echo e(Carbon::parse($item['valid_from'])->format('d-m-Y')); ?></td>
                            <td><?php if(in_array($item['code'], ['32', '33', '34'])): ?>
                                    <?php echo e(Carbon::parse($item['temp_end_date'])->format('d-m-Y')); ?>

                                <?php else: ?>
                                    <?php echo e(Carbon::parse($item['valid_to'])->format('d-m-Y')); ?>

                                <?php endif; ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <?php $__env->startPush('scripts'); ?>
                    <script>
                        $(document).ready(function () {
                            $('#attributes-table').DataTable({
                                    autoWidth: false
                                    , pageLength: 25
                                    , lengthMenu: [10, 25, 50, 100]
                                }
                            );
                        });
                    </script>
                <?php $__env->stopPush(); ?>
            </div>

            <!-- Table containing the need and service history -->
            <div class="tab-pane fade" id="nav-history" role="tabpanel" aria-labelledby="nav-history-tab" tabindex="0">
                <table id="attributeshistory-table" class="table table-striped" style="width:100%">
                    <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $__currentLoopData = $attributeService->previousAttributes($customer); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($item['code']); ?></td>
                            <td><?php echo e($item['description']); ?></td>
                            <td><?php echo e($item['valid_from']); ?></td>
                            <td><?php echo e($item['valid_to']); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>

                <?php $__env->startPush('scripts'); ?>
                    <script>
                        $(document).ready(function () {
                            $('#attributeshistory-table').DataTable({
                                    autoWidth: false
                                    , pageLength: 25
                                    , lengthMenu: [10, 25, 50, 100]
                                }
                            );
                        });
                    </script>
                <?php $__env->stopPush(); ?>
            </div>
        </div>

    </div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $attributes = $__attributesOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__attributesOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c)): ?>
<?php $component = $__componentOriginal1f9e5f64f242295036c059d9dc1c375c; ?>
<?php unset($__componentOriginal1f9e5f64f242295036c059d9dc1c375c); ?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/customers/show.blade.php ENDPATH**/ ?>