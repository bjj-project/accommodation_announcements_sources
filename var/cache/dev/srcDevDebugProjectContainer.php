<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerWexPh8F\srcDevDebugProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerWexPh8F/srcDevDebugProjectContainer.php') {
    touch(__DIR__.'/ContainerWexPh8F.legacy');

    return;
}

if (!\class_exists(srcDevDebugProjectContainer::class, false)) {
    \class_alias(\ContainerWexPh8F\srcDevDebugProjectContainer::class, srcDevDebugProjectContainer::class, false);
}

return new \ContainerWexPh8F\srcDevDebugProjectContainer(array(
    'container.build_hash' => 'WexPh8F',
    'container.build_id' => 'cfb3aeb6',
    'container.build_time' => 1543149104,
), __DIR__.\DIRECTORY_SEPARATOR.'ContainerWexPh8F');
