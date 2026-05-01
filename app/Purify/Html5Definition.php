<?php

namespace App\Purify;

use HTMLPurifier_HTMLDefinition;
use Stevebauman\Purify\Definitions\Definition;

class Html5Definition implements Definition
{
    /**
     * Apply rules to the HTML Purifier definition.
     *
     * @param HTMLPurifier_HTMLDefinition $definition
     *
     * @return void
     */
    public static function apply(HTMLPurifier_HTMLDefinition $definition)
    {
        // First apply the default Stevebauman Html5Definition rules
        \Stevebauman\Purify\Definitions\Html5Definition::apply($definition);

        // Then add our custom attributes for the video element
        $definition->addAttribute('video', 'autoplay', 'Bool');
        $definition->addAttribute('video', 'muted', 'Bool');
        $definition->addAttribute('video', 'loop', 'Bool');
        $definition->addAttribute('video', 'playsinline', 'Bool');
    }
}
