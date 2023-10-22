<?php

namespace App\Indices;

use Illuminate\Support\Str;
use Elasticsearch;

class EmailLog extends BaseIndex
{
    public function store(mixed $data): mixed
    {
        $data = [
            'body' => [
                'data' => $data
            ],
            'index' => $this->indexName,
            'type' => 'my_type',
            'id' => $data->id,
        ];

        $result = Elasticsearch::index($data);

        return $result['_id'];
    }

    public function getById(array $params): mixed
    {
        $queryString = [
            'index'  => $this->indexName,
            'body'   => [
                'query' => [
                    'term' => [
                        '_id' => $params['id']
                    ]
                ]
            ],
        ];

        $result = Elasticsearch::search($queryString);

        if (isset($result['hits']['hits']) && count($result['hits']['hits']) != 0) {
            return $result['hits']['hits'][0]['_source']['data'];
        }

        return [];
    }

    /**
     * Undocumented function
     *
     * @param array $params
     * @return \Illuminate\Support\Collection
     */
    public function getList(array $params = []): mixed
    {
        try {
            $queryString = [
                'scroll' => '30s',
                'size'   => 50,
                'index'  => $this->indexName,
                'body'   => [
                    'query' => [
                        'query_string' => [
                            'query' => '*'
                        ]
                    ]
                ],
            ];
    
            $result = Elasticsearch::search($queryString);
    
            if (isset($result['hits']['hits']) && count($result['hits']['hits']) != 0) {
                $data = collect($result['hits']['hits'])->map(function ($email) {
                    return $email['_source']['data'];
                })
                ->transform(function ($email) {
                    // Standardize output to snake case format
                    $dataPoint = [];
                    collect($email)->map(function ($value, $keys) use (&$dataPoint) {
                        $dataPoint[Str::snake($keys)] = $value;
                    });
                    return $dataPoint;
                });
    
                return collect($data);
            }
    
            return collect([]);
        } catch (\Exception $ex) {
            return collect([]);
        }
    }
}