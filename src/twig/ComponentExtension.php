<?php
/**
 * Twig Component Manager plugin for Craft CMS 5.x
 *
 * Advanced Twig component management with folder organization, prop validation, and slots
 *
 * @link      https://lindemannrock.com
 * @copyright Copyright (c) 2025 LindemannRock
 */

namespace lindemannrock\twigcomponentmanager\twig;

use lindemannrock\twigcomponentmanager\TwigComponentManager;
use lindemannrock\twigcomponentmanager\twig\ComponentTokenParser;
use lindemannrock\twigcomponentmanager\twig\SlotTokenParser;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Component Twig Extension
 *
 * @author    LindemannRock
 * @package   TwigComponentManager
 * @since     1.0.0
 */
class ComponentExtension extends AbstractExtension
{
    /**
     * @var TwigComponentManager
     */
    private TwigComponentManager $plugin;

    /**
     * Constructor
     *
     * @param TwigComponentManager $plugin
     */
    public function __construct(TwigComponentManager $plugin)
    {
        $this->plugin = $plugin;
    }

    /**
     * @inheritdoc
     */
    public function getName(): string
    {
        return 'TwigComponentManager';
    }

    /**
     * @inheritdoc
     */
    public function getTokenParsers(): array
    {
        return [
            new ComponentTokenParser($this->plugin),
            new SlotTokenParser(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('component', [$this, 'renderComponent'], ['is_safe' => ['html']]),
            new TwigFunction('c', [$this, 'renderComponent'], ['is_safe' => ['html']]),
            new TwigFunction('hasComponent', [$this, 'hasComponent']),
            new TwigFunction('componentProps', [$this, 'getComponentProps']),
            new TwigFunction('componentSlots', [$this, 'getComponentSlots']),
        ];
    }

    /**
     * Render a component
     *
     * @param string $name
     * @param array $props
     * @param string|null $content
     * @param array $slots
     * @return string
     */
    public function renderComponent(string $name, array $props = [], ?string $content = null, array $slots = []): string
    {
        return $this->plugin->components->render($name, $props, $content, $slots);
    }

    /**
     * Check if a component exists
     *
     * @param string $name
     * @return bool
     */
    public function hasComponent(string $name): bool
    {
        return $this->plugin->discovery->getComponent($name) !== null;
    }

    /**
     * Get component props definition
     *
     * @param string $name
     * @return array
     */
    public function getComponentProps(string $name): array
    {
        $component = $this->plugin->discovery->getComponent($name);
        return $component ? $component->props : [];
    }

    /**
     * Get component slots
     *
     * @param string $name
     * @return array
     */
    public function getComponentSlots(string $name): array
    {
        $component = $this->plugin->discovery->getComponent($name);
        return $component ? $component->slots : [];
    }
}