<?php 

namespace ProductionPanic\WebpEasy\Backend\Wp\Attributes;

use Attribute;

#[Attribute]
class WpAction {
    public string $name;
    public string $priority;
    public int $accepted_args;

    public function __construct(string $name, $priority = '10', int $accepted_args = 1) {
        $this->name = $name;
        $this->priority = $priority;
        $this->accepted_args = $accepted_args;
    }
}
