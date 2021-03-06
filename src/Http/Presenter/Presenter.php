<?php

declare(strict_types=1);

namespace Zeno\Http\Presenter;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Zeno\Http\Presenter\Format\Formatter;
use Zeno\Http\Service\Cors;

/**
 * @author  Iqbal Maulana <iq.bluejack@gmail.com>
 */
final class Presenter
{
    /**
     * @var Formatter[]
     */
    private array $formatters = [];
    private Formatter $callbackFormatter;
    private Cors $cors;

    public function __construct($formatters, Formatter $callbackFormatter, Cors $cors)
    {
        foreach ($formatters as $formatter) {
            $this->addFormatter($formatter);
        }

        $this->callbackFormatter = $callbackFormatter;
        $this->cors = $cors;
    }

    public function addFormatter(Formatter $formatter): void
    {
        $this->formatters[] = $formatter;
    }

    public function format(Request $request, int $statusCode, array $data): Response
    {
        foreach ($this->formatters as $formatter) {
            if (true === $formatter->supports($request)) {
                return $formatter->format($data, $statusCode);
            }
        }

        return $this->callbackFormatter->format($data, $statusCode);
    }

    public function render(Request $request, int $statusCode, array $data): Response
    {
        $response = $this->format($request, $statusCode, $data)->withHeaders([
            'Via'                          => config('app.response_header_via'),
            $this->getHeaderKey('Version') => config('app.version'),
        ]);

        return $this->cors->handle($request, $response);
    }

    private function getHeaderKey(string $key): string
    {
        return config('app.response_header_prefix').$key;
    }
}
