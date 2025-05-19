<?php

    // Get the current route being used for displaying the form so we can adjust its behaviour
    use Illuminate\Support\Facades\Route;
    $route = Route::currentRouteName();

     // Get the registration details
    $registration = $customer->registrations->where('active', '=', 1)->first();

    // set the variables for use in the form control
    $readonly = 'readonly class=form-control-plaintext';
    $editable = 'class=form-control';
    $disabled = 'disabled';
    $enabled = '';

    // Define the actions based on the route
    switch(true) {
        case in_array($route, ["customers.show"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $disabled;
            $consent = $readonly;
            $removed = $readonly;
            $acctname = $readonly;
            $recipient = $readonly;
            if(!isset($registration->removed_date)) {
                $removedVisible = 'hidden';
            } else {
                $removedVisible = null;
            }
            if(!isset($registration->consent_date)) {
                $consentVisible = 'hidden';
            } else {
                $consentVisible = null;
            }
            break;
        case in_array($route, ["registrations.create"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $enabled;
            $consent = $editable;
            $removed = $readonly;
            $acctname = $readonly;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
            break;
        case in_array($route, ["customers.edit"]):
            $psrid = $readonly;
            $sapbpref = $readonly;
            $custref = $readonly;
            $status = $readonly;
            $source = $disabled;
            $consent = $editable;
            $removed = $editable;
            $acctname = $readonly;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
            break;
        default:
            $psrid = $editable;
            $sapbpref = $editable;
            $custref = $editable;
            $status = $editable;
            $source = $enabled;
            $consent = $editable;
            $removed = $editable;
            $acctname = $editable;
            $recipient = $editable;
            $removedVisible = null;
            $consentVisible = null;
    }

    //dd($route);
    //dd($registration->source);
    //getAllAttributes();

?>


<form id="registrant-data">

    <div class="row">
        <div class="form-group col-md-2">
            <label for="psrid">PSR ID:</label>
            <!-- Check if this is an existing registrant and display their id -->
            <input type="text" <?php echo e($psrid); ?> id="psrid" name="psr_id" value=
                <?php if( in_array($action, array('display', 'edit'))): ?>
                "<?php echo e($registration->id); ?>"
                <?php endif; ?>
            >
        </div>
        <div class="form-group col-md-3">
            <label for="sapbpref">SAP Business Partner:</label>
            <input type="text" <?php echo e($sapbpref); ?> id="sapbpref" name="sap_bp_ref" value="<?php echo e($customer->sap_reference); ?>">
        </div>
        <div class="form-group col-md-3">
            <label for="custref">Customer Reference:</label>
            <input type="text" <?php echo e($custref); ?> id="custref" name="customer_ref" value="<?php echo e($customer->id); ?>">
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-3">
            <label for="status">Status:</label>
            <input type="text" <?php echo e($status); ?> id="status" name="status"
                   value="<?php echo e($customerService->customerStatus($customer)); ?>">
        </div>

        <div class="form-group col-md-4">
            <label for="source">Registration Source:</label>
            <span id="source_name-error" class="error text-danger"><?php $__errorArgs = ['source_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($message); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></span>
            <select id="source" name="source" class="form-control" <?php echo e($source); ?> required class="form-control <?php echo e($errors->has('source_name') ? 'is-invalid' : ''); ?>">>
                <?php $__currentLoopData = App\Models\Source::all()->where('active',1); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $source): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($source->id); ?>"
                            <?php if($source->id == ((isset($registration->source) ? old('source', $registration->source) : '' ))): ?>
                                selected="selected"
                        <?php endif; ?>
                    ><?php echo e($source->source); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>

        <div class="form-group col-md-2">
            <label for="consent">Consent to Share:</label>
            <input type="date" <?php echo e($consent); ?> class="form-control" id="consent" name="consent_date"
                   value="<?php echo e(isset($registration->consent_date) ? old('consent_date', $registration->consent_date) : null); ?>" <?php echo e($consentVisible); ?>>
        </div>

        <div class="form-group col-md-2">
            <label for="remove">Date Removed:</label>
            <input type="date" <?php echo e($removed); ?> class="form-control" id="remove" name="removed_date"
                   value="<?php echo e(isset($registration->removed_date) ? old('remove_date', $registration->removed_date) : null); ?>" <?php echo e($removedVisible); ?>>
        </div>
    </div>

    <div class="row">
        <div class="form-group col-md-4">
            <label for="accountname">Account Name:</label>
            <input type="text" <?php echo e($acctname); ?> id="accountname" name="account_name" value="<?php echo e($customer->customer_names); ?>">
        </div>
        <div class="form-group col-md-4">
            <label for="recipientname">Recipient Name:</label>
            <span id="recipient_name-error" class="error text-danger"><?php $__errorArgs = ['recipient_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <?php echo e($message); ?> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?></span>
            <input type="text" <?php echo e($recipient); ?> id="recipientname" name="recipient_name"
                   value="<?php echo e(isset($registration->recipient_name) ? old('recipient_name', $registration->recipient_name) : ''); ?>"
                   required class="form-control <?php echo e($errors->has('recipient_name') ? 'is-invalid' : ''); ?>">
        </div>
    </div>

</form>

<?php if(in_array($route, ['registrations.create', 'customers.edit'])): ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('attribute-list', ['customer' => $customer]);

$__html = app('livewire')->mount($__name, $__params, 'lw-2333096385-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php endif; ?>
<?php /**PATH /var/www/html/resources/views/forms/registrant.blade.php ENDPATH**/ ?>