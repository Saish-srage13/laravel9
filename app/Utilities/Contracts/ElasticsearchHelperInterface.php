<?php

namespace App\Utilities\Contracts;

interface ElasticsearchHelperInterface {
    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param object $data
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */
    public function store(mixed $data): mixed;

    /**
     * Get a single email from elasticsearch.
     *
     * @param array $params
     * @return mixed - Return the list of emails
     */
    public function getById(array $params): mixed;

    /**
     * Get the email list from elasticsearch.
     *
     * @param array $params
     * @return mixed - Return the list of emails
     */
    public function getList(array $params): mixed;
}
