<?php

namespace JiraRestApi\Label;

use JiraRestApi\JsonSerializableTrait;

class Label implements \JsonSerializable
{
    use JsonSerializableTrait;

    /** @var int */
    public $id;

    /** @var string */
    public $key;

    /** @var string */
    public $self;

    /** @var string */
    public $name;

}
