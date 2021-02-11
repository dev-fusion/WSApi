<?php
namespace WSApi;

class Record {
    public $type;
    public $name;
    public $content;
    public $prio;
    public $port;
    public $weight;
    public $ttl;
    public $note;
    
    public function __construct(array $data = []) {
        if(isset($data['type']))
            $this->type = $data['type'] ?? '';
        if(isset($data['name']))
            $this->name = $data['name'] ?? '';
        if(isset($data['content']))
            $this->content = $data['content'] ?? '';

        $this->prio = (int) ($data['prio'] ?? NULL);
        $this->port = (int) ($data['port'] ?? NULL);
        $this->weight = (int) ($data['weight'] ?? NULL);
        $this->ttl = (int) ($data['ttl'] ?? 600);
        $this->note = $data['note'] ?? NULL;
    }
}