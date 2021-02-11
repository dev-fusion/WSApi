<?php
class WSApi
{
    const API_URL = 'https://rest.websupport.sk';
    const TYPES = [
        'A' => [
            'name'  =>  'Adresa',
            'content' =>  'Cieľová IP',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'AAAA' => [
            'name'  =>  'Adresa',
            'content' =>  'Cieľová IP',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'ANAME' => [
            'name'  =>  'Adresa',
            'content' =>  'Cieľová Adresa',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'CNAME' => [
            'name'  =>  'Adresa',
            'content' =>  'Cieľová Adresa',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'MX' => [
            'name'  =>  'Adresa',
            'content' =>  'Mail Server',
            'prio'  =>  'Priorita',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'NS' => [
            'name'  =>  'Adresa',
            'content' =>  'Cieľová Adresa',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'SRV' => [
            'name'  =>  'Adresa',
            'content' =>  'Adresa Služby',
            'port'  =>  'Port',
            'weight'=>  'Váha',
            'prio'  =>  'Priorita',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ], 
        'TXT' => [
            'name'  =>  'Adresa',
            'content' =>  'Hodnota',
            'ttl'   =>  'TTL',
            'note'  =>  'Poznámka'
        ]
    ];
    
    private $apiKey;
    private $domain;
    private $secret;
    private $userId;
    
    public function __construct($domain, $apiKey, $secret) {
        $this->domain = $domain;
        $this->apiKey = $apiKey;
        $this->secret = $secret;
        
        $this->userId = $this->getUserId($this->domain) ?? 'self';
    }
    
    private function connectToApi($method = 'GET', $path = NULL, $query = '', $data = []) {
        if($path === NULL)
            throw new ErrorException('Path was not specified in JSON call.', 400);

        $time = time();
        $canonicalRequest = sprintf('%s %s %s', $method, $path, $time);
        $signature = hash_hmac('sha1', $canonicalRequest, $this->secret);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, sprintf('%s%s%s', self::API_URL, $path, $query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $this->apiKey.':'.$signature);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Accept: application/json',
            'Date: ' . gmdate('Ymd\THis\Z', $time),
        ]);
        
        if($method == 'POST' && !empty($data)) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }
        
        if($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        }
        
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response);

        if(isset($response->code) && $response->code >= 400)
            throw new ErrorException($response->message, $response->code);

        return $response;
    }
    
    private function getUserId($domain = NULL) {
        try {
            $response = $this->connectToApi('GET', '/v1/user');

            if($response->items[0] !== NULL && isset($response->items[0]->id))
                return $response->items[0]->id;
        } catch (ErrorException $e) {
             return exit($e->getMessage());
        }
    }
    
    public function getRecords($type = NULL): array {
        try {
            $response = $this->connectToApi('GET', '/v1/user/'.$this->userId.'/zone/'.$this->domain.'/record');

            if($response->items[0] !== NULL && isset($response->items[0]->id)) {
                if($type !== NULL)
                    foreach($response->items as $i => $record)
                        if($record->type !== $type)
                            unset($response->items[$i]);
                
                return $response->items;
            }
                
        } catch (ErrorException $e) {
             return exit($e->getMessage());
        }
        
        return array();
    }
    
    public function postRecord(WSApi\Record $record) {
        
        try {
            if($record->type !== '' && array_key_exists($record->type, self::TYPES) && $record->name !== '' && $record->content !== '') {
                $conditions = true;
                switch ($record->type) {
                    case "ANAME":
                        $record->name = '@';
                    break;
                    case "MX":
                        if($record->prio === NULL || !is_int($record->prio))
                            $conditions = false;
                    break;
                    case "SRV":
                        if($record->prio === NULL || !is_int($record->prio) || $record->port === NULL || !is_int($record->port) || $record->weight === NULL || !is_int($record->weight))
                            $conditions = false;
                    break;
                }
                
                if($conditions === true) {
                    $data = array_filter(get_object_vars($record), 'strlen');
                    $this->userId = 'self';
                    
                    $response = $this->connectToApi('POST', '/v1/user/'.$this->userId.'/zone/'.$this->domain.'/record', '', $data);

                    if($response->status === "success" && isset($response->item))
                        return $response->item;
                    else
                        if(isset($response->errors->name))
                            foreach($response->errors->name as $error)
                                throw new ErrorException($error, 400);
                        if(isset($response->errors->content))
                            foreach($response->errors->content as $error)
                                throw new ErrorException($error, 400);
                            
                }
            }
            
            throw new ErrorException('Required parameters are missing.', 400);

            
        } catch (ErrorException $e) {
             return exit($e->getMessage());
        }
        
        return;
    }
    
    public function putRecord($type, $name, $content, $data) {
        return;
    }
    
    public function deleteRecord(int $id) {
        try {
            $response = $this->connectToApi('DELETE', '/v1/user/'.$this->userId.'/zone/'.$this->domain.'/record/'.$id);

            if($response->status === "success" && isset($response->item))
                return $response->item;
        } catch (ErrorException $e) {
             return exit($e->getMessage());
        }
        
        return;
    }
 }