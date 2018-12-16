<?php

namespace App\Presenters;

use App\Transformers\SettingTransformer;
use Prettus\Repository\Presenter\FractalPresenter;

/**
 * Class SettingPresenter.
 */
class SettingPresenter extends FractalPresenter
{
    /**
     * Transformer.
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new SettingTransformer();
    }
}
