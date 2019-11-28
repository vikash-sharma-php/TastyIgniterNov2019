<?php if ($paymentGateways) { ?>
    <div class="row">
        <div class="col-sm-8">
            <input
                type="hidden"
                name="payment"
                value="">
            <div class="form-group">
                <label for=""><?= lang('igniter.cart::default.checkout.label_payment_method'); ?> *</label><br/>
                <div class="btn-group btn-group-toggle btn-group-vertical w-100" data-toggle="buttons">
                    <?php foreach ($paymentGateways as $paymentGateway) { ?>
                        <div
                            class="btn btn-light text-left<?= set_value('payment') == $paymentGateway->code ? ' active' : ''; ?>"
                            role="button">
                            <input
                                type="radio"
                                name="payment"
                                value="<?= $paymentGateway->code ?>"
                                autocomplete="off"
                                <?= set_radio('payment', $paymentGateway->code) ?>
                            />
                            <?= $paymentGateway->name; ?>
                            <?php if (!$paymentGateway->isApplicable($order->order_total, $paymentGateway)) { ?>
                                <p class="text-info mb-0">
                                    <?= sprintf(
                                        lang('igniter.payregister::default.alert_min_order_total'),
                                        currency_format($paymentGateway->order_total),
                                        $paymentGateway->name
                                    ); ?>
                                </p>
                            <?php } ?>
                        </div>
                        <?= $paymentGateway->renderPaymentForm($this->controller); ?>
                    <?php } ?>
                </div>
                <?= form_error('payment', '<span class="text-danger">', '</span>'); ?>
            </div>
        </div>
    </div>
<?php } ?>
