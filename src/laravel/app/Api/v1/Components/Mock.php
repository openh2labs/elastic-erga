<?php

namespace App\Api\v1\Components;

class Mock
{

    /**
     * Returns path to files with mock data
     * @return string
     */
    public function getMockDataPath()
    {
        return base_path('tests/js/mocks/');
    }

    /**
     * Returns list of mock data files
     * @return array
     */
    public function getMockDataFileNames()
    {
        $ret = [
            'response_0.json',
            'response_1.json',
            'response_2.json',
        ];
        
        $path = $this->getMockDataPath();
        
        $ret = array_map(function($element) use ($path){
            return $path . $element;
        }, $ret);
        
        return $ret;
    }

    /**
     * Loads mock data from file
     * @param $filename
     * @return array
     */
    protected function loadMockData($filename)
    {
        $data = file_get_contents($filename);
        $json = json_decode($data);
        return $json->events;
    }

    /**
     * Generates random data set
     * @param string $q
     * @return mixed
     */
    public function generateMockData($q='')
    {
        $files = $this->getMockDataFileNames();
        $file = $files[array_rand($files)];
        $data = $this->loadMockData($file);

        return $this->filter($data, $q);
    }

    /**
     * Simple O(n) filter
     * @param array $data
     * @param string $q
     * @return array
     */
    public function filter(array $data, $q)
    {
        if (empty($q)) {
            return $data;
        }

        $ret = [];

        foreach ($data as $line) {
            if (stripos($line->event_message, $q) !== false) {
                $ret[] = $line;
            }
        }

        return $ret;
    }


}