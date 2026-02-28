<?php

namespace Clarity\Core;

/**
 * ASSET MANAGER // SVG INJECTOR
 * Inlines local SVG assets for dynamic CSS recoloring.
 */
class Assets
{
    private const ASSET_PATH = __DIR__ . '/../../assets/images/';

    /**
     * Inlines an SVG from the assets/images directory.
     * Applies a default class for neon styling.
     */
    public static function getIcon(string $name, string $class = 'icon-neon-blue'): string
    {
        $file = self::ASSET_PATH . 'ICONS_' . strtoupper($name) . '.svg';
        if (!file_exists($file)) {
            return "<!-- Icon $name not found -->";
        }

        $svg = file_get_contents($file);
        
        // Strip XML and DOCTYPE for inlining
        $svg = preg_replace('/<\?xml.*\?>/i', '', $svg);
        $svg = preg_replace('/<!--.*-->/i', '', $svg);
        
        // Inject classes and handle sizing
        $svg = str_replace('<svg ', '<svg class="w-full h-full ' . $class . '" ', $svg);
        
        return $svg;
    }

    /**
     * Returns the CLARITY emblem SVG.
     */
    public static function getEmblem(string $class = 'text-ngn-blue'): string
    {
        $file = self::ASSET_PATH . 'LOGOSET1_EMBLEM.svg';
        if (!file_exists($file)) return '';
        
        $svg = file_get_contents($file);
        $svg = preg_replace('/<\?xml.*\?>/i', '', $svg);
        return str_replace('<svg ', '<svg class="' . $class . '" ', $svg);
    }

    /**
     * Returns the CLARITY horizontal logo SVG.
     */
    public static function getLogo(string $class = 'h-8'): string
    {
        $file = self::ASSET_PATH . 'LOGOSET1_LOGO-COMBO-HORIZONTAL.svg';
        if (!file_exists($file)) return '';
        
        $svg = file_get_contents($file);
        $svg = preg_replace('/<\?xml.*\?>/i', '', $svg);
        return str_replace('<svg ', '<svg class="' . $class . '" ', $svg);
    }

    /**
     * Returns a generic SVG from the assets/images directory.
     */
    public static function getSVG(string $filename, string $class = ''): string
    {
        $file = self::ASSET_PATH . $filename;
        if (!file_exists($file)) return "<!-- SVG $filename not found -->";
        
        $svg = file_get_contents($file);
        $svg = preg_replace('/<\?xml.*\?>/i', '', $svg);
        return str_replace('<svg ', '<svg class="' . $class . '" ', $svg);
    }
}
