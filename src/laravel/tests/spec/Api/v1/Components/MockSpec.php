<?php

namespace tests\spec\App\Api\v1\Components;

use App\Api\v1\Components\Mock;
use PhpSpec\Laravel\LaravelObjectBehavior;
use Prophecy\Argument;

class MockSpec extends LaravelObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Mock::class);
    }

    function it_returns_a_data_path()
    {
        $this->getMockDataPath()->shouldBeString();
        $this->getMockDataPath()->shouldExist();
    }

    function it_generates_mock_data()
    {
        $this->generateMockData()->shouldBeArray();
        $this->generateMockData()->shouldNotBeEmpty();
    }

    public function it_returns_existing_mock_files()
    {
        $this->getMockDataFileNames()->shouldBeArray();
        $this->getMockDataFileNames()->shouldNotBeEmpty();
    }
    
    public function getMatchers()
    {
        return [
            'exist' => function($subject){
                return file_exists($subject);
            },
            'beEmpty' => function($subject){
                return empty($subject);
            },
        ];
    }



}
