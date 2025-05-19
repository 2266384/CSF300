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

        <!-- Check that customers are loaded before displaying the data -->
        <?php if(isset($customers)): ?>
            <!-- Table containing the search results of the reference value -->
            <table id="customer-table" class="table table-striped" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Primary Title</th>
                    <th>Primary Forename</th>
                    <th>Primary Surname</th>
                    <th>Primary DoB</th>
                    <th>Secondary Title</th>
                    <th>Secondary Forename</th>
                    <th>Secondary Surname</th>
                    <th>Secondary DoB</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php $__currentLoopData = $customers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $customer): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($customer->id); ?></td>
                        <td><?php echo e($customer->primary_title); ?></td>
                        <td><?php echo e($customer->primary_forename); ?></td>
                        <td><?php echo e($customer->primary_surname); ?></td>
                        <td><?php echo e($customer->primary_dob ? $customer->primary_dob->format('d-m-Y') : ""); ?></td>
                        <td><?php echo e($customer->secondary_title); ?></td>
                        <td><?php echo e($customer->secondary_forename); ?></td>
                        <td><?php echo e($customer->secondary_surname); ?></td>
                        <td><?php echo e($customer->secondary_dob ? $customer->secondary_dob->format('d-m-Y') : ""); ?></td>
                        <td>
                            <div class="buttoncontainer">
                                <?php if( $customer->registrations->where('active', '=', 1)->first() === null): ?>
                                    <form action="<?php echo e(route('registrations.create', $customer)); ?>" method="GET" >
                                        <button class="btn p-0 border-0 bg-transparent" title="Create Registrant"><i class="bi bi-file-plus fs-4"></i></button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?php echo e(route('customers.show', $customer)); ?>" method="GET" >
                                        <button type="submit" class="btn p-0 border-0 bg-transparent" title="Show Registrant"><i class="bi bi-eye fs-4"></i></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>

            <?php $__env->startPush('scripts'); ?>
                <script>
                    $(document).ready( function () {
                        $('#customer-table').DataTable({
                                autoWidth: false
                                ,pageLength: 25
                                ,lengthMenu: [10, 25, 50, 100]
                            }
                        );
                    } );
                </script>
            <?php $__env->stopPush(); ?>
        <?php else: ?>
            <p>No customers found.</p>
        <?php endif; ?>


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
<?php /**PATH /var/www/html/resources/views/customers/index.blade.php ENDPATH**/ ?>