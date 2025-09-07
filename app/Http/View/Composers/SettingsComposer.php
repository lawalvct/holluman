<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Helpers\SettingsHelper;

class SettingsComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        $view->with([
            'companySettings' => SettingsHelper::getCompanySettings(),
            'metaSettings' => SettingsHelper::getMetaSettings(),
        ]);
    }
}
