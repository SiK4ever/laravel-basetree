<?php


namespace BaseTree\Tests\Unit\Exception;


use BaseTree\Exception\LaravelHandler;
use BaseTree\Tests\Fake\Unit\DummyModel;
use BaseTree\Tests\Unit\TestCase;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class HttpHandlerTest extends TestCase
{
    /**
     * @var LaravelHandler
     */
    private $instance;

    public function setUp()
    {
        parent::setUp();
        $this->instance = new LaravelHandler($this->app);
    }

    /**
     * I don't care about this. Laravel will handle it
     * @test
     */
    public function handle_http_report(): void
    {
        $exception = (new ModelNotFoundException)->setModel(get_class(new DummyModel));

        $response = $this->instance->render(request(), $exception);

        $this->assertInstanceOf(Response::class, $response);
    }

    /** @test */
    public function handle_http_render(): void
    {
        $exception = new Exception;
        Log::shouldReceive('error');

        $this->instance->report($exception);
    }
}