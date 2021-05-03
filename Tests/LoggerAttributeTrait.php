<?php

namespace Tests\Koded\Logging;

trait LoggerAttributeTrait
{
    private function property(object $logger, string $property)
    {
        $prop = new \ReflectionProperty($logger, $property);
        $prop->setAccessible(true);
        return $prop->getValue($logger);
    }
}
