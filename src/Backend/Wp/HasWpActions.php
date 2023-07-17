<?php

namespace ProductionPanic\WebpEasy\Backend\Wp;

use Sendy\Woocommerce\Attributes\WpAction;
use Sendy\Woocommerce\Attributes\WpFilter;
use Sendy\Woocommerce\Attributes\WpAjax;

abstract class HasWpActions extends Singleton
{
    protected function __construct()
    {
        $reflection_child = new \ReflectionClass($this);
        $methods = $reflection_child->getMethods();
        $checkfor = [WpAction::class, WpFilter::class, WpAjax::class];
        foreach ($methods as $method) {
            $attributes = $method->getAttributes();
            foreach ($attributes as $attribute) {
                if (in_array($attribute->getName(), $checkfor)) {
                    $name = $attribute->getArguments()['name'];
                    $priority = $attribute->getArguments()['priority'];
                    $accepted_args = $attribute->getArguments()['accepted_args'];
                    $action = $attribute->getName();
                    $callback = [$this, $method->getName()];
                    if ($action === WpAction::class) {
                        add_action($name, $callback, $priority, $accepted_args);
                    } else if ($action === WpFilter::class) {
                        add_filter($name, $callback, $priority, $accepted_args);
                    } else if ($action === WpAjax::class) {
                        add_action('wp_ajax_nopriv_' . $name, fn () => $callback(), $priority, $accepted_args);
                        add_action('wp_ajax_' . $name, fn () => $callback(), $priority, $accepted_args);
                    }
                }
            }
        }
    }
}
