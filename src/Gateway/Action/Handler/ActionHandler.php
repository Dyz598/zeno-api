<?php

declare(strict_types=1);

namespace Zeno\Gateway\Action\Handler;

use Illuminate\Http\Request;
use Zeno\Gateway\Action\ActionResponse;
use Zeno\Router\Model\Route;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
interface ActionHandler
{
    public function handle(Route $route, Request $request): ActionResponse;

    public function name(): string;
}
