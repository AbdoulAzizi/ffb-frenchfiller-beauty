<?php

// This file has been auto-generated by the Symfony Dependency Injection Component for internal use.

if (\class_exists(\ContainerQx47s5f\appProdProjectContainer::class, false)) {
    // no-op
} elseif (!include __DIR__.'/ContainerQx47s5f/appProdProjectContainer.php') {
    touch(__DIR__.'/ContainerQx47s5f.legacy');

    return;
}

if (!\class_exists(appProdProjectContainer::class, false)) {
    \class_alias(\ContainerQx47s5f\appProdProjectContainer::class, appProdProjectContainer::class, false);
}

return new \ContainerQx47s5f\appProdProjectContainer([
    'container.build_hash' => 'Qx47s5f',
    'container.build_id' => 'b58cdbb6',
    'container.build_time' => 1649432552,
], __DIR__.\DIRECTORY_SEPARATOR.'ContainerQx47s5f');
