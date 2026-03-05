<?php
use humhub\widgets\Button;
use humhub\modules\ui\form\widgets\ActiveForm;
?>

<div class="panel panel-default">
    <div class="panel-heading"><strong>Microsoft Entra ID</strong> Integration Mapping</div>
    <div class="panel-body">
        
        <div class="alert alert-info">
            <i class="fa fa-info-circle"></i> 
            <strong>Note:</strong> Application ID and Client Secret are configured securely via Coolify Environment Variables (<code>ENTRASSO_CLIENT_ID</code>, etc.).
        </div>

        <?php $form = ActiveForm::begin(); ?>

        <h4>Group & Space Mapping</h4>
        <p class="help-block">Enter one mapping per line using the format: <code>Entra-Object-ID=HumHub-Name</code></p>
        
        <?= $form->field($model, 'groupMapping')->textarea(['rows' => 4, 'placeholder' => "11111111-2222-3333-4444-555555555555=Administrators\n22222222-3333-4444-5555-666666666666=Moderators"]) ?>
        <?= $form->field($model, 'spaceMapping')->textarea(['rows' => 4, 'placeholder' => "33333333-4444-5555-6666-777777777777=Marketing Team\n44444444-5555-6666-7777-888888888888=Global News"]) ?>

        <hr>
        <h4>User Onboarding</h4>
        <?= $form->field($model, 'requirePrivateEmail')->checkbox() ?>
        <p class="help-block">If checked, users will be locked to their account settings page until they fill out the 'private_email' profile field.</p>

        <?= Button::save()->submit() ?>
        <?php ActiveForm::end(); ?>
    </div>
</div>
