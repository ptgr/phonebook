<?php

declare(strict_types=1);

namespace App\Presenters;

use Nette;

final class PhonebookPresenter extends Nette\Application\UI\Presenter
{
    public function actionGetAll()
    {
        $this->sendJson("test");
    }
}
