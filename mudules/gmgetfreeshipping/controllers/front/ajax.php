<?php

class GMGetFreeShippingAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        ob_end_clean();
        header('Content-Type: application/json');
        die(json_encode([
            'preview' => $this->module->renderWidget(null, ['cart' => $this->context->cart]),
        ]));
    }
}
